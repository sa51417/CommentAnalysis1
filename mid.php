<?php
include_once 'simplehtmldom/simple_html_dom.php';

$input = escapeWord($_REQUEST['url']);

ini_set('memory_limit', '256M');
date_default_timezone_set('Asia/Taipei');

const INTERVALDAY = 7; //間隔日期
$today = getToday();
$IP = getIP();
$count = 0;
$content = [];
$imgurl = [];
$k = 0;
//echo $input . "<br/>";
//echo $_REQUEST['startDate'] . "<br/>";
//echo $_REQUEST['endDate'] . "<br/>";
//https://www.tripadvisor.com.tw/Hotel_Review-g13808671-d8263705-Reviews-Cityinn_Hotel_Plus_Fuxing_N_Rd_Branch-Zhongshan_District_Taipei.html
//https://www.tripadvisor.com.tw/Hotel_Review-g13808671-d1527776-Reviews-Capital_Hotel-Zhongshan_District_Taipei.html


$texts = preg_split('/Reviews/',$input);
$temp = file_get_html($input)->find('div.pageNumbers>a');
$pages = end($temp)->plaintext;
$temp2 = file_get_html($input)->find('h1.header');
$hotelname = preg_split('/\s/',end($temp2)->plaintext)[0];


include_once ('all.php');
//$srart_time = microtime(TRUE);
$UID = getUID();
if(checkUrl($input)) {
    updateUrlSearchTimes($input);
    $sqlComcmd = "DELETE IPInfo WHERE IPAddress='$IP' AND UID=" . $UID . " " .
        "INSERT INTO IPInfo (IPAddress,HotelName,UID) " .
        "VALUES ('$IP','$hotelname',".$UID.") ";
    executeSql($sqlComcmd);
}
else {
    $sqlComcmd = "";
    do {
        if (checkUrlInfo($input)) {
            $sqlComcmd = "DELETE Comment WHERE UID=" . $UID . " " .
                "UPDATE URLInfo SET FetchDate='$today' WHERE UID=" . $UID . " " .
                "DELETE IPInfo WHERE IPAddress='$IP' AND UID=" . $UID;
        }
        else {
            $sqlComcmd = "INSERT INTO URLInfo (UID,URL,FetchDate,SearchTimes) " .
                "VALUES (" .$UID .",'$input','$today',1) ";
        }
        
        $sqlComcmd = $sqlComcmd .
        "INSERT INTO IPInfo (IPAddress,HotelName,UID) " .
        "VALUES ('$IP','$hotelname',".$UID.") " .
        "INSERT INTO Comment (ComName,ComText,ComDate,ComPublish,ComTitle,ComProf,UID) VALUES ";
        $url_array = composeArray();
        $sqltag = "";
        asyncGeturl($url_array);
    } while($count < $pages);
    //$end_time = microtime(TRUE);
    //echo sprintf("use time:%.3f s", $end_time - $srart_time);
    getContentData($UID);
    $sqlComcmd .= " EXEC sp_anaScore @UID=".$UID;
    insertcommentSql($sqlComcmd);
    updateComImg($UID);
}
header("Location: comment.php?UID=" . $UID);
die();

//組合多執行緒網頁array
function composeArray() {
    global $count;
    global $pages;
    global $texts;
    $array = [];
    for($i=0 ; $i<10 and $count < $pages ; $i++) {
        if ($count == 0) {
            $array[$i] = $texts[0] . 'Reviews' . $texts[1];
        }
        else {
            $array[$i] = $texts[0] . 'Reviews-or' . $count*5 . $texts[1];
        }
        $count++;
    }
    return $array;
}

//檢查網址是否已有查詢資料且在期間內
function checkUrl($url) {
    global $today;
    $sqlcmd = "SELECT COUNT(*) AS REC " .
        "FROM URLInfo " .
        "WHERE URL='$url' AND DATEDIFF(DAY,FetchDate,'$today') < " . INTERVALDAY;
    $rtn = fetchDBData($sqlcmd);
    return $rtn[0]['REC'] > 0;
}

function checkUrlInfo($url) {
    global $today;
    $sqlcmd = "SELECT COUNT(*) AS REC " .
        "FROM URLInfo " .
        "WHERE URL='$url'";
    $rtn =fetchDBData($sqlcmd);
    return $rtn[0]['REC'] > 0;
}

//更新網頁查詢次數
function updateUrlSearchTimes($url) {
    $sqlcmd = "UPDATE URLInfo SET SearchTimes=SearchTimes + 1 WHERE URL='$url'";
    executeSql($sqlcmd);
}

//新增評論資料到SQL
function insertcommentSql($cmd) {
    if (!empty($cmd)) {
        executeSql($cmd);
    }
}

//組合評論資料SQL命令
function composeCommentSqlCmd($domdata,$UID) {
    global $sqlComcmd;
    global $sqltag;
    $element = $domdata->find('div.cPQsENeY>q>span:nth-child(1)');
    $imgCount = 0;
    for($j=0; $j < count($element) ; $j++) {
        $ComText = htmlentities($element[$j]->plaintext);
        $temp = $domdata->find('div.hotels-community-tab-common-Card__card--ihfZB a.ui_header_link',$j);
        $ComName = $temp == null ? '' : htmlentities($temp->innertext);
        $dateArr = $temp == null ? '' : preg_split('/，/',$temp->parent()->innertext);
        if (strpos($dateArr[1],'年') !== false and strpos($dateArr[1],'月') !== false) {
            $ComDate = str_replace('月','-',str_replace('年','-',$dateArr[1])) . '01';
        } else if (strpos($dateArr[1],'月') !== false and strpos($dateArr[1],'日') !== false) {
            $ComDate = '2019-' .  str_replace('月','-',str_replace('日','',$dateArr[1]));
        }
        else {
            $ComDate = '';
        }
        $temp2 = $domdata->find('div.hotels-community-tab-common-Card__card--ihfZB a span span',$j);
        $ComTitle = $temp2->innertext;
        $profile= "";
        
        if ($ComName != "TripAdvisor 會員") {
            $temp3 = $domdata->find('div.hotels-community-tab-common-Card__card--ihfZB a>img',$imgCount);
            if (isset($temp3)) {
                $profile = "https://www.tripadvisor.com.tw" . $temp3->parent()->href;
            }
            $imgCount++;
        }
        
        //$temp2 = $element[$j]->parent()->parent()->parent()->parent()->find('span.location-review-review-list-parts-EventDate__event_date--1epHa');
        //$RoomDate = count($temp2) > 0 ? str_replace('月','-',str_replace('年','-',str_replace(' ','',str_replace('住宿日期：','',$temp2[0]->innertext)))) . '01' : "1900-01-01";
        $sqlComcmd = $sqlComcmd . $sqltag . "(N'$ComName',N'$ComText','$ComDate',N'$dateArr[1]',N'$ComTitle',N'$profile'," . $UID .") ";
        $sqltag = ",";
    }
}

//非同步取得網頁資料
function asyncGeturl($url_array)
{
    global $content;
    global $k;
    global $imgurl;
    if (!is_array($url_array))
        return false;
        
        $mh = curl_multi_init();
        
        foreach($url_array as $url) {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 不打印
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 對認證證書來源的檢查從證書中檢查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //
            
            curl_multi_add_handle($mh, $ch); // 把 curl resource 放進 multi curl handler 裡
            
        }
        
        $active = null;
        do {
            while (($mrc = curl_multi_exec($mh, $active)) == CURLM_CALL_MULTI_PERFORM) ;
            
            if ($mrc != CURLM_OK) { break; }
            
            while ($done = curl_multi_info_read($mh)) {
                $imgurl[$k] = curl_getinfo($done['handle'], CURLINFO_EFFECTIVE_URL);
                $content[$k++] = curl_multi_getcontent($done['handle']);
                curl_multi_remove_handle($mh, $done['handle']);
                curl_close($done['handle']);
            }
            
            if ($active > 0) {
                curl_multi_select($mh);
            }
            
        } while ($active);
        
        curl_multi_close($mh);
}

//取出陣列dom資料
function getContentData($UID) {
    global $content;
    foreach ($content as $c) {
        $dom = new simple_html_dom();
        $dom->load($c);
        composeCommentSqlCmd($dom,$UID);
    }
}

//取得瀏覽者IP
function getIP(){
    if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif(!empty($_SERVER["REMOTE_ADDR"])){
        $cip = $_SERVER["REMOTE_ADDR"];
    }
    else{
        $cip = "無法取得IP位址！";
    }
    return $cip;
}

function getToday(){
    $today = getdate();
    date("Y/m/d H:i");  //日期格式化
    $year=$today["year"]; //年
    $month=$today["mon"]; //月
    $day=$today["mday"];  //日
    
    if(strlen($month)=='1')$month='0'.$month;
    if(strlen($day)=='1')$day='0'.$day;
    $today=$year."-".$month."-".$day;
    //echo "今天日期 : ".$today;
    
    return $today;
}

function getUID() {
    global $input;
    
    $cmd = "SELECT ISNULL(dbo.fn_getUID('$input'),1) as UID";
    $result = fetchDBData($cmd);
    return  $result[0]['UID'];
}

function escapeWord($str)
{
    if(get_magic_quotes_gpc())
    {
        $str= stripslashes($str);
    }
    
    return str_replace(["'",'"'], ["''",'\"'], $str);
}

function updateComImg($UID) {
    global $content;
    global $k;
    global $imgurl;
    
    while (getImgCount($UID)) {
        $sql = "SELECT TOP 50 ComProf " .
            "FROM Comment " .
            "WHERE ComProf<>'' AND ComImg='' AND UID=" . $UID . " " .
            "GROUP BY ComProf";
        $result = fetchDBData($sql);
        
        $count = 0;
        $content = [];
        $imgurl = [];
        $k = 0;
        do {
            $ProfArr = [];
            for($i=0 ; $i<10 and $count < count($result) ; $i++) {
                $ProfArr[$i] = $result[$count]['ComProf'];
                $count++;
            }
            asyncGeturl($ProfArr);
        } while($count < count($result));
        
        $sqlcmd = "";
        for ($i = 0; $i < count($content); $i++) {
            $dom = new simple_html_dom();
            $dom->load($content[$i]);
            
            $e2 = $dom->find('div.ui_container div span img');
            if (isset($e2)) {
                $ComImg = $e2[0]->src;
                $sqlcmd .= "UPDATE Comment SET ComImg=N'$ComImg' WHERE ComProf='$imgurl[$i]' ";
            }
            else {
                $sqlcmd .= "UPDATE Comment SET ComImg=N'images/error.jgp' WHERE ComProf='$imgurl[$i]' ";
            }
        }
        executeSql($sqlcmd);
    }
}

function getImgCount($UID) {
    $sql = "SELECT COUNT(*) AS REC " .
        "FROM Comment " .
        "WHERE ComProf<>'' AND ComImg='' AND UID=" . $UID;
    $result = fetchDBData($sql);
    return $result[0]['REC'];
}
?>

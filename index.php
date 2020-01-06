<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Travelute</title>
</head>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
<link type="text/css" rel="stylesheet" href="css/index.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<body>
<form id="main" method="post" action="mid.php">
    <div class="wrap">
      <div class="main-image"></div>
      <div class="content">
        <div class="content-wrap">
              <div class="content-wrap-bg"></div>
              <h5>輸入飯店網址</h5>
              <div class="search-area">
                <div class="search-area-icon"></div>
                <input type="text" id="url" name="url" placeholder="輸入飯店網址">
                <div class="search-area-cursor"></div>
              </div>
              <h5>選擇評價日期</h5>
              <div class="pikaday-area">
                <div class="pikaday-area-icon "><i class="fas fa-calendar-alt"></i></div>
                <input type="text" id="datepicker-start" placeholder="請選擇起始日期">
                <div class="pikaday-area-dayToDay">-</div>
                <input type="text" id="datepicker-end" placeholder="請選擇結束日期">
              </div>
              <div class="list-area">
        	    <div class="row d-flex justify-content-between">
        	      <?php 
            	      include_once ('all.php');
            	      $sqlcmd = "SELECT TOP 5 URL,HotelName " .
                    	        "FROM IPInfo I " .
                    	        "INNER JOIN URLInfo U ON I.UID = U.UID " .
                    	        "WHERE IPAddress=? " .
            	                "ORDER BY IID DESC";
            	      
            	      $data = fetchDBData($sqlcmd,[0=>getIP()]);
            	      for($i=0 ; $i < 5 ; $i++) {
            	          if ($i < count($data)) {
                	          echo "<div class='list-item col-6 col-sm-4 col-md-3 col-lg-2'>" .
                        	          "<div class='list-item-img'>" .
                        	               '<img src="images/hotel' . ($i + 1) .'.png" alt="">' .
                        	          "</div>" .
                        	          "<a style='display:none;' href=" . $data[$i]['URL'] . "></a>" .
                        	          "<div class='list-item-title'>". $data[$i]['HotelName'] ."</div>" .
                    	          "</div>";
            	          }
            	          else {
            	              echo "<div class='list-item col-6 col-sm-4 col-md-3 col-lg-2'>" .
                	              "<div class='list-item-img'>" .
                	              "<img src='' alt=''>" .
                	              "</div>" .
                	              "<div class='list-item-title'></div>" .
                	              "</div>";
            	          }
            	      }
        	      ?>
        	      </div> 
        	  </div>
    	</div> 	  
	  </div>
	</div>
</form>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
	    <div class="modal-content">
	        <div class="modal-header">
		        <h5 class="modal-title">錯誤訊息</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
	        </div>
	        <div class="modal-body">
	        	<p>查詢網址列不可以為空白</p>
	        </div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
	        </div>
	    </div>
	</div>
</div>
<div id="divProgress" style="text-align:center; display: none; position: fixed; top: 50%;  left: 50%;" >
    <img id="imgLoading" src="images/loading.gif" />
    <br />
    <font color="#1B3563" size="6px">資料處理中</font>
</div>
<div id="divMaskFrame" style="background-color: #F2F4F7; display: none; left: 0px;
    position: absolute; top: 0px;">
</div>
</body>
</html>
<?php 
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
?>

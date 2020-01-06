<?php
    include 'all.php';
    $jsonStr = $_REQUEST['formData'];
    $phpObject = json_decode($jsonStr);

    $score = 0;
    switch($phpObject->Level) {
        case "優":
            $score = 9;
            break;
        case "中":
            $score = 5;
            break;
        case "劣":
            $score = 1;
            break;
    }
    
    
    $sql = "UPDATE ScoreInfo SET Score=" . $score . " WHERE UID=" . $phpObject->UID . " AND CID=" . $phpObject->CID . " AND categroyId=" . $phpObject->CategroyId;
    
    return executeSql($sql);

?>
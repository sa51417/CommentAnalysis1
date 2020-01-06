<?php
    include 'all.php';
    $jsonStr = $_REQUEST['formData'];
    $phpObject = json_decode($jsonStr);

    /*
    $sql = "SELECT TOP 5 ComName,ComText,ComTitle,ComPublish,ComImg " .
           "FROM Comment " .
           "WHERE UID=" . $phpObject->UID . " " .
           "ORDER BY CID";
    */
    $sql = "EXEC sp_Comment_get_LIST @XPAGE=" . $phpObject->NPAGE . ",@MAX_ROWS=5,@UID=" . $phpObject->UID .
    ",@CategroyId=" . $phpObject->CategroyId . ",@Score=" . $phpObject->Score . ",@keyword='" . $phpObject->Keyword ."'";
    if ($rows = fetchDBData($sql)){
        echo json_encode($rows, JSON_UNESCAPED_UNICODE );
    }
    else {
        echo '[]';
    }

?>
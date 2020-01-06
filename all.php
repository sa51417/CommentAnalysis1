<?php
function getConnection(){
    global $pdo;
    
    if(!isset($pdo)){
        $obj = json_decode( file_get_contents('dbConfig.json') );
        $dsn = $obj->dsn;
        $username = $obj->username;
        $password = $obj->password;
        try{
            $pdo=new PDO($dsn,$username,$password);
            return $pdo;
        }
        catch(PDOException $e){
            return false;
        }
    }
    else
        return $pdo;
}

function fetchDBData($sql, $params=[]){
    
    if( $pdo=getConnection()){
        $stmt=$pdo->prepare($sql);
        if($stmt->execute($params))
            return $stmt->fetchAll();
            else{
                return false;
            }
    }
    else
        return false;
}

function executeSql($sql, $params=[]){
    
    if( $pdo=getConnection()){
        $stmt=$pdo->prepare($sql);
        return $stmt->execute($params);
    }
    else
        return false;
}

?>
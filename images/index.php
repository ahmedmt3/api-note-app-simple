<?php
include "../connect.php";

try{

    $sql = "SELECT * FROM images";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    if($stmt->rowCount() == 0){
        echo json_encode([]);
    }else {
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($images);
    }
    
    
} catch (PDOException $e){
    echo $e->getMessage();
}
<?php
include "../connect.php";


try{
    
    $stmt = $con->prepare("SELECT * FROM notes ORDER BY last_modified DESC");
    // sleep(1);
    $stmt->execute();

    if($stmt->rowCount() == 0){
        echo json_encode([]);
    }else{
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($notes);
    }

    

}catch(PDOException $e){
    echo $e->getMessage();
}


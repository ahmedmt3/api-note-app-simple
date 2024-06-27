<?php
include "../connect.php";
include "../func.php";

$id = isset($_POST['id']) ? $_POST['id'] : null;
$imageName = isset($_POST['image_name']) ? $_POST['image_name'] : null;

$msg = '';
$response = [];

if($id){
    try{
        // Begin Transacion
        $con->beginTransaction();

        //Delete the  Note
        $stmtNote = $con->prepare("DELETE FROM notes WHERE id = ?");
        $stmtNote->execute([$id]);

        //Delete the Note's Image
        $stmtImage = $con->prepare("DELETE FROM images WHERE note_id = ?");
        $stmtImage->execute([$id]);

        if($imageName){
            $result = deleteFile('../images', $imageName);
            if($result !== 'success'){
                $msg = $result;
                $response = ["message" => $msg];
                echo json_encode($response);
                $con->rollBack();
                exit;
            }
        }

        if($stmtNote->rowCount() > 0){
            $con->commit(); // Commit the Transaction
            $msg = "success";

        }else{
            $con->rollBack(); //RollBack the Transaction
            $msg = "failed to delete";
        }

    }catch(PDOException $e){
        $con->rollBack();
        $msg = $e->getMessage();
    }

}else{
    $msg = "No id sent";
}

$response = ["message" => $msg];
echo json_encode($response);

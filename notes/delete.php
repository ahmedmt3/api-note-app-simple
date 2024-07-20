<?php
include "../connect.php";
include "../func.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = isset($_POST['id']) ? $_POST['id'] : null;
$imageName = isset($_POST['image_name']) ? $_POST['image_name'] : null;

$status = 'failed';
$msg = '';
$response = ['status' => $status, 'message' => $msg];

if($id){
    try{
        // Begin Transacion
        $con->beginTransaction();

        $noteDeleted = deleteData('notes', 'id', $id);
        $imgDeleted = deleteData('images', 'note_id', $id);

        if($noteDeleted){
            // Delete The Image If Exist
            if($imageName){
                $result = deleteFile('../images/uploads/', $imageName);
                if($result !== 'success'){
                    $con->rollBack();
                    $msg = $result;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit;
                }
            }
            $con->commit(); // Commit the Transaction
            $status = "success";
            $msg = "Note Deleted Successfully";

        }else{
            $con->rollBack(); //RollBack the Transaction
            $msg = "Failed to delete note";
        }

    }catch(PDOException $e){
        $con->rollBack();
        $msg = $e->getMessage();
    }

}else{
    $msg = "No id sent";
}
$response = ['status' => $status, 'message' => $msg];
echo json_encode($response);

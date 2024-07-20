<?php
include "../connect.php";
include "../func.php";

// Note Table
$title = isset($_POST['title']) ? $_POST['title'] : null;
$content = isset($_POST['content']) ? $_POST['content'] : null;
$color = isset($_POST['color']) ? $_POST['color'] : "DEFAULT";  // Optional
// Images table
$image = isset($_FILES['image']) ? $_FILES['image'] : null;
$imagePosX = isset($_POST['image_pos_x']) ? $_POST['image_pos_x'] : "DEFAULT";
$imagePosY = isset($_POST['image_pos_y']) ? $_POST['image_pos_y'] : "DEFAULT";

$msg = '';
$response = [];

if($title && $content){

    //Color validation
    if($color !== "DEFAULT"){
        if(!isValidHexColor($color)){
            $msg = "Invalid hex color format";
            $response = ['message' => $msg];
            echo json_encode($response);
            exit;
        }
    }
    //Format the positions
    if($imagePosX !== "DEFAULT"){
        $imagePosX = number_format(floatval($imagePosX), 3, '.', '');
    }
    if($imagePosY !== "DEFAULT"){
        $imagePosY = number_format(floatval($imagePosY), 3, '.', '');
    }
    //===========================[ Note Insertion ]=======================
    try{
        $noteData = [
            'title' => $title,
            'content' => $content,
            'color' => $color
        ];
        $result = insertData('notes', $noteData);

        if($result){
            //=======================[ Image Insertion ]========================
            $noteId = $con->lastInsertId();
            if($image !== null){
                $uploadRes = imageUpload($image);
                if($uploadRes === 'success'){
                    global $uploadedImgName;
                    $imgData = [
                        'note_id' => $noteId,
                        'image_name' => $uploadedImgName,
                        'image_pos_x' => $imagePosX,
                        'image_pos_y' => $imagePosY
                    ];
                    $imgRes = insertData('images', $imgData);
                    // Checking image insertion
                    if($imgRes <= 0){
                        $msg = "Image insertion failed";
                        $response = ['message' => $msg];
                        echo json_encode($response);
                        exit;
                    }
                }else{  //When upload fails
                    global $errMsg;
                    $msg = implode(' and ', $errMsg);
                    $response = ['message' => $msg];
                    echo json_encode($response);
                    exit;
                }
            }
            $msg = "success";
    
        }else{
            $msg = "Sql failed";
        }
    }catch(PDOException $e){
        $msg = 'SQL: ' . $e->getMessage();
    }
}else{
    $msg = "Try to add title & content";
}

$response = ["message" => $msg];
echo json_encode($response);




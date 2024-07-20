<?php
include "../connect.php";
include "../func.php";


// Notes Tabl
$id = isset($_POST['id']) ? $_POST['id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$content = isset($_POST['content']) ? $_POST['content'] : null;
$color = isset($_POST['color']) ? $_POST['color'] : null;
// Image Table
$image = isset($_FILES['image']) ? $_FILES['image'] : null;
$oldImage = isset($_FILES['old_image']) ? $_FILES['old_image'] : null;
$imagePosX = isset($_POST['image_pos_x']) ? $_POST['image_pos_x'] : "DEFAULT";
$imagePosY = isset($_POST['image_pos_y']) ? $_POST['image_pos_y'] : "DEFAULT";

$status = 'failed';
$msg = '';
$response = ['status' => $status, 'message' => $msg];


if ($id && $title && $content) {
    //Format the positions
    if($imagePosX !== "DEFAULT"){
        $imagePosX = number_format(floatval($imagePosX), 3, '.', '');
    }
    if($imagePosY !== "DEFAULT"){
        $imagePosY = number_format(floatval($imagePosY), 3, '.', '');
    }
    try {
        $lastMod = new DateTime();
        date_add($lastMod, date_interval_create_from_date_string("1 Hours"));//Summer Time
        $lastMod = $lastMod->format('Y-m-d H:i:s');

        $noteDataWithColor = [
            'title' => $title,
            'content' => $content,
            'color' => $color,
            'last_modified' => $lastMod
        ];
        $noteDataWithoutColor = [
            'title' => $title,
            'content' => $content,
            'last_modified' => $lastMod
        ];
        $noteData = $color == null ? $noteDataWithoutColor : $noteDataWithColor;
        $msg = updateData('notes', $noteData, 'id', $id);

        // If image sent to update the old one
        if($oldImage !== null){

            $uploadRes = imageUpload($image);
            if($uploadRes === 'success'){
                deleteFile('../images/uploads/', $oldImage);// Delete the Old One
                global $uploadedImgName;
                $imgData = [
                    'image_name' => $uploadedImgName,
                    'image_pos_x' => $imagePosX,
                    'image_pos_y' => $imagePosY
                ];
                //==================[ Result Message ]===================
                $msg = updateData('images', $imgData, 'note_id', $id);

            }else{
                global $errMsg;
                $msg = implode(' and ', $errMsg);
                $response['message'] = $msg;
                echo json_encode($response);
                exit;
            }

        }elseif($image !== null){// If It's New Image
            
            $uploadRes = imageUpload($image);
            if($uploadRes === 'success'){
                global $uploadedImgName;
                $imgData = [
                    'note_id' => $id,
                    'image_name' => $uploadedImgName,
                    'image_pos_x' => $imagePosX,
                    'image_pos_y' => $imagePosY
                ];
                $imgInsert = insertData('images', $imgData);
                //==================[ Result Message ]===================
                $msg = $imgInsert ? "success" : "Image Insert Failed";

            }else{
                global $errMsg;
                $msg = implode(' and ', $errMsg);
                $response['message'] = $msg;
                echo json_encode($response);
                exit;
            }

        }
        //==================[ Result Message ]===================
        $status = 'success';
        
    } catch (PDOException $e) {
        $msg = "PDO: " . $e->getMessage();
    }
} else {
    $msg = "Please provide id, title and content";
}

$response = ['status' => $status, 'message' => $msg];
echo json_encode($response);

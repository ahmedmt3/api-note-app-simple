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
$oldImage = isset($_POST['old_image']) ? $_POST['old_image'] : null;
$imagePosX = isset($_POST['image_pos_x']) ? $_POST['image_pos_x'] : null;
$imagePosY = isset($_POST['image_pos_y']) ? $_POST['image_pos_y'] : null;


$msg = '';
$response = [];

if ($id) {
    try {
        $fields = [];
        $params = [];

        if ($title !== null) {
            $fields[] = "`title` = ?";
            $params[] = $title;
        }
        if ($content !== null) {
            $fields[] = "`content` = ?";
            $params[] = $content;
        }
        if ($color !== null) {
            $fields[] = "`color` = ?";
            $params[] = $color;
        }

        if (count($fields) > 0) {
            $fields[] = "`last_modified` = DEFAULT";
            $params[] = $id;
            $sql = "UPDATE `notes` SET " . implode(", ", $fields) . " WHERE `id` = ?";
            $stmt = $con->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {

                // If image sent and positions
                if($image !== null){
                    $result = imageUpload($image);
                    if($result === 'success'){
                        deleteFile('../images/uploads', $oldImage);// Delete the Old One
                        global $uploadedImgName;
                        $posXval = "DEFAULT";
                        $posYval = "DEFAULT";
                        $imgParams = [$uploadedImgName];

                        if($imagePosX !== null){
                            $posXval = "?";
                            $imagePosX = number_format(floatval($imagePosX), 3, '.', '');
                            $imgParams[] = $imagePosX;
                        }
                        if($imagePosY !== null){
                            $posYval = "?";
                            $imagePosY = number_format(floatval($imagePosY), 3, '.', '');
                            $imgParams[] = $imagePosY;
                        }
                        $imgParams[] = $id;

                        $sqlImage = "UPDATE `images` SET `image_name` = ?, `image_pos_x` = $posXval, `image_pos_y` = $posYval
                        WHERE `note_id` = ?";
                        $stmtImage = $con->prepare($sqlImage);
                        $stmtImage->execute($imgParams);
                        // Done
                        $msg = "success";

                    }else{
                        global $errMsg;
                        $msg = implode(' and ', $errMsg);
                        $response = ['message' => $msg];
                        echo json_encode($response);
                        exit;
                    }
                }elseif($imagePosX !== null || $imagePosY !== null){
                    // If There's Not New Image Sent, Check for Positins
                    
                    $imgParams = [];
                    $sqlImage = "";
                    if ($imagePosX !== null) {
                        $imagePosX = number_format(floatval($imagePosX), 3, '.', '');
                        $imgParams[] = $imagePosX;
                        $sqlImage = "UPDATE `images` SET `image_pos_x` = ?";
                    }
                    if ($imagePosY !== null) {
                        $imagePosY = number_format(floatval($imagePosY), 3, '.', '');
                        $imgParams[] = $imagePosY;
                        $sqlImage = $sqlImage . ", `image_pos_y` = ?";
                    }

                    $imgParams[] = $id;

                    $sqlImage = $sqlImage . "WHERE `note_id` = ?";
                    $stmtImage = $con->prepare($sqlImage);
                    $stmtImage->execute($imgParams);

                    
                    $msg = "success";
                    
                }
                
            } else {// whene no Row affected
                $msg = "fail";
            }
        } else {// when no Title & Content
            $msg = "No fields to update";
        }
    } catch (PDOException $e) {
        $msg = "SQL: " . $e->getMessage();
    }
} else {
    $msg = "No ID provided";
}

$response = ["message" => $msg];
echo json_encode($response);

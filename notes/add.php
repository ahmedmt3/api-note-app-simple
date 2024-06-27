<?php
include "../connect.php";
include "../func.php";

// Note Table
$title = isset($_POST['title']) ? $_POST['title'] : null;
$content = isset($_POST['content']) ? $_POST['content'] : null;
$color = isset($_POST['color']) ? $_POST['color'] : null;  // Optional
// Images table
$image = isset($_FILES['image']) ? $_FILES['image'] : null;
$imagePosX = isset($_POST['image_pos_x']) ? $_POST['image_pos_x'] : null;
$imagePosY = isset($_POST['image_pos_y']) ? $_POST['image_pos_y'] : null;

$msg = '';
$response = [];

if($title && $content){
    
    try{
        //===========================[ Note Insertion ]=======================

        $columns = ['title', 'content', 'color'];
        $values = ["?", "?"];
        $params = [$title, $content];

        if($color == null){
            $values[] = "DEFAULT"; 

        }elseif(isValidHexColor($color)){
            $values[] = "?";
            $params[] = $color;

        }else{
            $msg = "Invalid hex color format";
            $response = ['message' => $msg];
            echo json_encode($response);
            exit;
        }

        $sql = "INSERT INTO `notes`(" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        $stmt = $con->prepare($sql);
        $stmt->execute($params);
    
        if($stmt->rowCount() > 0){
            $noteId = $con->lastInsertId();

            //=======================[ Image Insertion ]========================
            if($image !== null){
                $result = imageUpload($image);
                if($result === 'success'){
                    global $uploadedImgName;
                    $imgVals = ['?', '?', 'DEFAULT', 'DEFAULT'];
                    $imgParams = [$noteId, $uploadedImgName];

                    if($imagePosX !== null){
                        $imgVals[2] = '?';
                        $imgParams[] = number_format(floatval($imagePosX), 3, '.', '');
                    }
                    if($imagePosY !== null){
                        $imgVals[3] = '?';
                        $imgParams[] = number_format(floatval($imagePosY), 3, '.', '');
                    }
                    
                    $sqlImage = "INSERT INTO `images` (`note_id`, `image_name`, `image_pos_x`, `image_pos_y`)
                    VALUES (" . implode(', ', $imgVals) . ")";
                    $stmtImage = $con->prepare($sqlImage);
                    $stmtImage->execute($imgParams);
                    // Checking image insertion
                    if($stmtImage->rowCount() <= 0){
                        $msg = "Image insert failed: " . implode(", ", $stmtImage->errorInfo());
                        $response = ['message' => $msg];
                        echo json_encode($response);
                        exit;
                    }

                }else{
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




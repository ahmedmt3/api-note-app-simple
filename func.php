<?php

define('bytesToMB', 1048576);//Const convert bytes to MB


function filterRequest($requestName){
    return htmlspecialchars(strip_tags($_POST[$requestName]));
}

//Convert iso8601 to mySQL dateTime format
function toMySQLDateTime($dateTimeString) {
    $date = new DateTime($dateTimeString);
    return $date->format('Y-m-d H:i:s');
}

// To validate hex color
function isValidHexColor($color) {
    return preg_match('/^#[0-9A-Fa-f]{6}$/', $color) === 1;
}

// =======================[ Image Upload ]========================

function imageUpload($imageRequest){
    global $errMsg;
    global $uploadedImgName;

    $imageName  = rand(1, 1000) . $imageRequest['name'];
    $imageTemp  = $imageRequest['tmp_name'];
    $imageSize  = $imageRequest['size'];

    $allowedExt   = ['jpg', 'png', 'gif'];
    $imgNameToArr = explode('.', $imageName);
    $ext = end($imgNameToArr);
    $ext = strtolower($ext);

    if(!empty($imageName) && !in_array($ext, $allowedExt)){
        $errMsg[] = "Invalid image extension";
    }
    if($imageSize > 10 * bytesToMB){
        $errMsg[] = "Image Size over 10 MB";
    }

    if(empty($errMsg)){
        $targetDir = "../images/uploads/";
        // Ensure the target directory exists
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $errMsg[] = "Failed to create directory: " . $targetDir;
                return $errMsg;
            }
        }
        $targetFilePath = $targetDir . $imageName;
        if (move_uploaded_file($imageTemp, $targetFilePath)) {
            $uploadedImgName = $imageName;
            return "success";
        } else {
            return "Failed to move the uploaded file.";
        }
    }else{
        return $errMsg;
    }
}

//====================[ Delete File ]=====================

function deleteFile($dir, $fileName){
    $path = $dir . '/' . $fileName;
    if(file_exists($path)){
        if(unlink($path)){
            return "success";
        }else{
            return "Unable to delete the file";
        }
    }else{
        return "File does not exist";
    }
}
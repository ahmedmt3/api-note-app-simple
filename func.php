<?php

//Const converts bytes to MB
define('bytesToMB', 1048576);

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

// Print Json Error
function errorResponse($msg = []){
    $response = ["status" => 'failed', "data" => $msg];
    return json_encode($response);
}
// ===================[ Checks If Row Exist ]===================
function checkRowExist(string $table, array $columns, array $values): bool|string{
    global $con;
    // Query
    $sql = "SELECT * FROM $table WHERE " . implode(' = ? OR ', $columns) . " = ?";

    try{
        $stmt = $con->prepare($sql);
        $stmt->execute($values);
        $count = $stmt->rowCount();

        if($count > 0){
            return true;
        }else{
            return false;
        }
    }catch (PDOException $e){
        return "Error: " . $e->getMessage();
    }


}

// ===============================================================
// =======================[ Get All Data ]========================
// ===============================================================

function getAllData(string $table, string $orderBy = null, bool $desc = false):int{
    global $con;
    $status = "failed";
    $data = [];
    $response = ["status" => $status, "data" => $data];
    // Query
    $sql = "SELECT * FROM `$table`";
    if($orderBy !== null){
        $sql .= " ORDER BY `$orderBy`";
        if($desc){
            $sql .= " DESC";
        }
    }
    
    try{
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count  = $stmt->rowCount();

        $status = "success";
        $response['status'] = $status;
        $response['data'] = $data;
        echo json_encode($response);
        return $count;

    } catch (PDOException $e){
        $response['data'] = $e->getMessage();
        echo json_encode($response);
    }
}
// ==============================================================
// =======================[ Insert Data ]========================
// ==============================================================

function insertData(string $table, $data){
    global $con;
    $status = "failed";
    $data = [];
    $response = ["status" => $status, "data" => $data];
    // Data Placeholders & Fields
    foreach ($data as $field => $v)
        $ins[] = ':' . $field; 
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    //Query
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";
    
    $stmt = $con->prepare($sql);

    // Binding values to placeholders
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo json_encode(array("status" => "success"));
    } else {
        echo json_encode(array("status" => "failure"));
    }
  
    return $count;
    
}
// ===============================================================
// =======================[ Image Upload ]========================
// ===============================================================

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
// =======================================================
//====================[ Delete File ]=====================
// =======================================================

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
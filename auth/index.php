<?php
include "../connect.php";
include "../func.php";

$status = "failed";
$data = [];
$response = ["status" => $status, "data" => $data];

try{
    $data = getAllData('users');
    if($data){
        $status = 'success';
        $response = ['status' => $status, 'data' => $data];
    }

}catch(PDOException $e){
    $data = $e->getMessage();
    $response['data'] = $data;
}
echo json_encode($response);
<?php
include "../connect.php";
include "../func.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$status = 'failed';
$msg = '';
$response = ['status' => $status, 'message' => $msg];

if($username && $password){
    //Checking if User Exist
    $userExist = checkRowExist('users', ['username', 'password'], [$username, $password]);
    if ($userExist) {
        $status = 'success';
        $msg = 'Logged In Successfully';
        $response = ['status' => $status, 'message' => $msg];
        
    }else{
        $msg = 'Invalid Username Or Password';
        $response['message'] = $msg;
    }

}else{
    $msg = 'Please provide username and password';
    $response['message'] = $msg; 
}
echo json_encode($response);
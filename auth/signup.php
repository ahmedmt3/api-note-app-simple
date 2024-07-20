<?php
include "../connect.php";
include "../func.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = isset($_POST['username']) ? $_POST['username'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$status = 'failed';
$msg = '';
$response = ['status' => $status, 'message' => $msg];

if($username && $email && $password){

    //Check If User Exist
    $userExist = checkRowExist('users', ['username'], [$username]);
    if(!$userExist){
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => sha1($password)
        ];
        try{
            $userAdded = insertData('users', $data);
            if($userAdded){
                $status = "success";
                $msg = "User Added Successfully";
                $response = ['status' => $status, 'message' => $msg];

            }else{
                $msg = 'Failed to signup';
                $response['message'] = $msg;
            }

        }catch(PDOException $e){
            $msg = $e->getMessage();
            $response['message'] = $msg;
        }
    }else{
        $msg = 'User Already Exist, Try change username';
        $response['message'] = $msg;
    }

}else{
    $msg = 'Please provide username, email and password';
    $response['message'] = $msg; 
}
echo json_encode($response);
<?php

include "connect.php";
include "func.php";


// $sql = "INSERT INTO `notes` (title, content) VALUES ('Hellloo', 'NewContent')";

// date_default_timezone_set('Africa/Cairo');

// echo 'Current PHP default timezone: ' . date_default_timezone_get() . "\n";
// echo 'Current date and time: ' . date('Y-m-d H:i:s') . "\n";

try{
    
    $user =  getUser('ahmed', 'admin');
    echo json_encode($user);

}catch(PDOException $e){
    echo $e->getMessage();
}
<?php

$dsn = "mysql:host=localhost;dbname=notes_app";
$user = "root";
$pass = "";
$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4");

try {
    $con = new PDO($dsn, $user, $pass, $option);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
    header("Access-Control-Allow-Methods: POST, OPTIONS, GET");

    checkAuthenticate();

} catch (PDOException $e) {
    echo $e->getMessage();
}


function checkAuthenticate(){
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {

        if ($_SERVER['PHP_AUTH_USER'] != "ahmed" ||  $_SERVER['PHP_AUTH_PW'] != "amt123"){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }
}
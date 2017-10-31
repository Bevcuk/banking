<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';

session_start();
$config = new Config();

$repository = new UserRepository($config->DataBaseHandler);
$auth = new Auth($repository, "user_id", "user_hash");

if (!$auth->isAuth() && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$auth->auth(filter_input(INPUT_POST, "email"), filter_input(INPUT_POST, "password"))) {
        echo json_encode(array("success" => false, "errors" => array("Email або пароль був введений не правильний")));        
        exit();
    }
}

echo json_encode(array("success" => true));



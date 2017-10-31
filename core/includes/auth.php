<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/http.php';

session_start();

$config = new Config();
$repository = new UserRepository($config->DataBaseHandler);
$auth = new Auth($repository, "user_id", "user_hash");
$http = new HttpHelper();

if (!$auth->isAuth()) {
    $http->redirect('/');
}
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/account_repository.php';

if($auth->isClient() == true) {
    $http->redirect('/account/');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $repository = new AccountRepository($config->DataBaseHandler);
    $id = $http->getIntValue("id", INPUT_POST);
    $reason = $http->getValue("reason", INPUT_POST);
    $history = $repository->cancelRequest($id,$reason);
}  
?>

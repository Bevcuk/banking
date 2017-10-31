<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/account_repository.php';

if($auth->isClient() == false) {
    $http->redirect('/payment/');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $repository = new AccountRepository($config->DataBaseHandler);
    $errors = array();
    
    $card = $http->getFloatValue("card", INPUT_POST);
    $summa = $http->getFloatValue("summa", INPUT_POST);
    $comment = $http->getValue("comment", INPUT_POST);
    $user_id = $_SESSION["user_id"];
    
    if ($summa == null || $summa <= 0) {
        $errors[] = "Сума повинна бути більшою 0";
    }
    
    if ($comment == null || $comment == '') {
        $errors[] = "Ви повинні вказати призначення платежу";
    }
    
    if ($repository->isEnoughMoney($user_id, $summa) == false) {
        $errors[] = "На Вашому рахунку недостатньо коштів для здійнення даної операції";
    }
    
    if ($repository->isExists($user_id, $card) == false) {
        $errors[] = "№ картки отримувача не є вірним або отримувач не є клієнтом нашого банку";
    }
    
    if (count($errors) == 0) {
        echo $repository->transferRequest($user_id, $card, $summa, $comment);
    }

    echo json_encode(array("success" => count($errors) == 0, "errors" => $errors));
}  
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/account_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/deposit_repository.php';

if($auth->isClient() == false) {
    $http->redirect('/payment/');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deposit_repository = new DepositRepository($config->DataBaseHandler);
    $account_repository = new AccountRepository($config->DataBaseHandler);
    $errors = array();
    
    $summa = $http->getFloatValue("summa", INPUT_POST);
    $deposit_id = $http->getIntValue("deposit_id", INPUT_POST);
    $user_id = $_SESSION["user_id"];
    
    if ($summa != null && $summa > 0) {
        if ($account_repository->isEnoughMoney($user_id, $summa) == true) {
            $deposit_repository->addMoney($user_id, $deposit_id, $summa);
        } else {
            $errors[] = "На Вашому рахунку недостатньо коштів для здійнення даної операції";
        }
    } else {
        $errors[] = "Сума повинна бути більшою 0";
    }
    

    echo json_encode(array("success" => count($errors) == 0, "errors" => $errors));
}  
?>

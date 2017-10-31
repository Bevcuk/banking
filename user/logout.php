<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';

if ($auth->isAuth()) {
    $_SESSION["user_id"] = null;
    $_SESSION["user_hash"] = null;
    $http->redirect("/");
}
?>

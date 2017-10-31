<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/http.php';

$config = new Config();
$repository = new UserRepository($config->DataBaseHandler);
$auth = new Auth($repository, "user_id", "user_hash");

if ($auth->isAuth()) {
    $http = new HttpHelper();
    $http->redirect('/account/');
}
?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Banking</title>
    <script src="/content/js/script.js" type="text/javascript"></script>
    <script src="/content/js/jquery.js" type="text/javascript"></script>
    <script src="/content/js/jquery-ui.js" type="text/javascript"></script>
    <script src="content/js/script.js" type="text/javascript"></script>
    
    <link href="/content/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/grid.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/admin.css" rel="stylesheet" type="text/css" />
</head>
    <body>
        <script type="text/javascript">
            $(function(){
                var form = new EditForm("#login-form", "/user/login.php");
                form.success = function () {
                   window.location.href = '/account/'; 
                };
                form.bindSubmit();
            });
        </script>
        <div class="row top">
            <div class="col-12"></div>
        </div>
        <div class="row">
            <div class="col-3 filter" style="margin: 120px auto; float: none;">
                <div class="title">
                    <span>Авторизація</span>
                </div>
                <form id="login-form" method="post" action="/user/login.php" class="edit-form">
                    <div style="border: 1px #474644 solid;">
                        <div class="row error">
                            <i class="fa fa-warning fa-lg"></i> Під час обробки вашого запиту сталася помилка.
                        </div>
                        <div class="row validation">
                            <ul>
                            </ul>				
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-3">Email:</div>
                            <div class="col-8">
                                <input type="text" name="email" />
                            </div>
                            <div class="col-3">Пароль:</div>
                            <div class="col-8">
                                <input type="password" name="password" />
                            </div>                            
                        </div>
                        <div class="row" style="margin-top: 15px; border-top: 1px #1d9d74 dashed;">
                            <div class="col-6">
                                <a href="/user/register.php">Реєстрація</a>                                
                            </div>
                            <div class="col-6" style="text-align: right; margin: 20px 0;">
                                <button type="submit" class="btn btn-success add-btn">Log In <i class="fa fa-sign-in fa-lg"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>            
        </div>
    </body>
</html>

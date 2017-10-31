<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/register_page.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $config = new Config();
    $repository = new UserRepository($config->DataBaseHandler);
    $auth = new Auth($repository, "user_id", "user_hash");
    $page = new RegisterPage($repository);
    $user = $page->initUserFromRequest();
    
    if ($page->isValid($user)) {
        $user ->Hash = $auth->generateHash();
        $user ->Password = $auth->generatePasswordHash(filter_input(INPUT_POST, "password"), $user ->Hash);
        $repository->insert($user);
    }
    
    echo json_encode(array("success" => count($page->errors) == 0, "errors" => $page->errors));
    exit();
}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Banking | Реєстрація нового користувача</title>
    <script src="/content/js/script.js" type="text/javascript"></script>
    <script src="/content/js/jquery.js" type="text/javascript"></script>
    <script src="/content/js/jquery-ui.js" type="text/javascript"></script>
    <script src="/content/js/script.js" type="text/javascript"></script>
    
    <link href="/content/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/grid.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/admin.css" rel="stylesheet" type="text/css" />
</head>
    <body>
        <script type="text/javascript">
            $(function(){
                var form = new EditForm("#register-form", "/user/register.php");
                form.success = function () {
                   window.location.href = '/'; 
                };
                form.bindSubmit();
            });
        </script>
        <div class="row top">
            <div class="col-12"></div>
        </div>
        <div class="row">
            <div class="col-10 filter" style="margin: 120px auto; float: none;">
                <div class="title">
                    <span>Реєстрація нового користувача</span>
                </div>
                <form id="register-form" method="post" action="/user/register.php" class="edit-form">
                    <div style="border: 1px #474644 solid;">
                        <div class="row error">
                            <i class="fa fa-warning fa-lg"></i> Під час обробки вашого запиту сталася помилка.
                        </div>
                        <div class="row validation">
                            <ul>
                            </ul>				
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-4">Прізвище:</div>
                                    <div class="col-6"><input type="text" name="last_name" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Ім'я:</div>
                                    <div class="col-6"><input type="text" name="first_name" /></div>
                                </div>				
                                <div class="row">
                                    <div class="col-4">Побатькові:</div>
                                    <div class="col-6"><input type="text" name="sure_name" /></div>
                                </div>			
                                <div class="row">
                                    <div class="col-4">Телефон:</div>
                                    <div class="col-6"><input type="text" name="phone" /></div>
                                </div>			
                                <div class="row">
                                    <div class="col-4">Населений пункт:</div>
                                    <div class="col-6"><input type="text" name="city" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Адреса:</div>
                                    <div class="col-6"><input type="text" name="address" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Поштовий індекс:</div>
                                    <div class="col-6"><input type="text" name="zip" /></div>
                                </div>			
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-4">Email:</div>
                                    <div class="col-6"><input type="text" name="email" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Пароль:</div>
                                    <div class="col-6"><input type="password" name="password" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Пароль ще раз:</div>
                                    <div class="col-6"><input type="password" name="password_again" /></div>
                                </div>
                            </div>    
                        </div> 
                        <div class="row" style="margin-top: 15px; border-top: 1px #1d9d74 dashed;">
                            <div class="col-12" style=" margin: 20px 0; text-align: right">
                                <a class="btn btn-danger" href="/" style="color: #fff"><i class="fa fa-times-circle fa-lg"></i> Скасувати</a>
                                <button type="submit" class="btn btn-success add-btn">Реєстрація <i class="fa fa-user-plus fa-lg"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>            
        </div>
    </body>
</html>

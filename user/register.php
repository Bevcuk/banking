<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/register_page.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';

$errors = array();

function getRequestValue($key) {
    if (filter_has_var(INPUT_POST, $key)) {
        return filter_input(INPUT_POST, $key);
    }
    return "";
}
    
function validatePassword($password, $password_again, &$errors) {
    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,30}$/', $password)) {
        $errors[] = "Пароль повинен складатися із буквів англійського алфавіту і містити як мінімум одну цифру, бути не менше 6-ти і не більше 30-ти символів";
    }

    if($password !== $password_again)
    {
        $errors[] = "Паролі повинні співпадати";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $config = new Config();
    $repository = new UserRepository($config->DataBaseHandler);
    $auth = new Auth($repository, "user_id", "user_hash");
    
    $email = getRequestValue("email");
    $card_number = getRequestValue("card_number");
    $password = getRequestValue("password");
    $password_again = getRequestValue("password_again");
    validatePassword($password, $password_again, $errors);
    
    if (count($errors) == 0) {
        $user = $repository->getByEmailAndCardNumber($email, $card_number);
        
        if ($user != null) {
            $auth->changePassword($user->Id, $password);
        } else {
            $errors[] = "Не було знайдено користувача із таким Email або номером картки";
        }
    }
    
    echo json_encode(array("success" => count($errors) == 0, "errors" => $errors));
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
            <div class="col-6 filter" style="margin: 120px auto; float: none;">
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
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4">Email:</div>
                                    <div class="col-6"><input type="text" name="email" /></div>
                                </div>
                                <div class="row">
                                    <div class="col-4">Номер картки:</div>
                                    <div class="col-6"><input type="text" name="card_number" /></div>
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

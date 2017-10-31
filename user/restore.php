<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/pages/register_page.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/mailer.php';

function getRestorePasswordMessage($key, $email) {
        return "Вітаю!<br /><br />
Ви отримали цей лист, оскільки Ви або хтось інший намагається відновити пароль для облікового запису {$email} на <a target='_blank' href='http://online-banking.lb:8080'>online-banking.lb</a><br />
Якщо Ви бажаєте змінити свій пароль, будь ласка підтвердіть це натиснувши на лінк, наведений нижче:<br />
<a target='_blank' href='http://online-banking.lb:8080/user/new_password.php?key={$key}'>http://online-banking.lb:8080/user/new_password.php?key={$key}</a>
<br /><br />
З повагою<br />
команда shop.lb";
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();
    $config = new Config();
    $repository = new UserRepository($config->DataBaseHandler);
    $email = filter_input(INPUT_POST, 'email');  
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введений не правильний Email, введіть правильний Email";
    } else {
        $user = $repository->getByEmail($email);
        
        if ($user == null){
            $errors[] = "Користувач із таким Email не існує, введіть правильний Email";            
        }      
    }
    
    if (count($errors) == 0) {
        $guid = uniqid();
        $key =  base64_encode($guid.$email);
        $mailer = new Mailer();
            
        if ($mailer->send($email, "Restore password on online-banking.lb", getRestorePasswordMessage($key, $email))) {
            //$this->customer_repository->createRestorePasswordRecord($this->email, $guid);
            //$this->isSentEmail = true;
        } else {
            echo $mailer->getError();
            exit();
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
    <title>Online Banking | Відновлення паролю</title>
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
                var form = new EditForm("#restore-form", "/user/restore.php");
                form.success = function () {
                   $('#restore-form').hide();
                   $('#restore-info').show();                   
                };
                form.bindSubmit();
            });
        </script>
        <div class="row top">
            <div class="col-12"></div>
        </div>
        <div class="row">
            <div class="col-4 filter" style="margin: 120px auto; float: none;">
                <div class="title">
                    <span>Відновлення паролю</span>
                </div>
                <div style="border: 1px #474644 solid;">
                    <form id="restore-form" method="post" action="/user/restore.php" class="edit-form">
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
                                    <div class="col-3">Email:</div>
                                    <div class="col-8"><input type="text" name="email" /></div>
                                </div>
                            </div>    
                        </div> 
                        <div class="row" style="margin-top: 15px; border-top: 1px #1d9d74 dashed;">
                            <div class="col-12" style="text-align: right; margin: 20px 0;">
                                <a class="btn btn-danger" href="/" style="color: #fff"><i class="fa fa-times-circle fa-lg"></i> Скасувати</a>
                                <button type="submit" class="btn btn-success add-btn">Відновити <i class="fa fa-support fa-lg"></i></button>
                            </div>
                        </div>                    
                    </form>
                    <div class="row" id="restore-info" style="display: none;">
                        <div class="col-12">
                            <p>На вказаний Email був висланий лист із інструкцією для відновлення паролю. Перевірте будь-ласка пошту і слідуйте інструкція в листі. Якщо лист не прийшов до Вас - то повторіть спробу ще раз.</p>
                        </div>
                        <div class="col-12" style="text-align: right; margin: 20px 0; border-top: 1px #1d9d74 dashed; padding-top: 30px;">
                            <a class="btn btn-success" href="/" style="color: #fff"><i class="fa fa-home fa-lg"></i> Повернутись на головну сторінку</a>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </body>
</html>

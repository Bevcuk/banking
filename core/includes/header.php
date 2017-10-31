<?php
$user = $auth->getCurrentUser();
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Online Banking | Депозит</title>
    <script src="/content/js/script.js" type="text/javascript"></script>
    <script src="/content/js/jquery.js" type="text/javascript"></script>
    <script src="/content/js/jquery-ui.js" type="text/javascript"></script>
    
    <link href="/content/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="/content/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/grid.css" rel="stylesheet" type="text/css" />
    <link href="/content/css/admin.css" rel="stylesheet" type="text/css" />
</head>
    <body>
        <div class="row top">
            <div class="col-2">
            </div>
            <div class="col-10">
                <div style="text-align: right; color: #fff; margin: 35px 35px 0 0 ; font-size: 16px;">
                    Вітаємо <?php echo "{$user->LastName} {$user->FirstName} {$user->SureName}" ?>, 
                    <a href="/user/logout.php" style="background: none; color: #F5C501; text-decoration: none;">Вийти <i class="fa fa-sign-out"></i></a>
                </div>
                <?php if ($auth->isClient() == true) : ?>
                <ul>
                    <li class="<?php echo $page_name == "account" ? "active" : "" ?>"><a href="/account/">Pахунок</a></li>
                    <li class="<?php echo $page_name == "deposit" ? "active" : "" ?>"><a href="/deposit/">Депозит</a></li>
                    <li class="<?php echo $page_name == "loan" ? "active" : "" ?>"><a href="/loan/">Кредит</a></li>
                </ul>
                <?php endif; ?>
            </div>            
        </div>
        <hr />
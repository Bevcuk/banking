<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/account_repository.php';

if($auth->isClient() == false) {
    $http->redirect('/payment/');
}

$repository = new AccountRepository($config->DataBaseHandler);
$account = $repository->getByUserId($_SESSION['user_id']);
$history = array();

if ($account != null) {
   $history = $repository->getHistoryByAccountId($account->Id);
}

$page_name = "account";
include $_SERVER['DOCUMENT_ROOT'].'/core/includes/header.php'
?>
<script type="text/javascript">
    $(function() {
         var form = new EditForm("#account-form", "/account/transfer.php");
            form.success = function () {
                alert("Ваш запит успішно виконаний");
                document.location.reload();
            };
            form.bindSubmit();
        
         var dialog = $('#pupup-window').dialog({
            width: '900',
            height: 'auto',
            autoOpen : false,
            resizable: false,
            modal: true,
            title: 'Здійснення грошового переказу'           
        });      
        
        $('#add-loan').click(function(){
            $('input','#account-form').val('');
            $('textarea','#account-form').val('');
            $('input[name=summa]','#account-form').val('0');
            $('.error', '#account-form').hide();
            $('.validation', '#account-form').hide();
            dialog.dialog("open");
            return false;
        }); 
        
        
        $('.btn-danger').click(function(){
            dialog.dialog("close");
            return false;
        });
    });
</script>
<div class="row">
<?php if ($account != null) : ?>    
    <div class="col-12">
        <a class="btn btn-success add-btn" href="#" id="add-loan"><i class="fa fa-file-o fa-lg" ></i> Створити платіж</a>
    </div>
    <div class="col-12">
        <div class="content">
            <div class="row header">
                <div class="col-12"><b>Загальна інформація</b></div>
            </div>
            <div class="list row ">
                <div class="col-12"><b>Баланс: <?php echo $account->Summa ?> грн</b></div>
            </div>           
            <br />
            <div class="row header">
                <div class="col-12"><b>Історія</b></div>
            </div>
            <div class="list">
                <?php foreach ($history as $item): ?>
                <div class="row">
                    <div class="col-2"><?php echo $item->CreateDate ?></div>
                    <div class="col-4"><?php echo $item->Comment ?></div>
                    <div class="col-2"><?php echo $item->Summa ?> грн</div>
                    <div class="col-4" style="color: #1d9d74">
                        <?php switch ($item->Status) {
                            case 0:
                                echo "Новий платіж";
                                break;
                            case 1:
                                echo "Платіж відхилений: ".$item->Reason;
                                break;
                            default:
                                echo "<b>Платіж підтверджений</b>";
                        } ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div id="pupup-window" style="display: none;">
        <form method="post" action="/account/transfer.php" id="account-form" class="box" enctype="multipart/form-data">
            <div class="edit-form">
                <div class="row error">
                   <i class="fa fa-warning fa-lg"></i> Під час обробки вашого запиту сталася помилка.
                </div>
                <div class="row validation">
                    <ul>
                        <li class="active"></li>
                    </ul>				
                </div>
                <div class="row">
                    <div class="col-4"><b>№ картки отримувач:</b></div>
                    <div class="col-8">
                        <input type="text" max="16" step="0.01" name="card" value="" style="width: 100%" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-4"><b>Сума:</b></div>
                    <div class="col-8">
                        <input type="number" step="0.01" name="summa" value="0" style="width: 100%; margin-top: 5px" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-4"><b>Призначення платежу:</b></div>
                    <div class="col-8">
                        <textarea name="comment" style="width: 100%; margin-top: 8px"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 edit-btns">
                    <a class="btn btn-danger" href="#"><i class="fa fa-times-circle fa-lg" ></i> Скасувати</a>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save fa-lg" ></i> Здійснити платіж</button>
                </div>
            </div>
        </form>
    </div>
 <?php endif; ?>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/core/includes/footer.php' ?>
    
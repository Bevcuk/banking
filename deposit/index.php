<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/deposit_repository.php';

if($auth->isClient() == false) {
    $http->redirect('/payment/');
}

$repository = new DepositRepository($config->DataBaseHandler);
$deposit = $repository->getByUserId($_SESSION['user_id']);
$history = array();

if ($deposit != null) {
    $history = $repository->getHistoryByDepositId($deposit->Id);
}

$page_name = "deposit";
include $_SERVER['DOCUMENT_ROOT'].'/core/includes/header.php'
?>
<script type="text/javascript">
    $(function() {
         var form = new EditForm("#deposit-form", "/deposit/add.php");
            form.success = function () {
               alert("Ваш запит успішно виконаний");
               document.location.reload();
            };
            form.bindSubmit();
        
         var dialog = $('#pupup-window').dialog({
            width: '600',
            height: 'auto',
            autoOpen : false,
            resizable: false,
            modal: true,
            title: 'Поповнення депозиту'           
        });      
        
        $('#add-deposit').click(function(){
            $('input[name=summa]','#deposit-form').val('0');
            $('.error', '#deposit-form').hide();
            $('.validation', '#deposit-form').hide();
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
<?php if ($deposit != null) : ?>    
    <div class="col-12">
        <a class="btn btn-success add-btn" href="#" id="add-deposit"><i class="fa fa-file-o fa-lg" ></i> Поповнити депозит</a>
    </div>
    <div class="col-12">
        <div class="content">
            <div class="row header">
                <div class="col-12"><b>Загальна інформація</b></div>
            </div>
            <div class="list row ">
                <div class="col-3"><b>Сума:</b> <?php echo $deposit->Summa ?> </div>
                <div class="col-3"><b>Відсоток:</b> <?php echo $deposit->Percent ?> %</div>
                <div class="col-3"><b>Початок:</b> <?php echo $deposit->StartDate ?></div>                
                <div class="col-3"><b>Закінчення терміну:</b> <?php echo $deposit->EndDate ?></div>
            </div>
            <br />
            <div class="row header">
                <div class="col-12"><b>Історія</b></div>
            </div>
            <div class="list">
                <?php foreach ($history as $item): ?>
                <div class="row">
                    <div class="col-4"><?php echo $item->CreateDate ?></div>
                    <div class="col-4"><?php echo $item->Comment ?></div>
                    <div class="col-4"><?php echo $item->Summa ?> грн</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div id="pupup-window" style="display: none;">
        <form method="post" action="/deposit/add.php" id="deposit-form" class="box" enctype="multipart/form-data">
            <input type="hidden" name="deposit_id" value="<?php echo $deposit->Id ?>" />
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
                    <div class="col-4"><b>Сума:</b></div>
                    <div class="col-8">
                        <input type="number" step="0.01" name="summa" value="0" style="width: 100%" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 edit-btns">
                    <a class="btn btn-danger" href="#"><i class="fa fa-times-circle fa-lg" ></i> Скасувати</a>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save fa-lg" ></i> Поповнити</button>
                </div>
            </div>
        </form>
    </div>
 <?php endif; ?>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/core/includes/footer.php' ?>
    
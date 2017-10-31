<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/account_repository.php';

if($auth->isClient() == true) {
    $http->redirect('/account/');
}

$repository = new AccountRepository($config->DataBaseHandler);
$history = $repository->getHistory();

include $_SERVER['DOCUMENT_ROOT'].'/core/includes/header.php'
?>
<script type="text/javascript">
    function loadPayment(id) {
        $.ajax({
            url: "load.php",
            type: "POST",
            data: { id: id },
            success: function (response) {
                try {
                    response = JSON.parse(response);
                    $('#payment-form .error').hide();
                    $('#payment-form .validation').hide();
                    $('#recipient').hide();
                    $('.edit-btns').hide();
                    $('#reason-info').hide();

                    if (response.success === true) {
                        var history = response.history;
                        var recipient = response.recipient;
                        
                        $('#date').text(history.CreateDate);
                        $('#fullname').text(history.LastName + ' ' + history.FirstName + ' ' + history.SureName);
                        $('#phone').text(history.Phone);
                        $('#email').text(history.Email);
                        $('#address').text(history.City + ', ' + history.Address + ', ' + history.ZipCode);
                        $('#summa').val(history.Summa);
                        $('#comment').val(history.Comment);
                        
                        if (history.Status == 0) {
                            $('.btn-danger').attr('value', id);
                            $('.btn-success').attr('value', id);
                            $('.edit-btns').show();
                        }
                        
                        if (history.Status == 1) {
                           $('#reason-info textarea').val(history.Reason);
                           $('#reason-info').show();
                        }
                        
                        if (recipient != false) {
                            $('#recipient_fullname').text(recipient.LastName + ' ' + recipient.FirstName + ' ' + recipient.SureName);
                            $('#recipient_phone').text(recipient.Phone);
                            $('#recipient_email').text(recipient.Email);
                            $('#recipient_address').text(recipient.City + ', ' + recipient.Address + ', ' + recipient.ZipCode);
                            $('#recipient').show();
                        }
                        
                        $('#pupup-window').dialog("open");
                    } else {
                       alert(response.message);
                    }
                } catch(ex) {
                    $('#payment-form .validation').hide();
                    $('#payment-form .error').show();
                    $('#pupup-window').dialog("open");
                }
            },
            error: function() {
               $('#payment-form .validation').hide();
               $('#payment-form .error').show(); 
               $('#pupup-window').dialog("open");
            }
        });
    }
    
    
    $(function() {
         var form = new EditForm("#payment-form", "/payment/submit.php");
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
            title: 'Інформація про платіж'           
        });      
        
        $('.btn-picture').click(function(){
            $('.btn-danger').hide();
            $('.btn-success').show();
            $('#reason').hide();
            loadPayment($(this).attr('value'));
            return false;
        }); 
        
        $('.btn-remove').click(function(){
            $('.btn-danger').show();
            $('.btn-success').hide();
            $('#reason textarea').val('');
            $('#reason').show();            
            loadPayment($(this).attr('value'));
            return false;
        }); 
        
        $('.btn-success').click(function(){
            $.ajax({
                url: "apply.php",
                type: "POST",
                data: { 
                    id: $(this).attr('value')
                },
                success: function () {
                    document.location.reload();
                },
                error: function() {
                    $('#payment-form .validation').hide();
                    $('#payment-form .error').show(); 
                }
            });
            
            return false;
        });
        
        $('.btn-danger').click(function(){
            var reason = $('#reason textarea').val();
            
            if (reason == '') {
                alert('Ви повинні ввести причину скасування платежу');
            } else {
                $.ajax({
                    url: "cancel.php",
                    type: "POST",
                    data: { 
                        id: $(this).attr('value'),
                        reason: reason
                    },
                    success: function () {
                        document.location.reload();
                    },
                    error: function() {
                        $('#payment-form .validation').hide();
                        $('#payment-form .error').show(); 
                    }
                });
            }
            
            return false;
        });
    });
</script>
<div class="row">
    <div class="col-12">
        <div class="content">
            
            <div class="row header">
                <div class="col-1"><b>Дата</b></div>
                <div class="col-2"><b>ПІБ</b></div>
                <div class="col-1"><b>Телефон</b></div>
                <div class="col-4"><b>Призначення платежу</b></div>
                <div class="col-2"><b>Сума</b></div>
                <div class="col-1"><b>Статус</b></div>
                <div class="col-1"></div>
            </div>
            <div class="list">
                <?php foreach ($history as $item): ?>
                <div class="row">
                    <div class="col-1"><?php echo $item->CreateDate ?></div>
                    <div class="col-2"><?php echo "{$item->LastName} {$item->FirstName} {$item->SureName}" ?>
                    </div>
                    <div class="col-1"><?php echo $item->Phone ?></div>
                    <div class="col-4"><?php echo $item->Comment ?></div>
                    <div class="col-2"><?php echo $item->Summa ?> грн</div>
                    <div class="col-1" style="color: #1d9d74">
                        <?php switch ($item->Status) {
                            case 0:
                                echo "Новий";
                                break;
                            case 1:
                                echo "<b style='color:#d9534f'>Відхилений</b>";
                                break;
                            default:
                                echo "<b>Проведений</b>";
                        } ?>
                    </div>
                    <div class="col-1 action-btn">
                        <div class="col-4">
                            <a class="btn-picture" value="<?php echo $item->HistoryId ?>" href="#"><i class="fa fa-info-circle"></i></a>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4">
                            <?php if ($item->Status == 0) : ?>
                            <a class="btn-remove" value="<?php echo $item->HistoryId ?>" href="#"><i class="fa fa-undo"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div id="pupup-window" style="display: none;">
        <form method="post" action="/account/transfer.php" id="payment-form" class="box" enctype="multipart/form-data">
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
                    <div class="col-4"><b>Дата/Час:</b></div>
                    <div class="col-8" id="date"></div>                    
                </div>
                <div class="row">
                    <div class="col-4"><b>ПІБ:</b></div>
                    <div class="col-8" id="fullname"></div>                    
                </div>
                <div class="row">
                    <div class="col-4"><b>Телефон:</b></div>
                    <div class="col-8" id="phone"></div>                    
                </div>
                <div class="row">
                    <div class="col-4"><b>Адреса проживання:</b></div>
                    <div class="col-8" id="address"></div>                    
                </div>
                <div class="row">
                    <div class="col-4"><b>Email:</b></div>
                    <div class="col-8" id="email"></div>                    
                </div>                
                <div class="row">
                    <div class="col-4"><b>Призначення платежу:</b></div>
                    <div class="col-8">
                        <textarea id="comment" disabled="disabled"></textarea>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-4"><b>Сума:</b></div>
                    <div class="col-8">
                        <input type="text" disabled="disabled" id="summa" style="width: 200px;" /> грн
                    </div>                    
                </div>
                <div class="row" id="reason-info" style="display: none;">
                    <div class="col-4"><b>Відхилений у звязку із:</b></div>
                    <div class="col-8">
                        <textarea  disabled="disabled"></textarea>
                    </div>                    
                </div>
                <div id="recipient" style="display: none;" >
                        <div class="row">
                        <div class="col-4"><b>ПІБ отримувача:</b></div>
                        <div class="col-8" id="recipient_fullname"></div>                    
                    </div>
                    <div class="row">
                        <div class="col-4"><b>Телефон отримувача:</b></div>
                        <div class="col-8" id="recipient_phone"></div>                    
                    </div>
                    <div class="row">
                        <div class="col-4"><b>Адреса отримувача:</b></div>
                        <div class="col-8" id="recipient_address"></div>                    
                    </div>
                    <div class="row">
                        <div class="col-4"><b>Email отримувача:</b></div>
                        <div class="col-8" id="recipient_email"></div>                    
                    </div>
                </div>
                <div class="row" id="reason" style="display: none;">
                    <div class="col-4"><b>Причина скасування:</b></div>
                    <div class="col-8">
                        <textarea></textarea>
                    </div>  
                </div>
            </div>
            <div class="row">
                <div class="col-12 edit-btns">
                    <a class="btn btn-danger" href="#"><i class="fa fa-undo fa-lg" ></i> Скасувати</a>
                    <a  href="#" type="submit" class="btn btn-success"><i class="fa fa-info-circle fa-lg" ></i> Підтвердити</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/core/includes/footer.php' ?>
    
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/error_handler.php';

class AccountRepository {
    private $dbh = null;

    public function __construct($dbh) {
        $this->dbh = $dbh;
    }
    
    public function getByUserId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from account where UserId = ?");  
                $sth->bindParam(1, $id, PDO::PARAM_INT);
                $sth->execute();
                $sth->setFetchMode(PDO::FETCH_OBJ);

                return $sth->fetch();					
            }catch(Exception $e) {
                process_error($e);
            }
        }

        return null;
    }
    
    public function getHistory($id=null) {
        $query = "
            select
                u.Id as UserId,
                u.FirstName,
                u.LastName,
                u.SureName,
                u.Phone,
                u.Email,
                u.City,
                u.Address,
                u.ZipCode,
                a.Id as AccountId,
                a.CardNumber,
                ah.Id as HistoryId,
                ah.Summa,
                ah.Comment,
                ah.CreateDate,
                ah.Status,
                ah.Reason,
                ah.RecipientCardNumber
            from 
                account_history ah 
                inner join account a on ah.AccountId = a.Id
                inner join user u ON a.UserId = u.Id";
        
        if ($id != null) {
            $query .= " where ah.Id = ?";
        }
        
        $query .= " order by ah.Id desc";
        
        try {
            $sth = $this->dbh->prepare($query);  
            if ($id != null) {
                $sth->bindParam(1, $id, PDO::PARAM_INT);
            }
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_OBJ);

            return $id != null ? $sth->fetch() : $sth->fetchAll();					
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function getUserByCardNumber($card_number) {
        $query = "
            select
                u.Id as UserId,
                u.FirstName,
                u.LastName,
                u.SureName,
                u.Phone,
                u.Email,
                u.City,
                u.Address,
                u.ZipCode
            from 
                account a
                inner join user u ON a.UserId = u.Id
            where a.CardNumber = ?";
        
        try {
            $sth = $this->dbh->prepare($query);  
            $sth->bindParam(1, $card_number, PDO::PARAM_STR);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_OBJ);

            return $sth->fetch();					
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function getHistoryByAccountId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from account_history where AccountId = ? order by id desc");  
                $sth->bindParam(1, $id, PDO::PARAM_INT);
                $sth->execute();
                $sth->setFetchMode(PDO::FETCH_OBJ);

                return $sth->fetchAll();					
            }catch(Exception $e) {
                process_error($e);
            }
        }

        return null;
    }

    public function isEnoughMoney($user_id, $summa) {
        try {
           $sth = $this->dbh->prepare("select count(Id) from account where UserId = ? and Summa > ?");
           $sth->bindParam(1, $user_id, PDO::PARAM_INT);				
           $sth->bindParam(2, $summa);
           $sth->execute();

            return $sth->fetchColumn() > 0;				
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function isExists($user_id, $cart) {
        try {
           $sth = $this->dbh->prepare("select count(Id) from account where UserId <> ? and CardNumber = ? ");
           $sth->bindParam(1, $user_id, PDO::PARAM_INT);				
           $sth->bindParam(2, $cart);
           $sth->execute();

            return $sth->fetchColumn() > 0;				
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function transferRequest($user_id, $card, $summa, $comment) {
        try {
            $this->dbh->beginTransaction();
            
            $this->withdrawMoney($user_id, $summa);
            $this->addHistory($user_id, $card, $summa, $comment, 0); 
            
            $this->dbh->commit();
        }catch(Exception $e) {
            $this->dbh->rollBack();
            process_error($e);
        }
    }
    
     public function cancelRequest($history_id, $reason) {

        try {
            $this->dbh->beginTransaction();
            $history = $this->getHistory($history_id);
            
            if ($history != null) {
                $this->returnMoney($history->AccountId, $history->Summa);
                $this->updateHistoryStatus($history_id, 1, $reason); 
            }
            
            $this->dbh->commit();
        }catch(Exception $e) {
            $this->dbh->rollBack();
            process_error($e);
        }
    }
    
     public function applyRequest($history_id) {

        try {
            $this->dbh->beginTransaction();
            $history = $this->getHistory($history_id);
            
            if ($history != null) {
                $recipient = $this->getRecipientByCardNumber($history->RecipientCardNumber);
                $this->updateHistoryStatus($history_id, 2, null);
                
                if ($recipient != null) {
                    $comment = "Надходження платежу від: {$history->LastName} {$history->FirstName} {$history->SureName}\n "
                    . "№ карткового рахунку: {$history->CardNumber}\n "
                    . "Призначення платежу: {$history->Comment}";
                    
                    $this->addHistory($recipient->UserId, "", $history->Summa, $comment, 2);
                    $this->addMoney($recipient->UserId, $history->Summa);
                }
            }
            
            $this->dbh->commit();
        }catch(Exception $e) {
            $this->dbh->rollBack();
            process_error($e);
        }
    }
    
    private function addMoney($user_id, $summa) {
        $sth = $this->dbh->prepare("UPDATE account SET summa = summa + ? WHERE UserId = ?");
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $user_id, PDO::PARAM_INT);
        $sth->execute();        
    }
    
    private function withdrawMoney($user_id, $summa) {
        $sth = $this->dbh->prepare("UPDATE account SET summa = summa - ? WHERE UserId = ?");
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $user_id, PDO::PARAM_INT);
        $sth->execute();        
    }
    
    private function returnMoney($account_id, $summa) {
        $sth = $this->dbh->prepare("UPDATE account SET summa = summa + ? WHERE Id = ?");
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $account_id, PDO::PARAM_INT);
        $sth->execute();        
    }
    
     private function updateHistoryStatus($history_id, $status, $reason) {
        $sth = $this->dbh->prepare("UPDATE account_history SET Status = ?, Reason = ? WHERE Id = ?");
        $sth->bindParam(1, $status);
        $sth->bindParam(2, $reason);
        $sth->bindParam(3, $history_id, PDO::PARAM_INT);
        $sth->execute();        
    }
    
    private function getRecipientByCardNumber($card) {
        try {
            $sth = $this->dbh->prepare("select * from account where CardNumber = ?");  
            $sth->bindParam(1, $card);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_OBJ);

            return $sth->fetch();					
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    private function addHistory($user_id, $card, $summa, $comment, $status) {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO account_history (AccountId, Summa, Comment, CreateDate, Status, RecipientCardNumber) "
                . "SELECT Id, ?, ?, ?, ?, ? FROM account WHERE UserId= ?";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(1, $summa);        
        $sth->bindParam(2, $comment);
        $sth->bindParam(3, $date);
        $sth->bindParam(4, $status, PDO::PARAM_INT);
        $sth->bindParam(5, $card);
        $sth->bindParam(6, $user_id, PDO::PARAM_INT);
        $sth->execute();
    }
}
?>
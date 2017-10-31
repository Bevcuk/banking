<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/error_handler.php';

class DepositRepository {
    private $dbh = null;

    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    public function getByUserId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from deposit where UserId = ?");  
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
    
    public function getHistoryByDepositId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from deposit_history where DepositId = ?");  
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
    
    public function addMoney($user_id, $deposit_id, $summa) {
        try {
            $this->dbh->beginTransaction();
            
            $this->withdrawMoneyFromAccount($user_id, $summa);
            $this->addMoneyToDeposit($deposit_id, $summa);
            
            $this->dbh->commit();
        }catch(Exception $e) {
            $this->dbh->rollBack();
            process_error($e);
        }
    }    
    
    private function withdrawMoneyFromAccount($user_id, $summa) {
        $sth = $this->dbh->prepare("UPDATE account SET summa = summa - ? WHERE UserId = ?");
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $user_id, PDO::PARAM_INT);
        $sth->execute();
        
        $this->addAccountHistory($user_id, $summa);
    }
    
    public function addMoneyToDeposit($deposit_id, $summa) {
        $sth = $this->dbh->prepare("UPDATE deposit SET Summa = Summa + ? Where Id = ?");
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $deposit_id, PDO::PARAM_INT);
        $sth->execute();
        
        $this->addDepositHistory($deposit_id, $summa);
    }
    
    public function addDepositHistory($deposit_id, $summa) {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO deposit_history (DepositId, Summa, Comment, CreateDate, IgnoreSum) VALUES (?, ?, 'Поповнення депозиту', ?, 1)";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(1, $deposit_id, PDO::PARAM_INT);
        $sth->bindParam(2, $summa);
        $sth->bindParam(3, $date);
        $sth->execute();
    }
    
    private function addAccountHistory($user_id, $summa) {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO account_history (AccountId, Summa, Comment, CreateDate) SELECT Id, ?, 'Списання грошей для поповнення депозиту', ? FROM account WHERE UserId= ?";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $date);
        $sth->bindParam(3, $user_id, PDO::PARAM_INT);
        $sth->execute();
    }
}
?>
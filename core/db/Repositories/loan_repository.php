<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/error_handler.php';

class loanRepository {
    private $dbh = null;

    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    public function getByUserId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from loan where UserId = ?");  
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
    
    public function getHistoryByloanId($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from loan_history where LoanId = ?");  
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
    
    public function addMoney($user_id, $loan_id, $summa) {
        try {
            $date = date('Y-m-d H:i:s');
            $this->dbh->beginTransaction();
            $this->withdrawMoneyFromAccount($user_id, $summa);

            $query = "INSERT INTO loan_history (LoanId, Summa, Comment, CreateDate) VALUES (?, ?, 'Погашення кредитної заборгованості', ?)";
            $sth = $this->dbh->prepare($query);
            $sth->bindParam(1, $loan_id, PDO::PARAM_INT);
            $sth->bindParam(2, $summa);
            $sth->bindParam(3, $date);
            $sth->execute();
            
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
    
    private function addAccountHistory($user_id, $summa) {
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO account_history (AccountId, Summa, Comment, CreateDate) SELECT Id, ?, 'Списання грошей для погашення кредитної заборгованості', ? FROM account WHERE UserId= ?";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(1, $summa);
        $sth->bindParam(2, $date);
        $sth->bindParam(3, $user_id, PDO::PARAM_INT);
        $sth->execute();
    }
}
?>
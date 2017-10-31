<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/error_handler.php';

class UserRepository {
    private $dbh = null;

    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    public function getById($id) {
        if($id != null) {
            try {
                $sth = $this->dbh->prepare("select * from user where Id = ?");  
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

    public function getByEmail($email) {
        try {
            $sth = $this->dbh->prepare("select * from user where Email = ?");  
            $sth->bindParam(1, $email);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_OBJ);

            return $sth->fetch();					
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function getByEmailAndCardNumber($email, $card_number) {
        try {
            $sth = $this->dbh->prepare("select u.* from user u INNER JOIN account a ON a.UserId = u.Id where u.Email = ? AND a.CardNumber = ?");  
            $sth->bindParam(1, $email);
            $sth->bindParam(2, $card_number, PDO::PARAM_STR);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_OBJ);

            return $sth->fetch();					
        }catch(Exception $e) {
            process_error($e);
        }
    }

    public function isExistsByEmail($email) {
        try {		
            $sth = $this->dbh->prepare("select count(Id) from user where Email = ?");
            $sth->bindParam(1, $email);				
            $sth->execute();

            return $sth->fetchColumn() > 0;				
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function isExistsByIdHash($id, $hash) {
        try {
            $sth = $this->dbh->prepare("select count(Id) from user where Id = ? and Hash = ?");
            $sth->bindParam(1, $id);
            $sth->bindParam(2, $hash);
            $sth->execute();

            return $sth->fetchColumn() == 1;
        }catch(Exception $e) {
            process_error($e);
        }
    }

    public function updatePassword($id, $password, $hash) {
        try {		
            $query = "update user set Password = ?, Hash = ? where Id = ?";
            
            $sth = $this->dbh->prepare($query);
            $sth->bindParam(1, $password);
            $sth->bindParam(2, $hash);
            $sth->bindParam(3, $id, PDO::PARAM_INT);
            $sth->execute();
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function isClient($id) {
        try {		
            $sth = $this->dbh->prepare("select count(Id) from user where Id = ? and UserRoleId = 5");
            $sth->bindParam(1, $id, PDO::PARAM_INT);				
            $sth->execute();

            return $sth->fetchColumn() > 0;				
        }catch(Exception $e) {
            process_error($e);
        }
    }
    
    public function insert($user) {
        try {		
            $query = "INSERT INTO user(UserRoleId, FirstName, LastName, SureName, Phone, Email, City, Address, ZipCode, Password, Hash) 
            VALUES (5, ?,?,?,?,?,?,?,?,?,?)";
            
            $this->dbh->beginTransaction();
            $sth = $this->dbh->prepare($query);
            $sth->bindParam(1, $user->FirstName);
            $sth->bindParam(2, $user->LastName);
            $sth->bindParam(3, $user->SureName);
            $sth->bindParam(4, $user->Phone);
            $sth->bindParam(5, $user->Email);
            $sth->bindParam(6, $user->City);
            $sth->bindParam(7, $user->Address);
            $sth->bindParam(8, $user->ZipCode);
            $sth->bindParam(9, $user->Password);
            $sth->bindParam(10, $user->Hash);
            
            $sth->execute();
            $this->dbh->commit();
        }catch(Exception $e) {
            $this->dbh->rollBack();
            process_error($e);
        }
    }
}
?>
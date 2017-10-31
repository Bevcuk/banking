<?php
define('UNIQUE_SALT', '5&nL*dF4');

class Auth {
    private $repository;
    private $key_id;
    private $key_hash;
    
    public function __construct($repository, $key_id, $key_hash) {
        $this->repository = $repository;
        $this->key_id = $key_id;
        $this->key_hash = $key_hash;
    }
    
    public function isAuth() {
        if (!isset($_SESSION[$this->key_id]) || !isset($_SESSION[$this->key_hash])) {
            return false;
	}	
	
        return $this->repository->isExistsByIdHash($_SESSION[$this->key_id], $_SESSION[$this->key_hash]);
    }
    
    public function auth($email, $password) {
        $user = $this->repository->getByEmail($email);
                
        if ($user != null) {
            if ($user->Password == $this->generatePasswordHash($password, $user->Hash)) {
                $_SESSION[$this->key_id] = $user->Id;
                $_SESSION[$this->key_hash] = $user->Hash;
                return true;
            }
        }
        
        return false;
    }
    
    public function checkPassword($id, $password) {
        $user = $this->repository->getById($id);
        
        return $user != null && $user->Password === $this->generatePasswordHash($password, $user->Hash);
    }
    
    public function changePassword($id, $password) {
        $hash = $this->generateHash();
        $password = $this->generatePasswordHash($password, $hash);
        $this->repository->updatePassword($id, $password, $hash);
        $_SESSION[$this->key_hash] = $hash;
    }
    
    public function generatePasswordHash($password, $hash) {
        return hash('sha512', UNIQUE_SALT.sha1($password).$hash.sha1(UNIQUE_SALT));
    }
    
    public function generateHash() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";

        $clen = strlen($chars) - 1;  
        while (strlen($code) < 128) {
            $code .= $chars[mt_rand(0,$clen)];  
        }

        return sha1($code);
    }
    
    public function isClient() {
        return $this->repository->isClient($_SESSION[$this->key_id]);
    }
    
    public function getCurrentUser() {
        return $this->repository->getById($_SESSION[$this->key_id]);
    }
}
?>
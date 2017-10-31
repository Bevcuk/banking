<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/repositories/user_repository.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/db/user.php';

class RegisterPage {
    public $errors = array();
    public $repository;

    public function __construct($repository) {
        $this->repository = $repository;
    }

    function initUserFromRequest() {
        $user = new User();		

        $user->FirstName = $this->getRequestValue("first_name");
        $user->LastName = $this->getRequestValue("last_name");
        $user->SureName = $this->getRequestValue("sure_name");
        $user->Phone = $this->getRequestValue("phone");
        $user->Email = $this->getRequestValue("email");
        $user->City = $this->getRequestValue("city");
        $user->Address = $this->getRequestValue("address");
        $user->ZipCode = $this->getRequestValue("zip");

        return $user;
    }

    function isValid($user) {
        $this->validate($user->FirstName, "Ім'я є обов'язковим полем");
        $this->validate($user->LastName, "Прізвище є обов'язковим полем");	
        $this->validate($user->SureName, "Побатькові є обов'язковим полем");	
        $this->validate($user->Phone, "Телефон є обов'язковим полем");	
        $this->validate($user->City, "Населений пункт є обов'язковим полем");	
        $this->validate($user->Address, "Вулиця є обов'язковим полем");	
        $this->validate($user->ZipCode, "Поштовий індекс є обов'язковим полем");

        
        $this->validate($user->Email, "Email є обов'язковим полем");
        $this->validateEmail($user->Email);
        $this->validatePassword();
        
        return count($this->errors) == 0;	
    }

    private function getRequestValue($key) {
        if (filter_has_var(INPUT_POST, $key)) {
            return filter_input(INPUT_POST, $key);
        }

        return "";
    }

    private function validate($value, $message) {
        if ($value == null || strlen($value) == 0) {
            $this->errors[] = $message;
        }
    }

    private function validatePassword() {
        $password = $this->getRequestValue("password");

        if(strlen($password ) == 0) {
            $this->errors[] = "Пароль є обов'язковим полем";
            return;
        } 

        if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,30}$/', $password)) {
            $this->errors[] = "Пароль повинен складатися із буквів англійського алфавіту і містити як мінімум одну цифру, бути не менше 6-ти і не більше 30-ти символів";
            return;
        }

        if($password !== $this->getRequestValue("password_again"))
        {
            $this->errors[] = "Паролі повинні співпадати";
            return;
        }
    }

    private function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email повинен бути правильним";
        } else if ($this->repository->isExistsByEmail($email)){
            $this->errors[] = "Користувач із таким Email вже існує, введіть інший Email";
        }
    }
}

<?php
   class User {
    
         private $id;
         private $email;
         private $password;
         private $username;
         private $name;
         private $surname;
         private $roleId;
    
         public function __construct($id, $email, $password, $username, $name, $surname, $roleId) {
              $this->id = $id;
              $this->email = $email;
              $this->password = password_hash($password, PASSWORD_BCRYPT);
              $this->username = $username;
              $this->name = $name;
              $this->surname = $surname;
              $this->roleId = $roleId;
         }
    
         public function getEmail() {
              return $this->email;
         }
    
         public function getPassword() {
              return $this->password;
         }
    
         public function getUsername() {
              return $this->username;
         }
    
         public function getName() {
              return $this->name;
        }

         public function getSurname() {
              return $this->surname;
         }
 }
?>
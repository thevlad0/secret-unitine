<?php
   class User {
    
         private $id;    
         private $fn; //can be null- for students only    
         private $email;
         private $password;
         private $username;
         private $name;
         private $surname;
         private $role;
         
         public function __construct($id, $fn, $email, $password, $username, $name, $surname, $role) {
              $this->id = $id; 
              $this->fn = $fn;
              $this->email = $email;
              $this->password = password_hash($password, PASSWORD_BCRYPT);
              $this->username = $username;
              $this->name = $name;
              $this->surname = $surname;
              $this->role = $role;
         }
        
         public function getEmail() {
              return $this->email;
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
         
         public function getRole() {
                 return $this->role;
        }

        public function getId() {
          return $this->id;
        }
 }
?>
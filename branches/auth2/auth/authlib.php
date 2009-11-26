<?php

    abstract class AuthBase
    {
        
        abstract protected function checkCredintals($login, $pwd);
        
        protected function saveSession($role)
        {
            session_start();
            $_SESSION["user_id"] = $role;
        }  
        
        public function login($login, $password)
        {
            if ($this->checkCredintals($login,$password))
            {
                $this->saveSession("User");
            }
        }    
               
    }
    
    class AuthTXT extends AuthBase
    {
        
        function checkCredintals($login, $pwd)
        {
            if ($login == "web" && $pwd == "web")
                return true;
            return false;    
        }
        
    }
    
    class Validator
    {

        static function checkSession()
        {
			session_start();
			if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]))
                return true;
            return false;
        }
		
		static function redirect()
		{
			if (!Validator::checkSession())
			{
				header("Location: /index.php?page=login");
				die();
			}
		} 
		   
    }

?>
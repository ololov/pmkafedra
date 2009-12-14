<?php

    abstract class AuthBase
    {
        
        abstract protected function checkCredintals($login, $pwd);
        
        protected function saveSession($role, $login)
        {
			$id = session_id();
			if (empty($id))
				session_start();

 		        $_SESSION["user_id"] = $role;
			$_SESSION["name"] = $login;
        }  
        
        public function login($login, $password)
        {
            if ($this->checkCredintals($login,$password))
            {
                $this->saveSession("User", $login);
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
		
		static function getName()
		{
			$id = session_id();
			if (empty($id))
				session_start();
			return $_SESSION["name"];
		}

        static function checkSession()
        {
			$id = session_id();
			if (empty($id))
				session_start();
			if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]))
                return true;
            return false;
        }
		
		static function redirect()
		{
			if (!Validator::checkSession())
			{
				header("Location: index.php?page=login");
				die();
			}
		} 
		   
    }

?>

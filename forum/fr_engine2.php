<?php
require_once('./include/lib.php');
require_once('./auth/authlib.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/auth/authlib.php');

require_once('fr_page.php');

class ForumEngine
{
       
        private $link;
        private $db_name = 'clericsu_pm';
       
        private function fillPage($page, $params=null)
        {
                $query = $page->selectQuery($params);
                if (!is_null($query))
                {
                        $result = pg_query($this->link, $page->selectQuery($params))
                                or die('query failed ' . pg_last_error());
                        while ($line = pg_fetch_assoc($result))
                        {
                                $page->addEntry($line);                                                
                        }
                        pg_free_result($result);
                }
                return $page;
        }
       
        private function insert($page, $params)
        {
                $query = $page->insertQuery($params);
                if (!is_null($query) && !empty($query))
                {
                        $result = pg_query($this->link, $query)
                                or die('query failed ' . pg_last_error());
                }
                return $result;
        }
       
        private function insertMessage()
        {
                $insert = array();
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['txt'])));
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['header'])));
                array_push($insert, pg_escape_string($_POST['thread_id']));
                array_push($insert, pg_escape_string(Validator::getName()));
                $this->insert(new ForumThreadPage(), $insert);                  
        }
       
        private function insertThread()
        {
                $insert = array();
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['theme_name'])));
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['theme_comment'])));
                array_push($insert, pg_escape_string(Validator::getName()));
                array_push($insert, pg_escape_string($_POST['theme_id']));
                if ($res = $this->insert(new ForumThemePage(), $insert))
                {
                        $result = pg_fetch_result($res,0);
                        $_POST['thread_id'] = $result;
                }
                $this->insertMessage();
        }
       
        private function checkUser()
        {
                if (Validator::checkSession())
                {
                        $name = Validator::getName();
                        if (empty($name) || is_null($name))
                                return false;
                        $check_query = sprintf("SELECT COUNT(*) FROM fr_user WHERE name='%s'",
                                        pg_escape_string($name));
                        $res = pg_fetch_result(pg_query($this->link, $check_query), 0);
                        if ($res == 1)
                                return true;
                        else
                        {
                                $add_user_query = sprintf("INSERT INTO fr_user (name, nickname, idrRole) VALUES('%s', '%s', '1')",
                                                pg_escape_string($name),
                                                pg_escape_string($name));
                                pg_query($this->link, $add_user_query);
                                return false;
                        }
                }
                return false;
        }
       
        private function updateUser()
        {
                $insert = array();
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['nickname'])));
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['signature'])));
                array_push($insert, pg_escape_string(htmlspecialchars($_POST['name'])));
                $this->insert(new ForumCabinetPage(), $insert);
        }
       
        public function processRequest()
        {
                $params = array();
                if (isset($_GET['post_thread']))
                {
                        $this->insertThread();
                        $_GET['theme'] = $_POST['theme_id'];
                        array_push($params, $_GET['theme']);
                        $page = new ForumThreadPage();
                }
                if (isset($_GET['new_thread']))
                {
                        array_push($params, $_GET['new_thread']);
                        $page = new ForumNewThreadPage();
                }
                elseif (isset($_GET['post_msg']))
                {
                        $this->insertMessage();
                        array_push($params, $_POST['thread_id']);
                        $_GET['thread'] = $_POST['thread_id'];
                        $page = new ForumThreadPage();
                }
                elseif (isset($_GET['new_message']))
                {
                        array_push($params, $_GET['new_message']);
                        $page = new ForumNewMessagePage();
                }
                elseif (isset($_GET['theme']))
                {
                        array_push($params, $_GET['theme']);
                        $page = new ForumThemePage();
                }
                elseif (isset($_GET['thread']))
                {
                        array_push($params, $_GET['thread']);
                        $page = new ForumThreadPage();
                }
                elseif (isset($_GET['post_user_data']))
                {
                        $this->updateUser();
                        session_start();
                        $_GET = $_SESSION['request'];
                        unset($_SESSION['request']);
                        return $this->processRequest();
                }
                else
                        $page = new ForumMainPage();
                //
                // Åñëè ñòðàíèöà ïðåäïîëàãàåò ìîäèôèêàöèþ äàííûõ,
                // àâòîðèçóåìñÿ.
                //
                if (is_a($page, "ForumModifyPage"))
                {
                        if (!$this->checkUser())
                        {
                                session_start();
                                $_SESSION['request'] = $_GET;
                                array_push($params, pg_escape_string(Validator::getName()));
                                $page = new ForumCabinetPage();
                        }
                }
                return $this->fillPage($page, $params);
        }
       
        private function init()
        {
               
        }
       
        public function __construct($host, $db, $usr, $pwd)
        {
                $this->init();


 /*
		$this->db_name = $db;
                $conn_str = sprintf("host=%s port=5432 dbname=%s user=%s",//password=%s",
                        $host,
                        $db,
                        $usr);
                        //$pwd);
                $this->link = pg_pconnect($conn_str)
                        or die('Could not connect: ' . pg_last_error());
*/
		$this->link = db_connect();
        }
       
}

?>


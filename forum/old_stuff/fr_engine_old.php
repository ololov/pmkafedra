<?php

	require_once($_SERVER["DOCUMENT_ROOT"] . '/auth/authlib.php');
    
    class ForumEngine
    {
        
        private $db_name = 'mydb';
        private $db_msg_table = 'message';
        private $db_theme_table = 'theme';
        private $db_usr_table = 'user';
        private $db_thread_table = 'thread';
		
		private $link;
        
		//
		// SUMMARY:
		//   Получает из БД данные, необходимые для визуализации
		//   главной страницы форума (т.е. список тем с необходимыми атрибутами)
		// OUT:
		//   Возвращает 2-мерный массив, 1-й индекс которого соответствует нумерации
		//   строк в БД, а во 2-м:
		//     'id' -- идентификатор темы
		//     'name' -- название темы
		//     'comment' -- комментарий к теме
		//     'thread_count' -- количество тредов в теме
		//     'msg_count' -- количество сообщений в теме
		//     'last_msg' -- дата и автор последнего сообщения на форуме
		//
        public function getMainPage()
        {
			//
			// Получаем темы
			//
			$main_query = 'SELECT * FROM ' . $this->db_theme_table;
			$result = mysql_query($main_query)
				or die('query failed ' . mysql_error());
				
			//
			// Выходной массив с данными,
			// отражающими набор сущностей "Тема"
			//
			$out = array();
			$i = 0;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				//
				// Записываем в выходной массив уже
				// полученные из запроса данные
				//
				$out[$i]['id'] = $line['id'];
				$out[$i]['name'] = $line['name'];
				$out[$i]['comment'] = $line['comment'];
				
				//
				// Получаем количество тредов в теме
				//
				$thread_count_query = 'SELECT COUNT(*) FROM ' . 
									  $this->db_thread_table . 
									  ' WHERE idrTheme=' . 
									  $line['id'];
				$out[$i]['thread_count'] = mysql_result(mysql_query($thread_count_query), 0);
				
				//
				// Получаем количество сообщений в теме
				//
				$msg_count_query = 'SELECT COUNT(*) FROM ' . 
								   $this->db_msg_table . 
								   ' WHERE idrThread IN (SELECT id FROM ' . 
								   $this->db_thread_table . 
								   ' WHERE idrTheme=' . 
								   $line['id'] .
								   ')';
				$out[$i]['msg_count'] = mysql_result(mysql_query($msg_count_query), 0);
				
				//
				// Получаем дату последнего ответа
				//
				$last_msg_query = 'SELECT (SELECT nickname FROM user WHERE id = idrAuthor) AS name, time FROM ' .
								  $this->db_msg_table .
								  ' WHERE time = (SELECT MAX(time) FROM ' .
								  $this->db_msg_table .
								  ' WHERE idrThread IN (SELECT id FROM ' . 
								  $this->db_thread_table . 
								  ' WHERE idrTheme=' . 
								  $line['id'] .
								  '))';
				$last_msg = mysql_fetch_array(mysql_query($last_msg_query), MYSQL_ASSOC);
				if (isset($last_msg['time']) && isset($last_msg['name']))
					$out[$i]['last_msg'] = $last_msg['time'] . ' by ' . $last_msg['name'];
				else
					$out[$i]['last_msg'] = '   ---   ';
				$i++;				
			}
			mysql_free_result($result);
			$out['type'] = 'main';
			return $out;
		}
		
		
		
		public function getThemePage($id)
		{
			//
			// Получаем темы
			//
			$main_query = 'SELECT * FROM ' . $this->db_thread_table . ' WHERE idrTheme=' . $id;
			$result = mysql_query($main_query)
				or die('query failed ' . mysql_error());
			
			//
			// Выходной массив с данными,
			// отражающими набор сущностей "Тема"
			//
			$out = array();
			$i = 0;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				//
				// Записываем в выходной массив уже
				// полученные из запроса данные
				//
				$out[$i]['id'] = $line['id'];
				$out[$i]['name'] = $line['name'];
				$out[$i]['comment'] = $line['comment'];
				
				//
				// Получаем количество сообщений в треде
				//
				$thread_count_query = 'SELECT COUNT(*) FROM ' . 
					$this->db_msg_table . 
					' WHERE idrThread=' . 
					$line['id'];
				$out[$i]['msg_count'] = mysql_result(mysql_query($thread_count_query), 0);
				
				//
				// Получаем топикстартера
				//
				$author_query = 'SELECT nickname FROM '.
								$this->db_usr_table .
								' WHERE id=' .
								$line['idrStarter'];
				$out[$i]['author'] = mysql_result(mysql_query($author_query), 0);
						
				//
				// Получаем дату последнего ответа
				//
				/*$last_msg_query = 'SELECT (SELECT name FROM user WHERE id = idrAuthor) AS name, time FROM ' .
					$this->db_msg_table .
					' WHERE time = (SELECT MAX(time) FROM ' .
					$this->db_msg_table .
					' WHERE idrThread IN (SELECT id FROM ' . 
					$this->db_thread_table . 
					' WHERE idrTheme=' . 
					$line['id'] .
					'))';
				$last_msg = mysql_fetch_array(mysql_query($last_msg_query), MYSQL_ASSOC);
				$out[$i]['last_msg'] = $last_msg['time'] . ' by ' . $last_msg['name'];
				*/
				$i++;				
			}
			mysql_free_result($result);
			$out['type'] = 'theme';
			return $out;
		}
		
		public function getThreadPage($id)
		{
			
			$ava_folder = 'forum/avatars/';
			//
			// Получаем темы
			//
			$main_query = 'SELECT * FROM ' . $this->db_msg_table . ' WHERE idrThread=' . $id;
			$result = mysql_query($main_query)
				or die('query failed ' . mysql_error());
			
			//
			// Выходной массив с данными,
			// отражающими набор сущностей "Тема"
			//
			$out = array();
			$i = 0;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				//
				// Записываем в выходной массив уже
				// полученные из запроса данные
				//
				$out[$i]['id'] = $line['id'];
				$out[$i]['txt'] = $line['txt'];
				$out[$i]['header'] = $line['header'];
				$out[$i]['time'] = $line['time'];
				
				//
				// Получаем имя автора
				//
				$author_query = 'SELECT nickname FROM ' . 
					$this->db_usr_table . 
					' WHERE id=' . 
					$line['idrAuthor'];
				$out[$i]['author'] = mysql_result(mysql_query($author_query), 0);
				
				//
				// Получаем топикстартера
				//
				$sign_query = 'SELECT signature FROM '.
					$this->db_usr_table .
					' WHERE id=' .
					$line['idrAuthor'];
				$out[$i]['sign'] = mysql_result(mysql_query($sign_query), 0);
				
				$out[$i]['avatar'] = $ava_folder . 'default.png';
				if (file_exists($ava_folder . $line['idrAuthor'] . '.png'))
				{
					$out[$i]['avatar'] = $ava_folder . $line['idrAuthor'] . '.png';
				}
				
				$i++;				
			}
			mysql_free_result($result);
			$out['type'] = 'thread';
			return $out;
		}
		
		public function getNewMessagePage()
		{
			$out = array();
			$out['thread'] = $_GET['new_message'];
			$out['type'] = 'new_msg';
			return $out;
		}
		
		public function postMessage()
		{
			$main_query = sprintf("INSERT INTO %s VALUES(null, '%s', '%s', null, '%s', (SELECT id FROM %s WHERE name='%s'))", 
				mysql_real_escape_string($this->db_msg_table),
				mysql_real_escape_string(htmlspecialchars($_POST['txt'])),
				mysql_real_escape_string(htmlspecialchars($_POST['header'])),
				mysql_real_escape_string($_POST['thread_id']),
				mysql_real_escape_string($this->db_usr_table),
				mysql_real_escape_string(Validator::getName()));

			mysql_query($main_query)
				or die('query failed ' . mysql_error());
				
			//
			// "Эмулируем" запрос для отображения треда
			//
			$_GET['thread'] = $_POST['thread_id'];
			//
			// Отображаем страницы с тредом, куда
			// добавлено сообщение.
			// 
			return $this->getThreadPage($_POST['thread_id']);				
		}
		
		public function getNewThreadPage()
		{
			$out = array();
			$out['theme'] = $_GET['new_thread'];
			$out['type'] = 'new_thread';
			return $out;
		}
		
		public function postThread()
		{
			$main_query = sprintf("INSERT INTO %s VALUES(null,'%s','%s', (SELECT id FROM %s WHERE name='%s'), '%s')",
				mysql_real_escape_string($this->db_thread_table),
				mysql_real_escape_string(htmlspecialchars($_POST['theme_name'])),
				mysql_real_escape_string(htmlspecialchars($_POST['theme_comment'])),
				mysql_real_escape_string($this->db_usr_table),
				mysql_real_escape_string(Validator::getName()),
				mysql_real_escape_string($_POST['theme_id']));
			$result = mysql_query($main_query)
				or die('query failed ' . mysql_error());
			$_POST['thread_id'] = mysql_insert_id($this->link);
			return $this->postMessage();			
		}
		
		public function checkUser()
		{
			if (Validator::checkSession())
			{
				$name = Validator::getName();
				if (empty($name) || is_null($name))
					return false;
				$check_query = sprintf("SELECT COUNT(*) FROM %s WHERE name='%s'",
						mysql_real_escape_string($this->db_usr_table),
						mysql_real_escape_string($name));
				$res = mysql_result(mysql_query($check_query), 0);
				if ($res == 1)
					return true;
				else
				{
					$add_user_query = sprintf("INSERT INTO %s (name, nickname, idrRole) VALUES('%s', '%s', '1')",
						mysql_real_escape_string($this->db_usr_table),
						mysql_real_escape_string($name),
						mysql_real_escape_string($name));
					mysql_query($add_user_query);
					return false;
				}
			}
			return false;
		}
		
		public function updateUser()
		{
		}
			
		public function getCabPage()
		{
			$out = array();
			$out['type'] = 'cab';
			$name = Validator::getName();
			$main_query = sprintf("SELECT * FROM %s WHERE name='%s'",
					mysql_real_escape_string($this->db_usr_table),
					mysql_real_escape_string($name));
			$result = mysql_fetch_array(mysql_query($main_query), MYSQL_ASSOC);
			if ($result)
			{
				$out['sign'] = $result['sign'];
				$out['name'] = $result['nickname'];
			}
			else
			{
				$out['name'] = $name;
			}
			$out['real_name'] = $name;
			return $out;
		}
								
		public function processRequest()
		{
			if (!$this->checkUser())
			{
				return $this->getCabPage();
			}
			if (isset($_GET['post_thread']))
			{
				return $this->postThread();
			}
			if (isset($_GET['new_thread']))
			{
				return $this->getNewThreadPage(); 
			}
			if (isset($_GET['post_msg']))
			{
				return $this->postMessage();
			}
			if (isset($_GET['new_message']))
			{
				return $this->getNewMessagePage(); 
			}
			if (isset($_GET['theme']))
			{
				return $this->getThemePage($_GET['theme']);
			}
			if (isset($_GET['thread']))
			{
				return $this->getThreadPage($_GET['thread']);
			}
			return $this->getMainPage();
		}
        
        private function init()
        {
            
        }
        
        public function __construct($usr, $pwd)
        {
            $this->init();
			$this->link = mysql_pconnect('localhost', $usr, $pwd)
                or die('Could not connect: ' . mysql_error());
            mysql_select_db($this->db_name) 
                or die('Could not select database');
            mysql_query('set names utf8');
        }

    }

?>


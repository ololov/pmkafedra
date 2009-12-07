<?php

require_once($_SERVER["DOCUMENT_ROOT"] . '/auth/authlib.php');

class ForumEngine
{
	
	private $db_name = 'db_forum';
	private $db_msg_table = 'fr_message';
	private $db_theme_table = 'fr_theme';
	private $db_usr_table = 'fr_user';
	private $db_thread_table = 'fr_thread';
	
	private $link;
	
	//
	// SUMMARY:
	//   �������� �� �� ������, ����������� ��� ������������
	//   ������� �������� ������ (�.�. ������ ��� � ������������ ����������)
	// OUT:
	//   ���������� 2-������ ������, 1-� ������ �������� ������������� ���������
	//   ����� � ��, � �� 2-�:
	//     'id' -- ������������� ����
	//     'name' -- �������� ����
	//     'comment' -- ����������� � ����
	//     'thread_count' -- ���������� ������ � ����
	//     'msg_count' -- ���������� ��������� � ����
	//     'last_msg' -- ���� � ����� ���������� ��������� �� ������
	//
	public function getMainPage()
	{
		//
		// �������� ����
		//
		$main_query = 'SELECT * FROM ' . $this->db_theme_table;
		$result = pg_query($this->link, $main_query)
			or die('query failed ' . pg_last_error());
		
		//
		// �������� ������ � �������,
		// ����������� ����� ��������� "����"
		//
		$out = array();
		$i = 0;
		while ($line = pg_fetch_assoc($result))
		{
			//
			// ���������� � �������� ������ ���
			// ���������� �� ������� ������
			//
			$out[$i]['id'] = $line['id'];
			$out[$i]['name'] = $line['name'];
			$out[$i]['comment'] = $line['comment'];
			
			//
			// �������� ���������� ������ � ����
			//
			$thread_count_query = 'SELECT COUNT(*) FROM ' . 
				$this->db_thread_table . 
				' WHERE idrTheme=' . 
				$line['id'];
			$out[$i]['thread_count'] = pg_result(pg_query($thread_count_query), 0);
			
			//
			// �������� ���������� ��������� � ����
			//
			$msg_count_query = 'SELECT COUNT(*) FROM ' . 
				$this->db_msg_table . 
				' WHERE idrThread IN (SELECT id FROM ' . 
				$this->db_thread_table . 
				' WHERE idrTheme=' . 
				$line['id'] .
				')';
			$out[$i]['msg_count'] = pg_result(pg_query($msg_count_query), 0);
			
			//
			// �������� ���� ���������� ������
			//
			$last_msg_query = 'SELECT (SELECT nickname FROM fr_user WHERE id = idrAuthor) AS name, time FROM ' .
				$this->db_msg_table .
				' WHERE time = (SELECT MAX(time) FROM ' .
				$this->db_msg_table .
				' WHERE idrThread IN (SELECT id FROM ' . 
				$this->db_thread_table . 
				' WHERE idrTheme=' . 
				$line['id'] .
				'))';
			$last_msg = pg_fetch_assoc(pg_query($last_msg_query));
			if (isset($last_msg['time']) && isset($last_msg['name']))
				$out[$i]['last_msg'] = $last_msg['time'] . ' by ' . $last_msg['name'];
			else
				$out[$i]['last_msg'] = '   ---   ';
			$i++;				
		}
		pg_free_result($result);
		$out['type'] = 'main';
		return $out;
	}
	
	
	
	public function getThemePage($id)
	{
		//
		// �������� ����
		//
		$main_query = 'SELECT * FROM ' . $this->db_thread_table . ' WHERE idrTheme=' . $id;
		$result = pg_query($this->link, $main_query)
			or die('query failed ' . pg_last_error());
		
		//
		// �������� ������ � �������,
		// ����������� ����� ��������� "����"
		//
		$out = array();
		$i = 0;
		while ($line = pg_fetch_assoc($result))
		{
			//
			// ���������� � �������� ������ ���
			// ���������� �� ������� ������
			//
			$out[$i]['id'] = $line['id'];
			$out[$i]['name'] = $line['name'];
			$out[$i]['comment'] = $line['comment'];
			
			//
			// �������� ���������� ��������� � �����
			//
			$thread_count_query = 'SELECT COUNT(*) FROM ' . 
				$this->db_msg_table . 
				' WHERE idrThread=' . 
				$line['id'];
			$out[$i]['msg_count'] = pg_result(pg_query($thread_count_query), 0);
			
			//
			// �������� �������������
			//
			$author_query = 'SELECT nickname FROM '.
				$this->db_usr_table .
				' WHERE id=' .
				$line['idrstarter'];
			$out[$i]['author'] = pg_result(pg_query($author_query), 0);
			
			//
			// �������� ���� ���������� ������
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
			$last_msg = pg_fetch_array(pg_query($last_msg_query), pg_ASSOC);
			$out[$i]['last_msg'] = $last_msg['time'] . ' by ' . $last_msg['name'];
			*/
			$i++;				
		}
		pg_free_result($result);
		$out['type'] = 'theme';
		return $out;
	}
	
	public function getThreadPage($id)
	{
		
		$ava_folder = 'forum/avatars/';
		//
		// �������� ����
		//
		$main_query = 'SELECT * FROM ' . $this->db_msg_table . ' WHERE idrThread=' . $id;
		$result = pg_query($this->link, $main_query)
			or die('query failed ' . pg_last_error());
		
		//
		// �������� ������ � �������,
		// ����������� ����� ��������� "����"
		//
		$out = array();
		$i = 0;
		while ($line = pg_fetch_assoc($result))
		{
			//
			// ���������� � �������� ������ ���
			// ���������� �� ������� ������
			//
			$out[$i]['id'] = $line['id'];
			$out[$i]['txt'] = $line['txt'];
			$out[$i]['header'] = $line['header'];
			$out[$i]['time'] = $line['time'];
			
			//
			// �������� ��� ������
			//
			$author_query = 'SELECT nickname FROM ' . 
				$this->db_usr_table . 
				' WHERE id=' . 
				$line['idrauthor'];
			$out[$i]['author'] = pg_result(pg_query($author_query), 0);
			
			//
			// �������� �������������
			//
			$sign_query = 'SELECT signature FROM '.
				$this->db_usr_table .
				' WHERE id=' .
				$line['idrauthor'];
			$out[$i]['sign'] = pg_result(pg_query($sign_query), 0);
			
			$out[$i]['avatar'] = $ava_folder . 'default.png';
			if (file_exists($ava_folder . $line['idrauthor'] . '.png'))
			{
				$out[$i]['avatar'] = $ava_folder . $line['idrauthor'] . '.png';
			}
			
			$i++;				
		}
		pg_free_result($result);
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
		$main_query = sprintf("INSERT INTO %s VALUES(DEFAULT, '%s', '%s', DEFAULT, '%s', (SELECT id FROM %s WHERE name='%s'))", 
				pg_escape_string($this->db_msg_table),
				pg_escape_string(htmlspecialchars($_POST['txt'])),
				pg_escape_string(htmlspecialchars($_POST['header'])),
				pg_escape_string($_POST['thread_id']),
				pg_escape_string($this->db_usr_table),
				pg_escape_string(Validator::getName()));
		
		pg_query($this->link, $main_query)
			or die('query failed ' . pg_last_error());
		
		//
		// "���������" ������ ��� ����������� �����
		//
		$_GET['thread'] = $_POST['thread_id'];
		//
		// ���������� �������� � ������, ����
		// ��������� ���������.
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
		$main_query = sprintf("INSERT INTO %s VALUES(DEFAULTS,'%s','%s', (SELECT id FROM %s WHERE name='%s'), '%s')",
				pg_escape_string($this->db_thread_table),
				pg_escape_string(htmlspecialchars($_POST['theme_name'])),
				pg_escape_string(htmlspecialchars($_POST['theme_comment'])),
				pg_escape_string($this->db_usr_table),
				pg_escape_string(Validator::getName()),
				pg_escape_string($_POST['theme_id']));
		$result = pg_query($this->link, $main_query)
			or die('query failed ' . pg_last_error());
		$_POST['thread_id'] = pg_insert_id($this->link);
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
					pg_escape_string($this->db_usr_table),
					pg_escape_string($name));
			$res = pg_result(pg_query($check_query), 0);
			if ($res == 1)
				return true;
			else
			{
				$add_user_query = sprintf("INSERT INTO %s (name, nickname, idrRole) VALUES('%s', '%s', '1')",
						pg_escape_string($this->db_usr_table),
						pg_escape_string($name),
						pg_escape_string($name));
				pg_query($add_user_query);
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
				pg_escape_string($this->db_usr_table),
				pg_escape_string($name));
		$result = pg_fetch_array(pg_query($this->link, $main_query), PG_ASSOC);
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
		$conn_str = sprintf("host=localhost port=5432 dbname=%s user=%s password=%s",
			$this->db_name,
			$usr,
			$pwd);
		$this->link = pg_pconnect($conn_str)
			or die('Could not connect: ' . pg_last_error());
		//pg_select_db($this->db_name) 
		//	or die('Could not select database');
		//pg_query('set names utf8');
	}
	
}

?>


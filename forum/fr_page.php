<?php
	
	require_once('fr_avatar.php');
	
	// /------------------------------------------------\
	// | Базовый класс для визуализации страницы форума |
	// \------------------------------------------------/  
	
	abstract class ForumPage
	{
		
		public $CSS_FORUM  = '"forum"';
		public $CSS_ROW    = '"head_forum"';
		public $CSS_FIRST  = '"first"';
		public $CSS_LAST   = '"last"';
		public $CSS_IMG    = '"img_forum"';
		public $CSS_NOTE   = '"note"';
		public $CSS_THEME  = '"team"';
		public $CSS_SUBMIT = '"add"';
		
		private $get_query;
		private $type;
		protected $entries = array();
	
		//
		// Эти функции переопределяются в
		// производных классах... Они не абстрактные, т.к.
		// не всякая страница форума нуждается в именно таком наборе элементов.
		//
		public function printHead()
		{
			return "";
		}
		
		public function printRow($val)
		{
			return "";
		}

		public function printFooter()
		{
			return "";
		}
		
		public function addEntry($val)
		{
			array_push($this->entries, $val);
		}
		
		protected function getEntry()
		{
			return array_pop($this->entries);
		} 
			
		public function getType()
		{
			return $this->type;
		}
			
		public function selectQuery($params = null)
		{
			return null;
		}
		
		public function insertQuery($params)
		{
			return null;
		}
					
		//
		// Основная функция, в котрой определяется
		// общий порядок отображения страницы.
		//	
		public function printPage()
		{
			$res = "<table id=" . $this->CSS_FORUM . ">";
			$res = $res . $this->printHead();
			foreach ($this->entries as $entry)
			{
				$res = $res . $this->printRow($entry);
			}
			$res = $res . "</table>";
			$res = $res . $this->printFooter();
			return $res;
		}
		
		public function __construct()
		{
		}
	}
	
	abstract class ForumDisplayPage extends ForumPage
	{
		private $type = "display";
	}
	
	abstract class ForumModifyPage extends ForumPage
	{
		private $type = "modify";
	}
	
	// /------------------------------------------------\
	// |                Главная страница                |
	// \------------------------------------------------/  
	
	class ForumMainPage extends ForumDisplayPage
	{
		
		public $IMG_SOURCE1 = '"images/forum1.png"';
		
		public function selectQuery($params = null)
		{
			return 'SELECT * FROM main_page';
		}
			
		public function printHead()
		{
			return
				'<tr class = "head_forum">
				<td class = "first">Форум</td>
				<td>Тем</td>
				<td>Ответов</td>
				<td class = "last">Последний ответ</td>
				</tr>';
		}
		
		public function printRow($val)
		{
			$res = "";
			if (isset ($val["last_msg_time"]) && isset($val["last_msg_author"]))
				$last = $val["last_msg_time"] . ' by ' . $val["last_msg_author"];
			else
				$last = "   ---   ";
			return $res . 
				"<tr class=" . $this->CSS_ROW . ">
				<td class=" . $this->CSS_FIRST . ">
					<img src=" . $this->IMG_SOURCE1 . " class=" . $this->CSS_IMG . ">
					<a href=?page=forum&theme=" . $val['id'] ." class=" . $this->CSS_THEME . ">" . $val['name'] . "</a>
					<p class=" . $this->CSS_NOTE . ">" . $val['comment'] . "</p> 
				</td> 
				<td>" . $val['thread_count'] . "</td>
				<td>" . $val['message_count'] . "</td>
				<td class=" . $this->CSS_LAST . ">" . $last . "</td>";
			
		}
		
		public function __construct()
		{
			parent::__construct();
		}
						
	}
	
	// /------------------------------------------------\
	// |                Тема                            |
	// \------------------------------------------------/  
	class ForumThemePage extends ForumDisplayPage
	{
		
		public $IMG_SOURCE2 = '"images/topic1.png"';
		
		public function selectQuery($params = null)
		{
			return "SELECT * FROM theme_page WHERE idrtheme=" . $params[0];
		}
		
		public function insertQuery($params)
		{
			$q = vsprintf(
				"INSERT INTO fr_thread VALUES(DEFAULT,'%s','%s', (SELECT id FROM fr_user WHERE name='%s'), '%s');
				SELECT currval('id_thread');", 
				$params);
			return $q;
		}
			
		public function printRow($val)
		{
			$res = "";
			return $res . 
				"<tr class=" . $this->CSS_ROW . ">
					<td class=" . $this->CSS_FIRST . ">
					<img src=" . $this->IMG_SOURCE2 . " class=" . $this->CSS_IMG . ">
					<a href=?page=forum&thread=" . $val['id'] ." class=" . $this->CSS_THEME . ">" . $val['name'] . "</a>
					<p class=" . $this->CSS_NOTE . ">" . $val['comment'] . "</p>  
					</td> 
					<td>" . $val["topic_starter"] . "</td>
					<td>" . $val['message_count'] . "</td>
					<td class=" . $this->CSS_LAST . ">" . $val['last_msg'] . "</td>";
		}
		
		public function printHead()
		{
			return		
			'<tr class = "head_forum">
					<td class = "first">Тема</td>
					<td>Автор</td>
					<td>Ответов</td>
					<td class = "last">Последний ответ</td>
					</tr>';
		}
		
		public function printFooter()
		{
			if (isset($_GET['theme']))
				return '<br /><a href = "?page=forum&new_thread=' . $_GET['theme'] . '" class = "forum_button">Новая тема</a>';
		}
	
		public function __construct()
		{
			parent::__construct();
		}
		
	}
	
	
	// /------------------------------------------------\
	// |                Топик                           |
	// \------------------------------------------------/  
	
	class ForumThreadPage extends ForumDisplayPage
	{
		public $CSS_AVA  = '"forum_avatar"';
		public $CSS_AUTH = '"forum_auth"';
		public $CSS_FA   = '"forum_a"';
		public $CSS_NAME = '"forum_name"';
		public $CSS_TIME = '"forum_time"';
		public $CSS_NUM  = '"forum_numb"';
		public $CSS_TEXT = '"forum_text"';
		public $CSS_CONT = '"forum_cont"';
		public $CSS_SIGN = '"forum_subm"';
			
		public function selectQuery($params = null)
		{
			return "SELECT * FROM thread_page WHERE idrthread=" . $params[0];
		}
		
		public function insertQuery($params)
		{
			$q = vsprintf("INSERT INTO fr_message VALUES(DEFAULT, '%s', '%s', DEFAULT, '%s', (SELECT id FROM fr_user WHERE name='%s'))", $params);
			return $q;
		}
			
		public function printHead()
		{
			return "";
		}
		
		public function printRow($val)
		{
			$res = "";
			$ava = AvatarHelper::getAvatar($val['idrauthor']);
			return $res . 
			'<tr>
					<td rowspan = 2 id = ' . $this->CSS_AUTH . '>
					<div id = ' . $this->CSS_FA . '>
					<img height="100px" width="100px" src = ' . $ava . ' class = ' . $this->CSS_AVA . '></img>
					<p class = ' . $this->CSS_NAME . '>' . $val['author'] .'</p>
					</div>
				</td>
				<td id = ' . $this->CSS_TIME . '>' . $val['time'] .'</td>
				<td id = ' . $this->CSS_NUM . '>' . $val['header'] . '</td>
			</tr>
			<tr>
				<td colspan = 2 id = ' . $this->CSS_TEXT . '>
					<div id = ' . $this->CSS_CONT . '>
					<p>' .
					$val['txt'] .	
					'</p>
					<p class = ' . $this->CSS_SIGN . '>' .
					$val['sign'] .
					'</p>
					</div>
				</td>
			</tr>';
			
		}
		
		public function printFooter()
		{
			if (isset($_GET['thread']))
				return '<br /><a href = "?page=forum&new_message=' . $_GET['thread'] . '" class = "forum_button">Новое сообщение</a>';
		}
			
		public function __construct()
		{
			parent::__construct();
		}
		
	}
	
	
	// /------------------------------------------------\
	// |          Добавление нового сообщения           |
	// \------------------------------------------------/ 
	 
	class ForumNewMessagePage extends ForumModifyPage
	{
		
		public function selectQuery($params = null)
		{
			$this->addEntry($params[0]);
			return null;
		}
		
		public function printPage()
		{
			$res = "";
			return $res . 
			'<div id = "forum_main">
				<p class = "tit">Новое сообщение</p>
				<form id = "form_forum_mes" action = "?page=forum&post_msg=1" method="POST">
				<fieldset>
				<legend>Сообщение</legend>
				<p>Заголовок сообщения <input type = text class = "text_mes" name="header"></input></p>
				<p>Текст сообщения <textarea cols = 50 rows = 10 class = "text_mes" name="txt"></textarea></p>
				<input type="hidden" name ="thread_id" value="' . $this->getEntry() . '">
						</fieldset>
						<input type = submit value = "Отправить" class = "buttonSubmit">
					</form>
				</div>';
		}
		
	}
	
	
	// /------------------------------------------------\
	// |          Добавление новой темы                 |
	// \------------------------------------------------/ 
	
	class ForumNewThreadPage extends ForumModifyPage
	{
		
		public function selectQuery($params = null)
		{
			$this->addEntry($params[0]);
			return null;
		}
		
		public function printPage()
		{
			$res = "";
				return $res .  
				'<div id = "forum_main">
					<p class = "tit">Новая тема</p>
					<form id = "form_forum_mes" action = "?page=forum&post_thread=1" method="POST">
					<fieldset>
					<legend>Тема</legend>
					<p>Название темы<input type = text class = "text_mes" name="theme_name"></input></p>
					<p>Пояснения к теме<input type = text class = "text_mes" name="theme_comment"></input></p>
					<input type="hidden" name="theme_id" value="' . $this->getEntry() . '"
						</fieldset>
						<fieldset>
							<legend>Сообщение</legend>
							<p>Заголовок сообщения <input type = text class = "text_mes" name="header"></input></p>
							<p>Текст сообщения <textarea cols = 50 rows = 10 class = "text_mes" name="txt"></textarea></p>
						</fieldset>
						<input type = submit value = "Отправить" class = "buttonSubmit">
					</form>
				</div>';
		}
	}
	
	
	// /------------------------------------------------\
	// |              Личный кабинет                    |
	// \------------------------------------------------/ 
	
	class ForumCabinetPage extends ForumPage
	{
	
		public function selectQuery($params = null)
		{
			$res = sprintf("SELECT * FROM fr_user WHERE name='%s'",
				$params[1]);
			return $res;
		}
		
		public function insertQuery($params)
		{
			AvatarHelper::checkAvatar($_POST['id']);
			return vsprintf("UPDATE fr_user SET nickname='%s', signature='%s' WHERE name='%s'",
							$params);
		}
		
		public function printPage()
		{
			$res = "";
			return $res .
				'<p class = "tit">Личный кабинет</p>
				<form id = "form_forum_mes" action="?page=forum&post_user_data=1" method="POST" enctype="multipart/form-data">
					<fieldset>
						<legend>Данные пользователя</legend>
						<p>Отображаемое имя <input type = text class = "text_mes" name="nickname" value="' . $this->entries[0]['nickname'] . '"></input></p>
						<p>Подпись <input type = text class = "text_mes" name="signature" value="' . $this->entries[0]['signature'] . '"></input></p>
						<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
						<p>Аватар <input type="file" name="avatar" class="text_mes"></p>
						<input type="hidden" name="name" value="' . $this->entries[0]['name'] . '" />
						<input type="hidden" name="id" value="' . $this->entries[0]['id'] . '" />
					</fieldset>
					<input type = submit value = "Сохранить" class = "buttonSubmit">
				</form>';
		}
	}
	
?>
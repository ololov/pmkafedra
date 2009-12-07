<?php
	require_once('fr_page.php');

	//
	// Конструирует страницу нужного типа
	// 
	class ForumGUI
	{
		
		public static function printPage($val)
		{
			
			if ($val['type'] == 'main')
				$page = new ForumMainPage();
			else if ($val['type'] == 'theme')
				$page = new ForumThemePage();
			else if ($val['type'] == 'thread')
				$page = new ForumThreadPage();
			else if ($val['type'] == 'new_msg')
				$page = new ForumNewMessagePage();
			else if ($val['type'] == 'new_thread')
				$page = new ForumNewThreadPage();
			else if ($val['type'] == 'cab')
				$page = new ForumCabinetPage();
			else
				die('Внутренняя ошибка. Попробуйте обновить движок форума.');
			unset($val['type']);
			return $page->printPage($val);
		}
		
	}

?>
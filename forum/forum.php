<div id = forum_main>
	<?php
	
		require_once('fr_engine2.php');
		require_once('fr_page.php');
		
		//
		// *** ������� ��� ����! ***
		//
		$engine = new ForumEngine('localhost', 'clericsu_pm', 'postgres');//, '1');
		$page = $engine->processRequest();	
		print $page->printPage();
	?>
</div>

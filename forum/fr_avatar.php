<?php

	class AvatarHelper
	{
		private static $ext_list = array(
			'.jpeg',
			'.jpg',
			'.png',
			'.gif');
		private static $avatar_dir = "forum/avatars/";
		private static $default = "default.png";
			
		public static function getAvatar($id)
		{
			foreach (AvatarHelper::$ext_list as $ext)
			{
				$path = AvatarHelper::$avatar_dir . $id . $ext;
				if (file_exists($path))
					return $path;
			}
			return AvatarHelper::$avatar_dir . AvatarHelper::$default;
		}
		
		public static function checkAvatar($id)
		{
			if ($_FILES['avatar']['error'] == 0 && isset($id))
			{
				AvatarHelper::deleteOldAvatar($id);
				$tmp = preg_split('/\./', $_FILES['avatar']['name']);
				move_uploaded_file($_FILES['avatar']['tmp_name'], AvatarHelper::$avatar_dir . $id . "." . array_pop($tmp));
			}					
		}
		
		private static function deleteOldAvatar($id)
		{
			foreach (AvatarHelper::$ext_list as $ext)
			{
				$path = AvatarHelper::$avatar_dir . $id . $ext;
				if (file_exists($path) && is_writable($path))
					unlink($path);
			}
		}
	}

?>
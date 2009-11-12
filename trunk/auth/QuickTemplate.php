<?php

    /**
     * @author nameless
     */
    
    //
    // Класс, осуществляющий подстановку
    // дескрипторов типа %<буквы латиского алфавита>%
    //
    class QuickTemplate
    {
        //
        // Список дескрипторов и соответствующих им значений
        // ВНИМАНИЕ:
        //   в дескрипторе 'main' содержится путь к html документу,
        //   который необходимо пропарсить
        //
        private $t_def;
        
        //
        // Основная функция, осуществляет парсинг
        //
        public function parse($subset = 'main')
        {
            $noparse = false;
            $content = "";
            $temp_file = $this->t_def[$subset]['file'];
            //
            // Если связанный с дескриптором контент - файл,
            // пытаемся подставить содержимое этого файла
            //
            if (isset($temp_file))
            {
                if (strlen($temp_file) > 6)
                {
                    $ext = substr($temp_file, strlen($temp_file) - 6);
                }
                //
                // Осуществляем парсинг только если файл имеет расширение thtml.
                // Сделано из 2-х соображений:
                //   - Повышение эффективности (не парсим лишнее)
                //   - Возможность иметь обычные html файлы, в которых не надо осуществлять подстановку
                //
                if (strcasecmp($ext, ".thtml") != 0)
                {
                    $noparse = true;
                }
                $fr = fopen($temp_file, "r");
                if (!$fr)
                {
                    $content = "<!-- Load error '$temp_file' //-->";
                }
                else
                {
                    $content = fread($fr, filesize($temp_file));
                }
                fclose($fr);
            }
            //
            // Если связанный с дескриптором контент - строка...
            //
            else
            {
                if (isset($this->t_def[$subset]['content']))
                {
                    $content = $this->t_def[$subset]['content'];
                }
                else
                {
                    $content = "<!-- '$subset' is not defined //-->";
                }
            }
            //
            // *** РЕКУРСИЯ!!! ***
            // для всех дескрипторов, найденных внутри связанного с текущим дескриптором контента
            // рекурсивно вызываем эту функцию...
            //
            if (!$noparse)
            {
                $content = preg_replace("/\%([A-Z]*)\%/e",
                                        "QuickTemplate::parse(strtolower('$1'))",
                                        $content);
            }
            return $content;
        }
        
        function __construct($temp = '')
        {
            if (is_array($temp))
                $this->t_def = $temp;
        }
    }    
    

?>
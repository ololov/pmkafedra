<?php

    /**
     * @author nameless
     */
    
    //
    // �����, �������������� �����������
    // ������������ ���� %<����� ��������� ��������>%
    //
    class QuickTemplate
    {
        //
        // ������ ������������ � ��������������� �� ��������
        // ��������:
        //   � ����������� 'main' ���������� ���� � html ���������,
        //   ������� ���������� ����������
        //
        private $t_def;
        
        //
        // �������� �������, ������������ �������
        //
        public function parse($subset = 'main')
        {
            $noparse = false;
            $content = "";
            $temp_file = $this->t_def[$subset]['file'];
            //
            // ���� ��������� � ������������ ������� - ����,
            // �������� ���������� ���������� ����� �����
            //
            if (isset($temp_file))
            {
                if (strlen($temp_file) > 6)
                {
                    $ext = substr($temp_file, strlen($temp_file) - 6);
                }
                //
                // ������������ ������� ������ ���� ���� ����� ���������� thtml.
                // ������� �� 2-� �����������:
                //   - ��������� ������������� (�� ������ ������)
                //   - ����������� ����� ������� html �����, � ������� �� ���� ������������ �����������
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
            // ���� ��������� � ������������ ������� - ������...
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
            // *** ��������!!! ***
            // ��� ���� ������������, ��������� ������ ���������� � ������� ������������ ��������
            // ���������� �������� ��� �������...
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
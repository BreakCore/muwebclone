<?php  if (!defined('insite')) die(" no access");

class content
{
    private $vars = array(); //массив значений на которые будем заменять
    private $debug; //дебаг: показывать или нет пустые переменные
    private $themName; //название темы
    private $lng = array();
    private $clang; //текущий язык
    private $error = array();
    private $adr ;
    /**
     * @var string
     */
    private $file;

    /**
     * @param $addres
     * @param string $file
     * @param string $language
     * @param int $dbg
     * @param string $thName
     */
    function __construct($addres,$file="site",$language="rus",$dbg=0,$thName="default")
    {
        $this->debug = $dbg;
        $this->clang = $language;
        $this->themName=$thName;
        $this->add_dict($file);
        $this->adr = $addres;
        $this->vars["|siteaddress|"]=$this->adr;
        $this->vars["|theme|"]=$this->themName;
        $this->file = $file;
    }

    /**
     * Вывод "словаря"
     * @return array
     */
    public function getAbs()
    {
        return $this->lng;
    }

    /**
     * Вывод отдельного слова по идентификатору
     * @param mixed $id идентификатор
     * @return string
     */
    public function getVal($id)
    {
        if(isset($this->lng[$id]))
            return $this->lng[$id];
        return "";
    }


    /**
     * Добавляем язык к контенту
     * @param $file - название файла "словаря"
     * @param string $DELIMITER
     */
    public function add_dict($file,$DELIMITER="|")
    {
        if(is_array($file))
        {
            $this->lng+=$file;
            foreach ($file as $d=>$v)
                $this->vars[$DELIMITER.$d.$DELIMITER] = $v;
        }
        else
        {
            if (file_exists("lang/{$this->clang}/{$this->clang}_{$file}.php"))
            {
                /** @var $lang array */
                require "lang/{$this->clang}/{$this->clang}_{$file}.php";
                if (isset($lang) && is_array($lang))
                {
                    $this->lng+=$lang;
                    foreach ($lang as $d=>$v)
                        $this->vars["|".$d."|"] = $v;
                }
            }
        }
    }

    /**
     * возвращает текущий язык
     * @return string
     */
    public function cLAng()
    {
        return $this->clang;
    }

    /**
     * возвращает адрес сервера
     * @return mixed
     */
    public function getAdr()
    {
        return $this->adr;
    }

    /**
     * возвращает массив с ошибкой или фолс
     * @return array|bool
     */
    public  function errorInfo()
    {
        if (!empty($this->error))
            return $this->error;
        return false;
    }

    /**
     * добаляет в словарь
     * @param string $name - резервированное слово(без "|")
     * @param mixed $val - значение зарезервированного слова
     */
    public function set($name, $val)
    {
        $this->vars[$name] = $val;
    }

    /**
     * Функцимя аналог set, только для массивов
     * массив должен состоять из
     * ["|ключевое_слово|"] = на_Что_заменять
     * @param array $inputAr
     * @param string $delimiter
     */
    public function setArray($inputAr,$delimiter = "")
    {
        if(is_array($inputAr))
        {
            if($delimiter=="")
                $this->vars += $inputAr;
            else
            {
                foreach ($inputAr as $id=>$v)
                {
                    self::set("$delimiter$id$delimiter",$v);
                }
            }
        }
    }

    /**
     * заменяет название элемента в "словаре" (!в словаре должно присутствовать выражение $where)
     * @param string $what - что требуется вставить
     * @param string $where - за место чего
     */
    public function replace($what,$where)
    {
        $what = "|$what|";
        $where = "|$where|";
        if (!empty($this->vars[$what]))
        {
            $this->set($where,$this->vars[$what]);
            unset($this->vars[$what]);
        }
    }

    /**
     * функция выводит на экран или возвращает строку с содержимым шаблона и скрипта
     * @param string $tpl - название шаблона
     * @param int $type - 0 вывод на экран
     * @return mixed|string
     * @throws Exception
     */
    public function out($tpl,$type=0)
    {
        $path = "theme/".$this->themName."/them/".$tpl;

        if(file_exists($path))
        {
            $content = self::gContent($path);

            foreach($this->vars as $key => $val)
                $content = str_replace($key, $val, $content);

            if ($this->debug==0)
                $content = preg_replace("/[\|]+[A-Za-z0-9_]{1,25}[\|]+/", " ", $content);

            if($type==0)
                echo $content;

            return $content;
        }
        else
            throw new  Exception("theme: \"{$this->themName}\", file \"$tpl\" not found!");
    }

    public function out_content($tpl,$type=0)
    {
        if(file_exists($tpl))
        {
            $content = self::gContent($tpl);

            foreach($this->vars as $key => $val)
                $content = str_replace($key, $val, $content);

            if ($this->debug==0)
                $content = preg_replace("/[\|]+[A-Za-z0-9_]{1,25}[\|]+/", " ", $content);

            if($type==0)
                echo $content;

            return $content;
        }
        else
            throw new  Exception("$tpl not found!");
    }


    /**
     * отображение ошибок "на лету"
     * @param $ernum integer - номер ошибки(для поиска текста ошибки по "базе")
     * @param $olang string - язык сайта
     * @param $theme string - название темы
     * @param string $texz string - текст ошибки
     * @param int $stype integer 0-ошибка возвращается в сайт 1 поверх всего. глобальная ошибка
     * @return mixed|string
     */
    static public function showError($ernum,$olang="ru",$theme="default",$texz="",$stype=0)
    {
        /** @var $path string */
        $path =  "lang/{$olang}/error.php";
        if (file_exists($path))
        {
            include $path;
            /** @var $lang array */
            if($texz!="")
                $texz="($texz)";
            $text = $lang["err".$ernum].$texz;
        }
        else
        {
            $text = "Error #".$ernum." ".$texz;
        }

        if ($stype==0)
            $pathtosh = "theme/".$theme."/them/error.html";
        else
            $pathtosh = "theme/error.html";

        if (file_exists($pathtosh))
        {
            $content = self::gContent($pathtosh);// file_get_contents($pathtosh);
            return str_replace("|msg|", $text, $content);
        }
        else
            return "<h3>$text</h3>";
    }


    public function showEr($ernum=0,$fname=NULL)
    {
        self::add_dict("error");
        self::replace("err".$ernum,"msg");
        if ($fname==NULL)
            return self::out("error.html",1);
        else
            return self::out($fname,1);
    }

    static public function gContent($path)
    {
        return @file_get_contents($path);
    }

}
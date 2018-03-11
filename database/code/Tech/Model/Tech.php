<?
/**
 * Сборка полезных функций.
 *
 * @package  Tech
 * @author   Fred Melnichuk <fredeveloper@gmail.com>
 * @version  0.0.25
 */

class Tech_Model_Tech extends Tech_Object    
{
    public  $DIR_CLASS_ROOT;
    public  $fields_options;
    public  $get_url_func = array();
  
    
    /**
     * Удаляет параметры в ссылке
     * 
     * @return string
     */
    public function unsetParam()
    {
      $args = $this->arrays2array(func_get_args());
      if (sizeof($args) == 1) $url = URL_FULL;
      else
      {
        $url = $args[0];
        unset($args[0]);
      }
  
      $temp = explode('?',trim($url));
      if (sizeof($temp) > 1)
      {
        $temp[1] = explode('&',trim($temp[1]));
        foreach ($args as $key => $needle)
        {
          foreach ($temp[1] as $key => $value)
          {
            if (trim($value) == '')
              unset($temp[1][$key]);
            if (((strpos($value,'=') !== false) && (strtolower(substr($value,0,strpos($value,'='))) == strtolower($needle))) || (strtolower($value) == strtolower($needle)))
              unset($temp[1][$key]);
            if ((strpos($value,'[') !== false) && (strtolower(substr($value,0,strpos($value,'['))) == strtolower($needle)))
              unset($temp[1][$key]);
          }
        }
        $url = $temp[0].'?'.implode('&',$temp[1]);
        unset($temp);
        unset($args);
      }
  
      return preg_replace(array('/[\?]+$/'),array(''),$url);
    }
    
    public function addParam($url)
    {
      $args = $this->arrays2array(func_get_args());
      if (sizeof($args) == 1) $url = URL_FULL;
      else
      {
        $url = $args[0];
        unset($args[0]);
      }
  
      foreach ($args as $key => $value)
      {
        $value  = is_int($key)      ? "&$value" : "&$key=$value";
        $url   .= strpos($url,'?')  ? $value    : "?$value";
      }
      return preg_replace('/[\&]+/','&',$url);
    }
    
    public function __construct()
    {
      if (!defined('URL_FULL')) define('URL_FULL', @$_SERVER['REQUEST_URI']);
      parent::__construct();
      $this->tech();
    }
  
    public function forbidden($text = null)
    {
      ?>
      <h1>Forbidden.</h1>
      <? if ($text): ?>
          <div class="forbidden-description-text"><?php echo $text ?></div>
      <? endif; ?>
      <div class="forbidden-description-links">
          <a href="/">Home page</a>
      </div>
      <?
      exit;
    }
  
    public function arrays2array($array)
    {
      if (!is_array($array))
        return false;
        
      $result = array();
      foreach ($array as $key => $value)
      {
        if (!is_array($array[$key]))
          $result[$key] = $array[$key];
        else
          $result = array_merge($result, $array[$key]);
      }
      return $result;
    }
  
    /**
     * Аналог stripslashes.
     *
     * @param string|array $data
     * @return mixed
     */
    public function ssl($data)
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
          $data[$key] = $this->ssl($value);
      }
      else
      {
        if (is_string($data))
          $data = stripslashes($data);
      }
      
      return $data;
    }
    
    /**
     * Аналог htmlspecialchars.
     *
     * @param string|array $data
     * @return mixed
     */
    public function htmlspecialchars($data)
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
          $data[$key] = $this->htmlspecialchars($value);
      }
      else
      {
        if (is_string($data))
          $data = htmlspecialchars($data);
      }
      
      return $data;
    }
    
    /**
     * Аналог stripslashes.
     *
     * @param string|array $data
     * @return mixed
     */
    public function stripslashes($data)
    {
      return $this->ssl($data);
    }
      
    /**
     * Аналог trim.
     *
     * @param string|array $data
     * @return mixed
     */
    public function trim($data)
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
          $data[$key] = $this->trim($value);
      }
      else
      {
        if (is_string($data))
          $data = trim($data);
      }
      
      return $data;
    }
  
    public function preg_url($url, $replacement = '-')
    {
      $url = str_ireplace('&amp;', null, $url);
      $url = preg_replace('/[^\w\d\s]/i', null, $url);
      $url = preg_replace('/[\s]+/', $replacement, $url);
      $url = str_ireplace($replacement.'a'.$replacement, $replacement, $url);
      $url = preg_replace('/['.$replacement.']{2,}/', $replacement, $url);
      $url = preg_replace('/^'.$replacement.'|'.$replacement.'$/', null, $url);
      
      return $url;
    }
  
    public function preg_password($pass)
    {
      $pass = trim(stripslashes($pass));
      return preg_replace(array('/&amp;/','/[^a-zA-Z-0-9\s&^+#$@%=*\-\\\(\)\[\]!?.;,]/','/[\s]/'),array('-','-','-'),$pass);
    }
  
    public function preg_quot($text)
    {
      return str_ireplace(
        array('\'','"','`'),
        array('&#x27;', '&#x22;', '&#x60;'),
        $text
      );
    }
    public function unpreg_quot($text)
    {
      return str_ireplace(
        array('&#x27;', '&#x22;', '&#x60;'),
        array('\'','"','`'),
        $text
      );
    }
  
    public function unset_dbl_params($url)
    {
      $url = explode('?',$url);
      if (sizeof($url) > 1)
      {
        $url[1] = explode('&',$url[1]);
        if (sizeof($url[1]) > 1)
        {
          $vars       = array();
          $vars_null  = array();
          foreach ($url[1] as $value)
          {
            $value = explode('=',$value);
            if (sizeof($value) > 1)
              $vars[$value[0]] = $value[1];
            else $vars_null[$value[0]] = false;
          }
          foreach ($vars as $key => $value)
            if ($key == '') unset($vars[$key]);
              else $vars[$key] = "$key=$value";
          foreach ($vars_null as $key => $value)
            if ($key == '') unset($vars_null[$key]);
              else $vars_null[$key] = $key;
  
          $url[1] = implode('&',array_merge($vars,$vars_null));
        } else $url[1] = $url[1][0];
        $url = $url[0].'?'.$url[1];
      } else $url = $url[0];
      return $url;
    }
  
    public function gotoJ($url="")
    {
      if ($url == "") $url = URL_FULL;
      ?>
      <script language="JavaScript" type="text/javascript">
      <!--
        window.location.href = "<? echo $url ?>";
      //-->
      </script>
      <?
      die;
    }
  
    public function dblspace2space(&$data)
    {
      if (is_array(@$data))
      {
        foreach ($data as $key => $value)
        {
          if (is_array(@$data[$key]))
            $this->dblspace2space($data[$key]);
          else @$data[@$key] = preg_replace("/[\s]{2,}/"," ",trim(@$value));
        }
      } else $data = preg_replace("/[\s]{2,}/"," ",trim(@$data));
      return $data;
    }
  
    public function striptags(&$data)
    {
      if (is_array(@$data))
      {
        foreach ($data as $key => $value)
        {
          if (is_array(@$data[$key]))
            striptags($data[$key]);
          else @$data[@$key] = strip_tags(@$value);
        }
      } else $data = strip_tags(@$data);
      return $data;
    }
  
    public function preg_match_email($str)
    {
      if (preg_match("/^[\w-.0-9]+\@[\w-.0-9]+\.[\w]{2,6}$/",$this->trim($str))) return true;
      return false;
    }
  
    public function preg_match_password($str)
    {
      if (preg_match("/^[\w-.0-9]+$/",$this->trim($str))) return true;
      return false;
    }
  
    public function get_params()
    {
      if (strpos(URL_FULL, "?"))
        return (substr(URL_FULL,strpos(URL_FULL,"?")+1,strlen(URL_FULL)));
      else return "";
    }
      
    public function dir2path($dir)
    {
      return substr($dir,strpos($dir,DIR_ROOT)+strlen(DIR_ROOT)-1);
    }
    
    private function tech()
    {
    }

    public function folderSize($dir)
    {
      $size = 0;
      foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : $this->folderSize($each);
      }
      return $size;
    }
    
    public function get_url()
    {
      global $tech;
      $args = func_get_args();
      asort($tech->get_url_func);
      foreach ($tech->get_url_func as $key => $value)
      {
        if (strpos($key,'->') !== false)
        {
          $key = explode('->',$key);
          global ${substr($key[0],1)};
          $args = ${substr($key[0],1)} -> {str_replace('()','',$key[1])}($args);
        }
      }
      return $this -> clean_url($this -> unset_dbl_params(is_array($args) ? implode($args) : $args));
    }
  
    public function clean_url($url)
    {
      $url = preg_replace("/&\/$/","",$url);
      return $url;
    }
    
    /**
     * Генерация рандомной строки длинной $len символов
     *
     * @param int $len
     * @param string $chars
     * @return string
     */
    public function getRandomString($len = 16, $chars = null)
    {
      if (is_null($chars))
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        
      mt_srand(10000000*(double)microtime());
      
      for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) 
        $str .= $chars[mt_rand(0, $lc)];
        
      return $str;
    }
    
    /**
     * Генерация рандомной строки длинной $len символов. 
     * В нижнем регистре
     *
     * @param int $len
     * @param string $chars
     * @return string
     */
    public function getRandomStringLower($len = 16, $chars = null)
    {
      return $this->getRandomString($len, "abcdefghijklmnopqrstuvwxyz0123456789");
    }
    
    public function reverse($variable)
    {
      switch ($variable)
      {
        case  (int)0:   return 1; break;
        case  (int)1:   return 0; break;
      }
    }
    
    /**
     * добавляет данные к массиву по заданному пути
     */
    public function add2array(&$array,$path,$val,$is_add = false)
    {
      if (!is_array($path))
        $path = explode('][',substr(trim($path),1,-1));
  
      foreach ($path as $key => $value)
      {
        if (!isset($array[$value]))
          $array[$value] = false;
  
        unset($path[$key]);
        if (sizeof($path) > 0)
        {
          $this -> add2array($array[$value],$path,$val,$is_add);
          return;
        }
        else
        {
          if (is_array($val))
            if ($is_add)
              $array[$value][] = $val;
            else
              $array[$value][key($val)] = $val[key($val)];
          else
            $array[$value][] = $val;
          return;
        }
      }
    }
    
    public function str_compare()
    {
      $args   =   $this -> arrays2array(func_get_args());
      $this   ->  trim($args);
      $this   ->  lowercase($args);
      for ($i = 0; $i < sizeof($args); $i++)
        for ($k = $i+1; $k < sizeof($args); $k++)
          if ($args[$i] !== $args[$k])
            return false;
  
      return true;
    }
  
    public function lowercase(&$data)
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
        {
          if (is_array($data[$key]))
            $this->lowercase($data[$key]);
          else $data[$key] = strtolower($value);
        }
      } else $data = strtolower($data);
      return $data;
    }
  
    public function uppercase(&$data)
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
        {
          if (is_array($data[$key]))
            $this->uppercase($data[$key]);
          else $data[$key] = strtoupper($value);
        }
      } else $data = strtoupper($data);
      return $data;
    }
    
    public function var_dump($array)
    {
      ob_start();
      var_dump($array);
      $result = ob_get_contents();
      ob_end_clean();
      return $result;
    }
  
    public function print_r($array)
    {
      ob_start();
      print_r($array);
      $result = ob_get_contents();
      ob_end_clean();
      return $result;
    }
  
    function time($data, $day, $month, $year)
    {
      if (isset($data[$day]) && isset($data[$month]) && isset($data[$year]) && (int)$data[$day] > 0 && (int)$data[$month] > 0 && (int)$data[$year] > 0)
      {
        return mktime(0,0,0, (int)$data[$month], (int)$data[$day], (int)$data[$year]);
      }
      else
        return 0;
    }
    
    public function saveUrl($url = null)
    {
      if (is_null($url))
        $url = $_SERVER['REQUEST_URI'];
          
      Mage::getModel('core/session')->setData('encode_url'.($md5 = md5($url)), $url);
      return $md5;    
    }
    
    public function loadUrl($md5)
    {
      return Mage::getModel('core/session')->getData('encode_url' . $md5);    
    }
    
    
    /**
     * Уберает из массива повторяющиеся значения, 
     * все значения массивы уберает
     *
     * @param array $array
     * @return array
     */
    public function unsetDoubleValues($array)
    {
      $result = array();
      
      foreach ($array as $value) 
      {
        if (!is_array($value))
          $result[$value] = null;      
      }
      
      return array_keys($result);      
    }
    
    
    /**
     * транслит )
     *
     * @param string $str
     * @return string
     */
    public function translitIt($str) 
    {
      $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        " "=> "_", "."=> "", "/"=> "_"
      );
      
      return strtr($str, $tr);
    }
    
    
    /**
     * Добавляет к данным суфикс и префикс
     *
     * @param array|string $data
     * @param string $prefix
     * @param string $sufix
     * 
     * @return mixed
     */   
    public function wrapData($data, $prefix = '', $sufix = '')
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value) 
          $data[$key] = $this->wrapData($value, $prefix, $sufix);
          
        return $data;  
      }
      
      $data = trim($data);
      
      if (strlen($prefix) == 0)
        $data = trim($data, $prefix);
      if (strlen($sufix) == 0)
        $data = trim($data, $sufix);
      
      return $prefix . $data . $sufix; 
    }    
    
    
    /**
     * Склоняет слово в зависимости от цифры
     *
     * @param integer $n
     * @param string $template1
     * @param string $template2
     * @param string $template3
     * 
     * @return string
     */
    public function declinationString($n, $template1, $template2, $template3)
    {
       $index = ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2));
       
       return ($index == 1 ? $template2 : ($index == 2 ? $template3 : $template1));
    }
    
    
    /**
     * Форматирует байтовый размер
     *
     * @param integer $bytes
     * @param string $force_unit
     * @param string $format
     * @param bool $si
     *
     * @return string
     */
    public function formatBytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
    {
      // Format string
      $format = ($format === NULL) ? '%01.2f %s' : (string) $format;

      // IEC prefixes (binary)
      if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
      {
        $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
        $mod   = 1024;
      }
      // SI prefixes (decimal)
      else
      {
        $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
        $mod   = 1000;
      }

      // Determine unit to use
      if (($power = array_search((string) $force_unit, $units)) === FALSE)
      {
        $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
      }

      return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
    }


    /**
     * Строку в URL конвертирует
     *
     * @param string $string 
     * @return string
     */
    public function strToURL($string)
    {
      $string = mb_strtolower($string);
      $string = trim($string);
      $string = $this->translitIt($string);
      $string = preg_replace('/[^a-zA-Z0-9\-]/i', '-', $string);
      
      while (strstr($string, '--'))
        $string = str_replace('--', '-', $string);
        
      if (!$string)
        $string = 'untitled';
      
      return $string;
    }
    
    
    /**
     * Форматирует время в "$prefix N <секунд|минут|часов|дней|месяцев> $sufix"
     *
     * @param timestump|datetime $timestump
     * @param string $prefix
     * @param string $sufix
     * 
     * @return string
     */
    function timeElapsedString($timestump, $prefix = null, $sufix = 'назад')
    {
      if (preg_match('/[^0-9]+/i', trim($timestump)))
        $timestump = strtotime($timestump);
        
      $etime = time() - $timestump;
    
      if ($etime <= 1)
        return 'сейчас';
  
      $a = array( 
        12 * 30 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second',
      );
  
      $result = array();
      
      foreach ($a as $secs => $str)
      {
        $d = $etime / $secs;
        
        if ($d >= 1)
        {
          if ($prefix)
            $result[] = $prefix;
            
          $result[] = ($r = round($d));

          switch ($str)
          {
            case 'year': $result[] = $this->declinationString($r, 'год', 'года', 'лет'); break;            
            case 'month': $result[] = $this->declinationString($r, 'месяц', 'месяца', 'месяцев'); break;            
            case 'day': $result[] = $this->declinationString($r, 'день', 'дня', 'дней'); break;            
            case 'hour': $result[] = $this->declinationString($r, 'час', 'часа', 'часов'); break;            
            case 'minute': $result[] = $this->declinationString($r, 'минута', 'минуты', 'минут'); break;            
            case 'second': $result[] = $this->declinationString($r, 'секунда', 'секунды', 'секунда'); break;            
          }
          
          if ($sufix)
            $result[] = $sufix; 
           
          break;          
        }
      }
      
      return implode(' ', $result);
    }    
    
    
    /**
     * Возвращает день недели по дате
     *
     * @param integer|datetime $datetime
     */
    public function getDayName($datetime)
    {
      if (!is_integer($datetime))
        $datetime = strtotime($datetime);
        
      switch (date('w', $datetime))  
      {
        case 0: return 'воскресенье';
        case 1: return 'понедельник';
        case 2: return 'вторник';
        case 3: return 'среда';
        case 4: return 'четверг';
        case 5: return 'пятница';
        case 6: return 'суббота';
        default : return '---';
      }
    }

    
    /**
     * Удаляет элементы массива равные $escape_string
     *
     * @param array $data
     * @param string $escape_string
     * 
     * @return array
     */
    public function array_escape($data, $escape_string = '')
    {
      if (is_array($data))
      {
        foreach ($data as $key => $value)
        {
          if (is_array($value))
            $data[$key] = $this->escapeData($value, $escape_string);
          elseif (trim($value) == $escape_string)
            unset($data[$key]);          
        }
      }
      else
      {
        if (trim($data) == $escape_string)
          $data = null;
      }
      
      return $data;
    }
    
    
    /**
     * Возвращает диапазон дат в виде массива
     *
     * @param string $from
     * @param string $to
     * 
     * @return array
     */
    public function getDatesRange($date_from, $date_to)
    {
      $dates = array();
      
      $from = new DateTime($date_from);             
      $to = new DateTime($date_to);             
      
      if ($from > $to)
      {
        $temp = $from;
        $from = $to;
        $to = $temp;
      }
      
      if (($date = $from) && $to)
      {
        while ($date <= $to)
        {
          $dates[] = $date->format('Y-m-d');
          $date->add(new DateInterval('P1D'));
        }
      }
      
      return $dates;
    }
    
    
    /**
     * Возвращает количество дней в диапазоне дат
     *
     * @param string $from
     * @param string $to
     * 
     * @return integer
     */
    public function getDaysCountInDatesRange($date_from, $date_to)
    {
      $from = new DateTime($date_from);             
      $to = new DateTime($date_to);             
      
      $interval = $from->diff($to, true);
      
      return $interval->format('%a');
    }
    
    
    /**
     * Проверяет находится ли дата в диапазоне дат.
     *
     * @param string $from
     * @param string $from
     * @param string $to
     * @param bool $except_last_day - последний день НЕ включительно
     * 
     * @return bool
     */
    public function isDateInRange($date, $date_from, $date_to, $except_last_day = false)
    {
      $date_from = new DateTime($date_from);             
      $date_to = new DateTime($date_to);             
      $date = new DateTime($date);             
      
      return (bool)($date >= $date_from && (($except_last_day == false && $date <= $date_to) || ($except_last_day == true && $date < $date_to)));
    } 
    
    
    /**
     * Меняет ключи массива на ID значений
     *
     * @param array $array
     * @param string $id_key
     * 
     * @return array
     */
    public function restructArrayKeysByID($array, $id_key = 'id')
    {
      
      $result = array();
      
      foreach ((array)$array as $item) 
      {
        if (isset($item[$id_key]))
          $result[$item[$id_key]] = $item;
      }

      return $result;
    }
    
    
    /**
     * Возвращает первый элемент массива
     *
     * @param array $array
     * @return mixed
     */
    public function getFirstElementOfArray($array)
    {
      reset($array);
      return current($array);      
    }
    
    
    /**
     * Возвращает первый ключь массива
     *
     * @param array $array
     * @return mixed
     */
    public function getFirstKeyOfArray($array)
    {
      reset($array);
      return key($array);      
    }
    
    
    /**
     * Строит строку параметров
     *
     * @param array $params
     * @param array $changes
     * @param array $erase
     * 
     * @return string
     */
    public function getParamsString($params, $changes = array(), $erase = array())
    {
      foreach ((array)$params as $key => $value) 
      {
        if (! preg_match('/^[\w\d\-]{1,}$/i', $key) || ! preg_match('/^[\w\d]{1,}$/i', $value))
          unset($params[$key], $changes[$key]);
      }
      
      foreach ((array)$changes as $key => $value) 
      {
        if (preg_match('/^[\w\d\-]{1,}$/i', $key) && preg_match('/^[\w\d]{1,}$/i', $value))
          $params[$key] = $value;
        else unset($params[$key], $changes[$key]);
      }
        
      foreach ((array)$erase as $key) 
        unset($params[$key]);
        
      foreach ($params as $key => $value) 
        $params[$key] = "{$key}={$value}";

      return implode(' ', $params); 
    }


    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function arrayToXml( $data, $rootNodeName = 'ResultSet', &$xml=null ) 
    {
      // turn off compatibility mode as simple xml throws a wobbly if you don't.
      if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
      if ( is_null( $xml ) ) //$xml = simplexml_load_string( "" );
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");

      // loop through the data passed in.
      foreach( $data as $key => $value ) {

        $numeric = false;

        // no numeric keys in our xml please!
        if ( is_numeric( $key ) ) {
          $numeric = 1;
          $key = $rootNodeName;
        }

        // delete any char not allowed in XML element names
        $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

        // if there is another array found recrusively call this function
        if ( is_array( $value ) ) {
          $node = Tech::isAssoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

          // recrusive call.
          if ( $numeric ) $key = 'anon';
          Tech::arrayToXml( $value, $key, $node );
        } else {

          // add single node.
          $value = htmlentities( $value );
          $xml->addChild( $key, $value );
        }
      }

      // pass back as XML
      return $xml->asXML();

      // if you want the XML to be formatted, use the below instead to return the XML
      //$doc = new DOMDocument('1.0');
      //$doc->preserveWhiteSpace = false;
      //$doc->loadXML( $xml->asXML() );
      //$doc->formatOutput = true;
      //return $doc->saveXML();
    }


    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
     *
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function XmlToArray( $xml ) 
    {
      if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
      $children = $xml->children();
      if ( !$children ) return (string) $xml;
      $arr = array();
      foreach ( $children as $key => $node ) {
        $node = Tech::toArray( $node );

        // support for 'anon' non-associative arrays
        if ( $key == 'anon' ) $key = count( $arr );

        // if the node is already set, put it into an array
        if ( isset( $arr[$key] ) ) {
          if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
          $arr[$key][] = $node;
        } else {
          $arr[$key] = $node;
        }
      }
      return $arr;
    }


    /**
     * Является ли массив ассоциативным
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc( $array ) 
    {
      return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
  
  
    /**
     * Валидация даты
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s') 
    {
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }

    
    /**
     * Формат integer значения
     *
     * @param integer $value
     * @return string
     */
    public static function integerFormat($value)
    {
      $value = (int)$value;
      
      $value = preg_replace('/000$/', 'K', $value);
      $value = preg_replace('/000K/', 'M', $value);
      
      return $value;
    }
    
    
    /**
     * Сокращает строку до пробела.
     *
     * @param string $string
     * @param integer $length
     * @param string $etc
     * @param string $charset
     * @param boolean $break_words
     * @param boolean $middle
     * 
     * @return string
     */
    public static function shortString($string, $length = 80, $etc = '...', $charset = 'UTF-8', $break_words = false, $middle = false)    
    {
      if ($length == 0)
        return '';
      
      if (strlen($string) > $length)
      {
        $length -= min($length, strlen($etc));
        
        if (! $break_words && ! $middle)
        {
          $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $charset));
        }
        
        if (! $middle)
        {
          return mb_substr($string, 0, $length, $charset) . $etc;
        }
        else
        {
          return mb_substr($string, 0, $length / 2, $charset) . $etc . mb_substr($string, - $length / 2, $charset);
        }
      }
      else
      {
        return $string;
      }
    }
}
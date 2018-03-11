<?php
/**
 * Работа с базой данных. 
 * 
 * @package  Mysql
 * @author   Fred Melnichuk <fredeveloper@gmail.com>
 * @version  0.2.16
 * 
 * 
 * Примеры использования:
 * 
 * 
 * 1. Получение одного значения  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 * 
 * Core::getMysql()->getValue("SELECT user_id FROM users LIMIT 1")
 * 
 * 
 * 2. Получение НЕСКОЛЬКИХ строк в виде массива, каждая строка - массив значений - - - - - - - - - - - -
 * 
 * Core::getMysql()->getValues("SELECT user_id, user_name FROM users LIMIT 2")
 * 
 * Пример 
 *    Array
 *    (
 *      [0] => Array
 *             (
 *               [user_id] => 1 
 *               [user_name] => vasya 
 *             )
 *      [1] => Array
 *             (
 *               [user_id] => 2 
 *               [user_name] => petya 
 *             )
 *    )
 * 
 * 
 * 3. Получение ОДНОЙ строки в виде массива (в конце функции параметр TRUE) - - - - - - - - - - - - - - - 
 * 
 * Core::getMysql()->getValues("SELECT user_id, user_name FROM users LIMIT 1", TRUE)
 * 
 * Пример
 *    Array
 *    (
 *      [user_id] => 1 
 *      [user_name] => vasya 
 *    )
 * 
 *  
 * 4. Получение одного поля из множества строк - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 
 * Core::getMysql()->getValuesAsOneArray("SELECT user_name FROM users LIMIT 5")
 * 
 * Пример 
 *    Array
 *    (
 *      [0] => vasya
 *      [1] => petya
 *      [2] => kolya
 *      [3] => misha
 *      [4] => jenya
 *    )
 * 
 *  
 * 5. Вставка данных  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 * 
 * $last_id = Core::getMysql()->query("INSERT INTO users SET (?)", array(
 *   'user_id' => 1,
 *   'user_name' => 'vasya',
 * ));
 * 
 *  
 * 6. Обновление данных - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 
 * Core::getMysql()->query("UPDATE users SET (?) WHERE user_id = 1", array(
 *   'user_name' => 'petya',
 * ));
 * 
 * 7. Мульти запрос - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * 
 * Core::getMysql()->multiQuery("
 *   INSERT INTO ... ;
 *   INSERT INTO ... ;
 *   ... ;
 *   INSERT INTO ... ;
 * ");
 * 
 */

class Core_Model_Mysql
{
    /**
     * Текущие данные соединения
     */
    private $connection_information = array();
    
    /**
     * Если true дебаг включен.
     * При этом записывается полная история запросов с данными о времени выполнения.
     */
    private $debug = null; 
    public $debug_total = null; 
    public $debug_data = null; 
    private $query_index = null; 
    
    /**
     * Если указан путь к mysql то можно использовать функцию queryList()
     */
    private $mysqli_path = null;
    private $query_list = array();
    
    /**
     * Переменные функциональности вставки данный способом загрузки файла на сервер
     */
    private $load_data = array();
    private $load_data_options = array();
    
    /**
     * Ошибки
     */
    public $last_error = null;
    public $last_errno = null;
    
    
    /**
     * Текущее соединение
     */
    private $_link = null;
    
    
    function  __construct($debug = null)
    {
      global $mysql_conndata, $mysql_conndata_id;
      
      $this->debug = $debug;
      
      if (!defined('DB_CONNECTED'))
      {
        #foreach ($mysql_conndata as $key => $value)
        {
          if (strlen(DB_HOST) > 0 && strlen(DB_USER) > 0 && ($this->_link = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD)) !== false)
          {
            #$this->connection_information = array(
            #  'host' => $value['host'],
            #  'user' => $value['user'],
            #  'password' => $value['pass'],
            #  'db_name' => $value['name'],
            #);
            #$mysql_conndata_id = $key;
            mysqli_select_db($this->_link, DB_NAME);
            define('DB_CONNECTED', true);
            #  break;
          }
        }
      }
    }
    
    
    /**
     * Возвращает значение одного первого поля из запроса.
     *
     * @param string $query
     * @return mixed
     */
    public function getValue($query)
    {
      $this->last_error = null;
      $this->last_errno = null;
      
      $res = $this->_query($query);
      
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $start_time = microtime(true);
      }
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)))
        $this->error($query);
        
      //--- получаем результат  
      $this->last_error = null;
      $this->last_errno = null;
      
      $rez = mysqli_fetch_row($res);
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)))
        $this->error($query);
        
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $end_time = microtime(true);
        
        $time_diff = sprintf('%.4f', $end_time - $start_time);
        
        $hours = floor($time_diff / 3600);
        $minutes = floor(($time_diff % 3600) / 60);
        $seconds = ($time_diff % 3600 % 60);
        
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($query)])-1]['Fetch time'] = sprintf('%02d:%02d:%02d (%.4f)', $hours, $minutes, $seconds, $time_diff);
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($query)])-1]['Fetch length'] = strlen(serialize($rez[0]));
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($query)])-1]['Fetch result'] = $rez[0];
      }      
      
      return $rez[0];
    }
    
    
    /**
     * Возвращает массив результата. Каждый его елемент - массив полей запроса.
     *
     * @param string $query
     * @return array
     */
    public function getValues($query, $oneRow = false)
    {
      if (is_array($query))
        $query = $this->buildQuery($query);            
      
      $this->last_error = null;
      $this->last_errno = null;
        
      $result = array();
      $res = $this->_query(($prepared_query = $this->prepareQuery($query)));
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)))
        $this->error($query);
        
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $start_time = microtime(true);
      }
      
      while (($rez = mysqli_fetch_assoc($res)))
      {
        foreach ($rez as $key => $value)
          $rez[$key] = stripslashes($value); 
        
        $result[] = $rez;      
      }
      
      $this->last_error = null;
      $this->last_errno = null;
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)))
        $this->error($query);
      
      if ($oneRow && isset($result[0]))
        $result = $result[0];
        
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $end_time = microtime(true);
        
        $time_diff = sprintf('%.4f', $end_time - $start_time);
        
        $hours = floor($time_diff / 3600);
        $minutes = floor(($time_diff % 3600) / 60);
        $seconds = ($time_diff % 3600 % 60);
        
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($prepared_query)])-1]['Fetch time'] = sprintf('%02d:%02d:%02d (%.4f)', $hours, $minutes, $seconds, $time_diff);
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($prepared_query)])-1]['Fetch length'] = strlen(serialize($result));
        $this->debug_data[md5($query)][sizeof($this->debug_data[md5($prepared_query)])-1]['Fetch result'] = strlen(serialize($result)) > 250 ? '..big' : $result;
      }      
        
      return $result;    
    }
    
    /**
     * Возвращает все записи из таблицы
     *
     * @param string $from - таблица 
     * @return array
     */
    public function getValuesAll($from)
    {
      $result = (array)$this->getValues("SELECT * FROM $from");        
      
      return $result;    
    }
    
    /**
     * Возвращает массив результата. Каждый его елемент - массив полей запроса.
     *
     * @param string $query
     * @return array
     */
    public function getValuesReverse($query, $oneRow = false)
    {
      return array_reverse($this->getValues($query, $oneRow));      
    }
    
    
    /**
     * Возвращает результат в одномерном массиве. 
     * Eсли во втором уровне тоже масив - его значения обьеденяются в строку разделяясь сепаратором.
     *
     * @param string $query
     * @param string $separator 
     * @return array  
     */
    public function getValuesAsOneArray($query, $separator = null)
    {
      if (is_null($separator))
        $separator = ',';
        
      $result = $this->getValues($query);
      foreach ($result as &$value) 
      {
        if (is_array($value))
          $value = implode($separator, $value);     
      }
      
      return $result;  
    }
    
    public function getValuesAsString($query, $separator_outer = null, $separator_inner = null)
    {
      $auto_change_separator = is_null($separator_inner) && is_null($separator_outer) ? TRUE : FALSE; 
      
      if (is_null($separator_outer))
        $separator_outer = "\r\n";
      if (is_null($separator_inner))
        $separator_inner = ',';
        
      $result = $this->getValues($query);
      
      foreach ($result as &$outerValue) 
      {
        if ($auto_change_separator && (is_array($outerValue) == false || (is_array($outerValue) && sizeof($outerValue) == 1)))
          $separator_outer = $separator_inner;
          
        if (is_array($outerValue))
        {
          $outerValue = trim(implode($separator_inner, $outerValue));
        }     
      }
      
      $result = trim(implode($separator_outer, $result));
      return $result;    
    }
    
    
    /**
     * Выполняет запрос и возвращает ID транзакции
     * Подавляет ошибки если указан параметр $suppress_error
     *
     * @param string $query
     * @param array $values
     * @param bool $suppress_error
     * 
     * @return integer
     */
    public function query($query, $values = null, $suppress_error = false)
    {
      if (is_array($values))
        $values = $this->asQueryString($values);
              
      if ($values)
      {
        if (strpos($query, $target = '(?)') === false)
          $target = '?';
        $query = str_replace($target, $values, $query);
      }
      
      $this->last_error = null;
      $this->last_errno = null;

      $this->_query($query);
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)) && !$suppress_error)
        $this->error($query);
        
      return !$this->last_error && !$this->last_errno ? mysqli_insert_id($this->_link) : false;
    }
    

    /**
     * Выполняет запрос
     * Подавляет ошибки если указан параметр $suppress_error
     *
     * @param string $query
     * @param bool $suppress_error
     * 
     * @return bool
     */
    public function multiQuery($query, $suppress_error = false)
    {
      $this->last_error = null;
      $this->last_errno = null;
      
      $this->_query($query, true);
      
      if (($this->last_error = mysqli_error($this->_link)) && ($this->last_errno = mysqli_errno($this->_link)) && !$suppress_error)
        $this->error($query);
        
      return !$this->last_error;  
    }
    
    
    /**
     * Возвращает последнюю шибку 
     */
    public function getLastError()
    {
      return $this->last_error;      
    }
    
    
    /**
     * Возвращает последнюю шибку 
     */
    public function getLastErrorNo()
    {
      return $this->last_errno;      
    }
    

    /**
     * Добавляет данные в очередь для вставки способом загрузки файла
     *
     * @param string $table_name
     * @param array $values
     * @return Mysql
     */
    public function addInsertQueryForLoad($table_name, $values, $options = null)
    {
      if (!isset($this->load_data_options[$table_name]['columns']) || !$this->load_data_options[$table_name]['columns'])
      {
        $columns = array();
        
        foreach (array_keys($values) as $column) 
          $columns[] = "`$column`";
          
        $this->load_data_options[$table_name]['columns'] = implode(',',$columns);
      }
      
      if (!is_null($options))
        $this->load_data_options[$table_name]['options'] = $options; 
      
      foreach ($values as $key => $value) 
        $values[$key] = '"'.$value.'"';
              
      $this->load_data[$table_name][] = implode(',',$values);;
      
      return $this;
    }
      
    /**
     * Выполняет запрос способом загрузки файла
     *
     * @return Mysql
     */
    public function execInsertQueryLoad()
    {
      foreach ($this->load_data as $table_name => $lines) 
      {
        $options = null;
        
        $file_name = str_replace('\\','/',dirname(__FILE__)) .'/'. md5(time()*mt_rand()) . '.txt';
        $data = implode(';',$lines);
        file_put_contents($file_name, $data);
        
        if (isset($this->load_data_options[$table_name]['options']) && $this->load_data_options[$table_name]['options'])
          $options = $this->load_data_options[$table_name]['options']; 
          
        $this->query("ALTER TABLE $table_name DISABLE KEYS");
        
        $this->_query("
          LOAD DATA 
          INFILE '{$file_name}'
          {$options} 
          INTO TABLE {$table_name} 
          FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY ';'
          ({$this->load_data_options[$table_name]['columns']})            
        ");  
          
        $this->query("ALTER TABLE $table_name ENABLE KEYS");
        
        unlink($file_name);
        unset($this->load_data[$table_name], $this->load_data_options[$table_name]);
      }      
      return $this;
    }
      
    private function error($query)
    { 
      /*
      Core::alert('fredeveloper@gmail.com', 'Mysql Error', array(
        'query' => $query,
        'code' => $this->getLastErrorNo(),
        'error' => $this->getLastError(),
        'debug_backtrace' => debug_backtrace(),
        'debug_data' => $this->getDebugData(),
      ), 1, true);
      
      if (System::is_test_mode() || isset($_GET['stop_on_mysql_error']))
      */
      if (Core::isTestMode() || isset($_GET['stop_on_mysql_error']))
      {
        echo 'Mysql Error.<br>'."\r\n";
        echo 'Query: '.$query.'<br>'."\r\n";
        echo 'Code: '.$this->getLastErrorNo().'<br>'."\r\n";
        echo 'Error: '.$this->getLastError().'<br>'."\r\n";
        echo '-----------------------------------------------------------------------------------'."\n";
        ?><pre><? print_r(debug_backtrace()) ?></pre><?    
        ?><pre><b>Debug Data:</b></pre><?
        ?><pre><? print_r($this->getDebugData()) ?></pre><?php
        
        die('STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED - STOPED');    
      }
    }
    
    private function buildQuery($queryArray)
    {
      if (isset($queryArray['join']))
      {
        foreach ($queryArray['join'] as &$value) 
        {
          $value = 'LEFT JOIN ' . $value;        
        }
      }
      else $queryArray['join'] = array();
      
      $result = implode(' '."\n",array(
        'SELECT ' . implode(COMMA,  $queryArray['select']),
        'FROM '   . implode(COMMA,  $queryArray['from']),
                    implode(' '."\n",  $queryArray['join']),
        'WHERE '  . implode(' and ',$queryArray['where']),
      ));    
      
      return $result;     
    }
    
    /**
     * Из массива данных делает валидную для mysql запроса строку.
     * Строка вида `key` = 'value', ...
     * Если задан параметр $keys то его значения подставляются вместо ключей массива с данными
     * 
     * @param  array  $data
     * @param  array  $keys
     * @return string 
     */
    public function asQueryString($data, $separator = ',', $keys = null)
    {
      $temp = array();
  
      if (is_null($keys) == false)
      {
        if (sizeof($data) != sizeof($keys))
          die('function asQueryString. sizeof($data) != sizeof($keys)');
        $data = array_combine($this->trim($keys), $data);
      }
      
      foreach ($data as $key => $value) 
      {
        if (is_null($value))
        {
          $temp[] = "`$key` = null";
        }
        elseif (preg_match('/^[\w\d]+\(\)$/i', trim($value)))
        {
          $temp[] = "`$key` = $value";
        }
        else
        {
          if (is_array($value))
            $value = serialize($value);
          
          $temp[] = "`$key` = '".mysqli_real_escape_string($this->_link, $value)."'";  
        }
      }
      
      return implode($separator, $temp);
    }
    
    public function asObject($data)
    {
      $object = clone $this;
      $object->setData($data);      
      return $object;      
    }
    
    public function connect()
    {
      global $mosConfig_db, $mosConfig_host, $mosConfig_user, $mosConfig_password;
      
      #@mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
      #if (mysqli_errno() > 0) die();

      #@mysqli_select_db($mosConfig_db);
      #if (mysqli_errno() > 0) die();
    }
    
    private function prepareQuery($query)
    {
      //todo:временная версия
      return $query;      
    }
    
    /**
     * Выполняет запрос
     * 
     * @param string $query
     * @param bool $multi_query
     * 
     * @return mixed
     */
    protected function _query($query, $multi_query = false)
    {
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $start_time = microtime(true);
      }
      
      /*
      if (Core::registry('mysql_debug_log'))
      {
        $log = (array)Core::registry('mysql_debug_log_data');
        $log[] = $query;
        Core::register('mysql_debug_log_data', $log);
      }
      */
      
      /*
      */
      if (isset($_GET['qwe']))
      {
        if (!$this->_link)
        {
          ?><pre><?php print_r(debug_backtrace()) ?></pre><?php
        }
      }
      
      if ($multi_query)
      {
        if (($result = mysqli_multi_query($this->_link, $query)))
        {
          do {
          } while (mysqli_more_results($this->_link) && mysqli_next_result($this->_link));
        }
      }
      else
      {
        $result = mysqli_query($this->_link, $query);
      }
      
      if ($this->debug || Core::registry('mysql_debug_log'))
      {
        $end_time = microtime(true);
        
        //--- тотал
        {
          $time_diff = ((double)$this->debug_total['time'] + ($end_time - $start_time));
          
          $hours = floor($time_diff / 3600);
          $minutes = floor(($time_diff % 3600) / 60);
          $seconds = ($time_diff % 3600 % 60);
          
          $this->debug_total = array(
            'time' => $time_diff,
            'Duration' => sprintf('%02d:%02d:%02d (%.4f)', $hours, $minutes, $seconds, $time_diff),
          );
        }
        
        $time_diff = sprintf('%.4f', $end_time - $start_time);
        
        $hours = floor($time_diff / 3600);
        $minutes = floor(($time_diff % 3600) / 60);
        $seconds = ($time_diff % 3600 % 60);
        
        $backtrace = array();
        
        foreach ((array)debug_backtrace() as $trace) 
          $backtrace[] = $trace['file'] .' (line: '.$trace['line'].')';
        
        $this->debug_data[md5($query)][] = array(
          'Duration' => sprintf('%02d:%02d:%02d (%.4f)', $hours, $minutes, $seconds, $time_diff),
          'Time' => date("H:i:s", time()),
          'Query length' => strlen($query),
          'Query' => $query,    
          'Index' => (int)($this->query_index++), 
          'Backtrace' => $backtrace, 
        );
      }
      
      return $result;     
    }
    
    
    /**
     * Возвращает данные дебага
     *
     * @return array
     */
    public function getDebugData($min_duration = null)
    {
      $result = $this->debug_data;
      if ($min_duration > 0)
      {
        foreach ($result as $key => $value) 
        {
        	if ($value['Duration'] < $min_duration)
            unset($result[$key]);
        }                
      }
      return $result;
    }
    
    
    /**
     * Очищает историю дебага
     * 
     * @return Mysql
     */
    public function debugReset()
    {
      $this->debug_data = array();
      return $this;      
    }
    

    /**
     * Добавляет к списку запрос
     *
     * @param string $query
     * @param array $values
     */
    public function addQuery($query, $values = null)
    {
      if (is_array($values))
        $values = $this->asQueryString($values);
      
      if ($values)
      {
        if (strpos($query, $target = '(?)') === false)
          $target = '?';
        $query = str_replace($target, $values, $query);
      }
      
      $this->query_list[] = $query; 
    }
    
    
    /**
     * Выполняет заранее подготовленный список запросов 
     * И возвращает результат true или текст ошибки
     *
     * @param mixed $lock_tables
     * @return mixed
     */
    public function execQueryList($lock_tables = array())
    {
      $query = '';
      
      if (is_string($lock_tables))
        $lock_tables = explode(',', $lock_tables);

      $temp = array('a'=>array(),'l'=>array());  
      foreach ($lock_tables as $lock_table) 
      {
        $temp['l'][] = '`'.$lock_table.'` WRITE'; 
        $temp['a'][] = '/*!40000 ALTER TABLE `'.$lock_table.'` DISABLE KEYS */';
      }
      if ($temp['l'])
        $query .= 'LOCK TABLES ' . implode(',',$temp['l']) . ';' . "\r\n";
      if ($temp['a'])
        $query .=  implode(';' . "\r\n", $temp['a']) . ';' . "\r\n";
      
      $file_name = dirname(__FILE__) .'/'. md5(time()*mt_rand()) .'.sql';
      $query .= implode(';'."\r\n", $this->query_list) . ';' . "\r\n"; 
      
      foreach ($lock_tables as $lock_table) 
      {
        $query .= '/*!40000 ALTER TABLE `'.$lock_table.'` ENABLE KEYS */;' . "\r\n";
      }
      
      $query .= 'UNLOCK TABLES;' . "\r\n";
      
      file_put_contents($file_name, $query);
      
      if (!$this->mysqli_path)
      {
        foreach ($this->query_list as $query) 
          $this->query($query);        
      }
      else 
      {
        $return_val = null;
        
        system($command = '"C:\Program Files\MySQL\MySQL Server 5.1\bin\mysql.exe" -D trade_study_test -u www < ' . $file_name, $return_val);
        
        if ($return_val)
        {
          ?><b>Command:</b> <pre><? print_r($command) ?></pre><?php
          ?><pre><? print_r($return_val) ?></pre><?php
          die('return val != 0');
        }
        
        unlink($file_name);
      }
      
      $this->query_list = array();
    }
    
    
    /**
     * Указывает путь к mysql
     *
     * @param string $path
     * @return Mysql
     */
    public function setMysqlPath($path)
    {
      if (file_exists($path))  
        $this->mysqli_path = $path;
        
      return $this;
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
    
    
    /**
     * Экранирует специальные символы в строках для использования в выражениях SQL
     *
     * @param string $string
     * @return string
     */
    public function escapeString($string)
    {
      return mysqli_real_escape_string($this->_link, $string);
    }
    
    
    /**
     * Возвращает true если таблица заблокирована (LOCK TABLE .. WRITE)
     *
     * @param string $table
     */
    public function isLockedTable($table)
    {
      $data = Core::getMysql()->getValues("SHOW OPEN TABLES WHERE `Table` = '{$table}'", true);
      
      return $data['In_use'] > 0;
    }
  
  
  /**
   * Возвращает данные о подключении к БД
   *
   * @return array
   */
    public function getConnectionInformation()
    {
        return $this->connection_information;
    }
}

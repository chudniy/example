<?php
  
  class Registry
  {
    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry = array();
    
    
    /**
     * Выберает данные из глобальной сферы
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public static function getData($key = null, $default = null)
    {
      $result = $default;
      
      if ($key && isset(self::$_registry[$key]))
        $result = self::$_registry[$key];
      elseif (is_null($key))
        $result = self::$_registry[$key];
      
      return $result;
    }
    
    
    /**
     * Сохраняет данные в глобальную сферу
     *
     * @param mixed|array $key
     * @param mixed|null $value
     * @return true
     */
    public static function setData($key, $value = null)
    {
      if (is_array($key))
      {
        self::$_registry = array_merge(self::$_registry, $key);
      }
      else
      {
        self::$_registry[$key] = $value;
      }
      
      return true;
    }
  }
  
  
  
  /**
 * Главный класс
 *
 * @package  Core
 * @author   Fred Melnichuk <fredeveloper@gmail.com>
 * @version  0.0.7
 */
final class Core extends Tech_Object
{
  /**
   * Application root absolute path
   *
   * @var string
   */
  static private $_appRoot;
  
  /**
   * Application model
   *
   * @var Mage_Core_Model_App
   */
  static private $_app;
  
  /**
   * Application design package object
   *
   * @var Core_Model_Design
   */
  static private $_design;
  
  /**
   * Application layout
   *
   * @var Core_Model_Layout
   */
  static private $_layout;
  
  /**
   * Application Session
   *
   * @var Core_Model_Session
   */
  static private $_session;
  
  /**
   * Application Session
   *
   * @var Core_Model_Abstract
   */
  static private $_block;
  
  /**
   * Application Locale
   *
   * @var Core_Model_Locale
   */
  static private $_locale;
  
  /**
   * Application Translate
   *
   * @var Core_Model_Translate
   */
  static private $_translate;
  
  /**
   * Application configuration object
   *
   * @var Core_Model_Config
   */
  static private $_config;
  
  /**
   * Frontend User
   *
   * @var User_Model_CurrentUser
   */
  static private $_current_user;
  
  /**
   * Request object
   *
   * @var Core_Controller_Request_Http
   */
  static private $_request;
  
  /**
   * Application front controller
   *
   * @var Core_Controller_Front
   */
  static private $_frontController;
  
  /**
   * Response object
   *
   * @var Core_Controller_Response_Http
   */
  static private $_response;
  
  /**
   * Router object
   *
   * @var Core_Controller_Router_Standard
   */
  static private $_router;
  
  /**
   * Cms/String object
   *
   * @var Cms_Model_String
   */
  static private $_string;
  
  
  /**
   * Возвращает класс для работы с базой данных
   *
   * @return Core_Model_Mysql
   */
  static public function getMysql()
  {
    if (is_null($data = Registry::getData('core/mysql'))) {
      $data = new Core_Model_Mysql();
      Registry::setData('core/mysql', $data);
    }
    
    return $data;
  }
  
  
  
  /**
   * Возвращает класс всяких полезностей
   *
   * @return Tech_Model_Tech
   */
  static public function getTech()
  {
    if (is_null($data = Registry::getData('tech/tech'))) {
      $data = new Tech_Model_Tech();
      Registry::setData('tech/tech', $data);
    }
    
    return $data;
  }
  
  
  /**
   * Возвращает класс для работы с HTML
   *
   * @return Tech_Model_Html
   */
  static public function getHtml()
  {
    if (is_null($data = Registry::getData('tech/html'))) {
      $data = new Tech_Model_Html();
      Registry::setData('tech/html', $data);
    }
    
    return $data;
  }
  
  /**
   * Сохраняет данные в глобальную сферу
   *
   * @param mixed|array $key
   * @param mixed|null $value
   *
   * @return true
   */
  static public function register($key, $value = null)
  {
    Registry::setData($key, $value);
    
    return true;
  }
  
  
  /**
   * Выберает данные из глобальной сферы
   *
   * @param mixed $key
   * @param mixed $default
   *
   * @return mixed
   */
  static public function registry($key = null)
  {
    return Registry::getData($key);
  }
  
  
  /**
   * На локальной ли машине запущен сайт
   *
   * @return boolean
   */
  static public function isTestMode()
  {
    return true;
  }
  
}

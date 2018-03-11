<?
/**
 * Парсинг HTML
 *
 * @package  Tech
 * @author   Fred Melnichuk <fredeveloper@gmail.com>
 * @version  0.0.2
 */

class Tech_Model_Html extends Tech_Object    
{
    /**
     * Возвращает форму из HTML
     *
     */
    public function getForm($needle, $content)
    {
      return $this->getHtmlEntity($needle, $content, 'form');
    }
    

    /**
     * Возвращает HTML объект.
     *
     * @param string $needle
     * @param string $content
     * @param string $type
     * 
     * @return string
     */
    public function getHtmlEntity($needle, $content, $type)
    {
      //--- находим поля формы для поиска
      $match = array();
      $result = null;
      
      $content = str_ireplace("\r", '', $content);
      $content = str_ireplace("\n", '', $content);
      $content = str_ireplace("{$type}>", "{$type}>\r\n", $content);
                
      if (preg_match('/<'.$type.'[^>]*'.$needle.'[^>]*>(?:.*)<\/[\s]*'.$type.'>/i', $content, $match))
      {
        $result = $match[0];        
      }
      
      return $result;
    }
    
  
    /**
     * @param string $text
     * @return array
     */
    public function parseVars($text)
    {
      $match = array();
      $vars = array();
      
      if (!preg_match_all('/([\w]+)[\s]*=[\s]*["]+([^"]*)["]+/i', $text, $match))
        preg_match_all('/([\w]+)[\s]*=[\s]*[\']+([^\']*)[\']+/i', $text, $match);
        
      foreach (array_keys($match[0]) as $key) 
        $vars[$match[1][$key]] = $match[2][$key];
        
      return $vars;    
    }
    
    
    /**
     * @return array
     */
    public function parseFormInputs($content, $with_name_only = false)
    {
      $inputs = array();
      $match = array();
      
      $content = str_replace("\r", '', $content);
      $content = str_replace("\n", '', $content);
      $content = preg_replace('/>/i', ">\r\n", $content);
      $content = preg_replace('/</i', "\r\n<", $content);
      
      if (preg_match_all('/<input(.*)>/i', $content, $match))
      {
        foreach ($match[0] as $input) 
        {
          if (($_input = $this->parseVars($input)))
          {
            if ($with_name_only == false || ($with_name_only && isset($_input['name']) && $_input['name']))
              $inputs[@$_input['name']] = $_input;
          }
        }
      }

      return $inputs;
    }  
}


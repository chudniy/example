<?
/**
 * ������ � �������� ��������.
 *
 * @package  Tech
 * @author   Fred Melnichuk <fredeveloper@gmail.com>
 * @version  0.0.3
 */

class Tech_Model_Filesystem extends Tech_Model_Tech    
{
  
    /**
     * ��������� ������ ������� ������.
     * � ������ ���������� ����� ������ ����� ���������� �������.
     * �������� $format ����� ���������� � ���� ������, ��� ������� ��������� �������.
     *
     * @param string|array $format
     * @return Tech_Filesystem
     */
    public function setFilterFormat($format)
    {
      if (is_array($format))
      {
        foreach ($format as $value)
          $this->setFilterFormat($value);
        return $this;       
      }
      
      $formats = explode(',', preg_replace('/[.,]+/', ',', $format));
      
      foreach ($formats as $format) 
      {
        if (strlen($format = strtolower(preg_replace('/[^\w\d]*/i', null, $format))))
          $this->addFilter('format', $format);
      }
      return $this;
    }

    /**
     * ��������� �������� ������� �� ��� �����
     *
     * @param string $filterName
     * @param mixed $value
     * @return Tech_Filesystem
     */
    private function addFilter($filterName, $value)
    {
      $key = 'filter_' . $filterName;
      
      if (is_null($filter = $this->getData($key)))
        $filter = array();
        
      $filter[md5(strtolower($value))] = $value;
      
      $this->setData($key, $filter);
      
      return $this;      
    }
    
    /**
     * �������� �������� ������� �� ��� �����
     *
     * @param string $filterName
     * @return mixed
     */
    private function getFilter($filterName)
    {
      $key = 'filter_' . $filterName;
      
      return $this->getData($key);
    }
    
    /**
     * ��������� ������ ���� ������.
     * � ������ ���������� ����� ������ ����� � ������ ������� ����������� ��������� ����� (��� ����� ��������).
     *
     * @param string|array $format
     * @return Tech_Filesystem
     */
    public function setFilterText($text)
    {
      if (is_array($text))
      {
        foreach ($text as $value)
          $this->setFilterText($value);
        return $this;       
      }
      
      if (strlen($text = strtolower(trim($text))))
        $this->addFilter('text', $text);
        
      return $this;
    }
    
    /**
     * ��������������� ���� �� �������� ����� ����������� ����� ������.
     *
     * @param string $path
     * @return Tech_Filesystem
     */
    public function setPath($path)
    {
      if (is_array($path))
      {
        foreach ($path as $value)
          $this->setPath($value);
        return $this;       
      }
      
      $path = $this->pregPath(trim($path));
      
      if (file_exists($path) && is_dir($path))
        $this->addFilter('path', $path);
        
      return $this;
    }
    
    /**
     * ���������� ����� �� ��������� �����.
     *
     * @return array
     */
    public function getFiles()
    {
      return $this->_getFiles($this->getPaths());
    }
    
    /**
     * ���������� ������ ������.
     * ��������� ������� � ������ ������.
     *
     * @param string|array $path
     * @return array
     */
    private function _getFiles($path)
    {
      $list = array();
      
      if (is_array($path))
      {
        foreach ($path as $value)
          $list = array_merge($list, $this->_getFiles($value));
        return $list; 
      }
      
      $list = $this->_getFileList($path);
      
      /**
       * Apply filtres
       */
      $list = $this->filterByFormat($list);
      $list = $this->filterByText($list);
      
      return $list;
    }
    
    /**
     * ���������� ����� �� ��������� ����� � �� ��������.
     *
     * @return array
     */
    public function getFilesRecursive()
    {
      $paths = $this->getPathsRecursive();
      
      return $this->_getFiles($paths);
    }
    
    /**
     * �������������� ����� �� �������
     *
     * @param array $files
     * @return array
     */
    private function filterByFormat($files)
    {
      $filter = $this->getFilter('format');
      
      if (sizeof($filter) == false)
        return $files;

      foreach ($files as $key => $file) 
      {
        if (in_array($this->getExtension($file), $filter) == false)
          unset($files[$key]);
      }
      
      return $files;     
    }
    
    /**
     * �������������� ����� �� ������
     *
     * @param array $files
     * @return array
     */
    private function filterByText($files)
    {
      $filter = $this->getFilter('text');
      
      if (sizeof($filter) == false)
        return $files;

      foreach ($files as $key => $file) 
      {
        $unset = true;
        foreach ($filter as $text) 
        {
          if (stripos(basename($file), $text) !== false)
          {
            $unset = false;
            break;
          }
        }
        
        if ($unset)
          unset($files[$key]);
      }
      
      return $files;     
    }
    
    /**
     * ���������� ����.
     *
     * @param string $path
     * @return string
     */
    public function pregPath($path)
    {
      if (is_array($path))
      {
        foreach ($path as &$value)
          $value = $this->pregPath($value);      
      }
      else
        $path = preg_replace(array('/[\\\]/','/\/$/'), array('/',null), $path);  
            
      return $path;
    }
    
    /**
     * ���������� ������ ����� ������ ��������� �������������.
     * ����� �������� ��������� ���������� ����. 
     *
     * @return array
     */
    public function getPaths()
    {
      return $this->getFilter('path');
    }
    
    /**
     * ���������� ������ ����� ������ ��������� �������������.
     * ���� ������ ��������. 
     *
     * @param string $path
     * @return array
     */
    public function getPathsRecursive($path = null)
    {
      $key = 'paths_recursive_' . md5($path);
      
      if (is_null($list = $this->getData($key)))
      {
        $list = array();  
        
        if (is_null($path))
          $path = $this->getPaths();
          
        if (is_array($path))
        {        
          foreach ($path as $value)
          { 
            $list[] = $value;
            $list = array_merge($list, $this->getPathsRecursive($value));
          }
          return $list;
        }
        
        $paths = $this->_getDirList($path); 
        
        foreach ($paths as $path) 
        {
          $list[] = $path;
          $list = array_merge($list, $this->getPathsRecursive($path));
        }
          
        $this->setData($key, $list);
      }
      return $list;
    }
    
    public function getExtension($file)
    {
      $key = 'file_extension_' . $file; 
      if (is_null($extension = $this->getData($key)))
      {
        $match = array();
        if (preg_match('/[^.]+$/i', $file, $match))
          $extension = strtolower($match[0]);
        $this->setData($key, $extension);  
      }
      return $extension;
    }
  
    public function getFormatedSize($size, $type)
    {
      switch (strtolower($type))
      {
        case  'kb': return number_format($size / 1024,2); break;
        default   : return $size; break;
      }
    }
    
    public function toPath($str)
    {
      $str = str_ireplace(array('\\',DOCUMENT_ROOT),array('/',''),trim($str));
      return str_replace('//','/',strlen($str) > 0 ? $str : '/');
    }
    public function toFull($str)
    {
      return str_replace('//','/',trim(stripos($str,DOCUMENT_ROOT) === false  ?  DOCUMENT_ROOT . '/' . trim($str)  :  $str));
    }
  
    public function getShortName($file)
    {
      return str_ireplace('.' . $this->get_extension($file), null, basename($file));
    }
  
    public function getShortNameLCase($file)
    {
      return strtolower($this->getShortName($file));
    }
    
    public function pathUp($path, $level = 1)
    {
      $last_slash   = false;
      $path         = explode('/',str_replace(array('\\','//','///'),'/',trim($path)));
      if ($path[sizeof($path)-1] == false)
      {
        unset($path[sizeof($path)-1]);
        $last_slash = true;
      }
      for ($i = 0; $i < $level; $i++)
        unset($path[sizeof($path)-1]);
  
      return implode('/',$path) . ($last_slash ? '/' : '');
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
     * ���������� ������ ������ ���� ����� �� ���������� ����. 
     *
     * @param string $path
     * @return array
     */
    private function _getDirList($path)
    {
      $key = 'dir_list_' . md5(strtolower($path));
      
      if (is_null($list = $this->getData($key)))
      {
        $list = array();
        
        if ($dir = opendir($path))
        {
          while ($file = readdir($dir))
          {
            if ($file != '.' && $file != '..')
            {
              $file = $path . '/' . $file;
              if (is_dir($file))
                $list[] = $file;
            }
          }
        }
        
        $this->setData($key, $list);
      }
      return $list;
    }
    
    /**
     * ���������� ������ ������ ���� ������ �� ���������� ����. 
     *
     * @param string $path
     * @return array
     */
    private function _getFileList($path)
    {
      $key = 'file_list_' . md5(strtolower($path));
      
      if (is_null($list = $this->getData($key)))
      {
        $list = array();
        
        if ($dir = opendir($path))
        {
          while ($file = readdir($dir))
          {
            if ($file != '.' && $file != '..')
            {
              $file = $path . '/' . $file;
              if (is_file($file))
                $list[] = $file;
            }
          }
        }
        
        $this->setData($key, $list);
      }
      return $list;
    }
    
    /**
     * ������� ������ ��������
     *
     * @return Tech_Filesystem
     */
    public function clearFilterFormat()
    {
      $this->setData('filter_format', null);
      return $this;      
    }
    
    /**
     * ������� ������ �������
     *
     * @return Tech_Filesystem
     */
    public function clearFilterText()
    {
      $this->setData('filter_text', null);
      return $this;      
    }
    
    /**
     * ������� ��� �������
     *
     * @return Tech_Filesystem
     */
    public function clearFilterAll()
    {
      $this->clearFilterFormat()->clearFilterText();
      return $this;      
    }
    
    /**
     * ������� ��� ��������� �����.
     * 
     * @return Tech_Filesystem 
     */
    public function deleteFiles()
    {
      $this->_deleteFiles($this->getFiles());
      return $this;
    }
    
    /**
     * ������� ��� ��������� �����.
     * 
     * @return Tech_Filesystem 
     */
    public function deleteFilesRecursive()
    {
      $this->_deleteFiles($this->getFilesRecursive());
      return $this;
    }
    
    /**
     * ������� ��� ��������� �����.
     * 
     * @param array $files
     * @return Tech_Filesystem 
     */
    private function _deleteFiles($files)
    {
      foreach ($files as $file)
      {
        if (is_file($file) && is_writeable($file))
          unlink($file);
      }
      return $this;
    }
}




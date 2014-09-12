<?php
namespace Controller;

/**
 * Simple wrapper for the HTTP request
 *
 * @author Fabrizio Manunta
 */
class Request
{
    /**
     *
     * @var array $_get, $_GET
     */
    private $_get;
    
    /**
     *
     * @var array $_post, $_POST
     */
    private $_post;
    
    /**
     *
     * @var array $_files, $_FILES
     */
    private $_files;
    
    /**
     *
     * @var array $_server, $_SERVER
     */
    private $_server;
    
    /**
     * 
     * Constructor
     */
    public function __construct()
    {
        $this->_post = $_POST;
        $this->_get = $_GET;
        $this->_files = $_FILES;
        $this->_server = $_SERVER;
        
    }
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getServer($key)
    {
        if(array_key_exists($key, $this->_server)) {
            
            return $this->_server[$key];
        }
    }
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        if(array_key_exists($key, $this->_get)) {
            
            return $this->_get[$key];
        }
        
    }
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getPost($key = null)
    {
        if($key === null) {
            return $this->_post;
        } 
        
        if(array_key_exists($key, $this->_post)) {
            
            return $this->_post[$key];
        }
        
    }
    
    /**
     * 
     * @param string $file
     * @return mixed
     */
    public function getFile($file)
    {
        return isset($this->_files[$file]) ? $this->_files[$file] : null;
    }
}

?>

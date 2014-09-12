<?php
namespace Controller;

/**
 * Description of AppController
 *
 * @author Fabrizio Manunta
 */
class AppController
{
    /**
     *
     * @var Request $_request
     */
    private $_request;
    
    /**
     *
     * @var array $_errors
     */
    private $_errors;
    
    /**
     * 
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
        
    }
    
    /**
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Add an erorr message
     * 
     * @param string $message
     */
    public function addError($message)
    {
        $this->_errors[] = $message;
    }
    
    /**
     * Check if any error occured
     * 
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }
    
    /**
     * Display errors
     * 
     * @return string
     */
    public function displayErrors()
    {
        $output = '';
        if(!empty($this->_errors)) {
            $output .= '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all"><ul>';
            
            foreach ($this->_errors as $error) {
                $output .= '<li>'
                        . '<span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>' 
                        .  $error . '</li>';
            }
            
            $output .= '</ul></div>';
        }
        
        return $output;
        
    }
}

?>

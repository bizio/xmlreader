<?php
namespace Application;
use Controller\Request;
use Persistence\Database;

/**
 * Description of App
 *
 * @author Fabrizio Manunta
 */
class App
{
    /**
     * Simple configuration array
     * @var array $_config
     */
    private $_config;
    
    /**
     * Creates a database instance and registers autoload
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
        require 'Autoloader/Autoloader.php';
        spl_autoload_register(array('Autoloader\\Autoloader', 'load'));
        Database::getInstance($config['database']);
    }
    
    /**
     * 
     * Parses request and returns the requested controller/view
     */
    public function run()
    {
        $request = new Request();
        $requestedRoute = trim($request->getServer('REQUEST_URI'));
        $routeParts = explode('/', $requestedRoute);
        
        $requestMethod = strtolower($request->getServer('REQUEST_METHOD'));
        
        $controllersPath = $this->_config['controllersPath'];
        $viewPaths = $this->_config['viewsPath'];
        $controllerName = empty($routeParts[1]) ? 'Index' : ucfirst($routeParts[1]);
        
        $controllerFile = $controllersPath . DIRECTORY_SEPARATOR . $controllerName . '.php';
        
        $viewFile = $viewPaths . DIRECTORY_SEPARATOR . strtolower($controllerName) . '.phtml';
        
        if(file_exists($controllerFile)) {
            include $controllerFile;
            
            try {
                
                $controller = new $controllerName($request);
                $controller->{$requestMethod}();
                
                if(file_exists($viewFile)) {
                    
                    $content = file_get_contents($viewFile);
                    echo '<!DOCTYPE HTML>
                            <head>
                                <meta http-equiv="content-type" content="text/html; charset=utf-8">
                                <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css">
                                <link rel="stylesheet" href="/css/styles.css">
                            </head>
                            <body>' . $controller->displayErrors() . $content . '</body>
                          </html>';
                    
                } else {
                    die(sprintf('View not found in %s', $viewFile));
                }
                
            } catch (\Exception $e) {
                // @todo handle exception
                die($e->getMessage());
            }
            
        } else {
            header('HTTP/1.0 404 Not Found');
            die('Document not found');
        }
        
    }
    
    
}

?>

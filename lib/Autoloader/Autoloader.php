<?php

namespace Autoloader;

/**
 * Basic autoloader
 *
 * @author Fabrizio Manunta
 */
class Autoloader
{
    /**
     * Try to load the specified class resolving namespaces
     * 
     * @param string $className
     */
    public static function load($className)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        include_once $classPath;
    }

}

?>

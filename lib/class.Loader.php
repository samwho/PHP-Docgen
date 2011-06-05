<?php

/**
 * This class implements automatic class loading for the project. Every time
 * a class is requested but not found, the load() method of this class is
 * called that knows where to look for the file that contains that class.
 */
class Loader {

    /**
     * An array of paths to search. These will be prefixed with
     * the path to this project's root directory (1 directory up
     * from this file).
     *
     * @var array
     */
    private static $paths = array(
        'lib/'
    );

    /**
     * Registers the autoloading function with PHP. Only call this once
     * at the start of a page.
     *
     * @return bool Returns the result from the call to spl_autoload_register
     */
    public static function register() {
        return spl_autoload_register(array(__CLASS__, 'load' ));
    }

    /**
     * Unregisters the autoloading function with PHP.
     * *
     * @return bool Returns the result from the call to spl_autoload_unregister
     */

    public static function unregister() {
        return spl_autoload_unregister(array(__CLASS__, 'load'));
    }

    /**
     * Loads the file that contains the passed in $class name.
     *
     * @param string $class The name of the class that is being searched for.
     */
    private static function load($class) {
        $project_path = dirname(__FILE__) . '/../';

        foreach (self::$paths as $path) {
            $path_to_class = $project_path . $path . 'class.' . $class . '.php';

            if (file_exists($path_to_class)) {
                include_once $path_to_class;
                return;
            }
        }
    }
}

Loader::register();

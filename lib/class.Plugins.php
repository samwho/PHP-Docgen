<?php

class Plugins {
    private static $loaded_plugins = array();

    /**
     * Returns the directory that docgen looks for plugins in.
     */
    public static function directory() {
        return dirname(__FILE__) . '/../plugin/';
    }

    /**
     * Loads all files ending in .php in the plugins directory.
     */
    public static function loadAll() {
        foreach(glob(self::directory() . '*.php') as $file) {
            if (!in_array($file, get_included_files())) {
                require $file;
                self::$loaded_plugins[] = $file;
            }
        }
    }

    /**
     * Loads a plugin by its file name. This function just requires a file by
     * prepending the plugin directory and appending .php to the argument
     * passed in.
     *
     * Example:
     *
     * $plugin_dir . $file . '.php'
     *
     * The file name does not need a leading forward slash.
     *
     * @param string $name Plugin file name without path or extension.
     */
    public static function load($name) {
        $file = self::directory() . $name . '.php';

        if (!in_array($file, get_included_files())) {
            require $file;
            self::$loaded_plugins[] = $file;
        }
    }

    /**
     * Returns an array of plugin file paths that have been loaded into the
     * application.
     *
     * @return array $loaded_plugins File paths of plugins that have been loaded.
     */
    public static function getLoadedPlugins() {
        return self::$loaded_plugins;
    }
}

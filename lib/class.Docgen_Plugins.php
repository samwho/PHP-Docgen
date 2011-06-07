<?php

class Docgen_Plugins {
    private static $loaded_plugins = array();

    /**
     * Returns the directory that docgen looks for plugins in.
     */
    public static function directory() {
        return dirname(__FILE__) . '/../plugin/';
    }

    /**
     * Loads all files ending in .php in the plugins directory.
     *
     * When it is finished, the 'plugins_loaded' hook is called with no
     * arguments.
     */
    public static function loadAll() {
        foreach(glob(self::directory() . '*.php') as $file) {
            self::load(basename($file));
        }

        // Fire a hook that signifies all plugins have been loaded.
        Docgen_Hooks::call('plugins_loaded');
    }

    /**
     * Loads a plugin by its file name. This function just requires a file by
     * prepending the plugin directory to the argument passed in.
     *
     * Example:
     *
     * $plugin_dir . $file
     *
     * The file name does not need a leading forward slash.
     *
     * @param string $name Plugin file name without path.
     */
    public static function load($name) {
        $file = self::directory() . $name;

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

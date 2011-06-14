<?php

class Docgen_Plugins {
    private static $loaded_plugins = array();

    /**
     * Returns the directory that docgen looks for plugins in.
     */
    public static function directory() {
        return dirname(__FILE__) . '/../plugins/';
    }

    public static function register(Docgen_Plugin $plugin) {
        if ($plugin->checkRequirements()) {
            self::$loaded_plugins[] = $plugin;
        } else {
            trigger_error('The plugin "' . $plugin->getName() . '" did not ' .
                          'meet its requirement check. Please contact the author.');
        }
    }

    /**
     * Loads all files ending in .php in the plugins directory.
     *
     * When it is finished, the 'plugins_loaded' hook is called with no
     * arguments.
     *
     * @param bool $testing Only set this during testing. It makes the
     * class load plugins from tests/test_plugins/ instead of the normal
     * plugin/ directory.
     */
    public static function loadAll() {
        foreach(glob(self::directory() . '*/*.php') as $file) {
            self::load($file);
        }

        /*
         * Calls all of the onLoad() methods in the loaded plugins.
         */
        foreach (self::$loaded_plugins as $plugin) {
            $plugin->onLoad();
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
    public static function load($file) {
        if (!in_array($file, get_included_files())) {
            require realpath($file);
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

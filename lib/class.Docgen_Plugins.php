<?php

class Docgen_Plugins {
    private static $loaded_plugins = array();

    /**
     * Returns the directory that docgen looks for plugins in.
     */
    public static function directory() {
        return dirname(__FILE__) . '/../plugins/';
    }

    /**
     * Registers a plugin with the system. First, it checks that it passes
     * its requirements, then it adds it to the array of loaded plugins.
     *
     * If it does not meet its requirements, an error is triggered (not a
     * fatal, so the program will continue) and the method returns false.
     *
     * If the plugin is already registered, method returns false.
     */
    public static function register(Docgen_Plugin $plugin) {
        if ($plugin->checkRequirements()) {
            return self::load($plugin);
        } else {
            trigger_error('The plugin "' . $plugin->getName() . '" did not ' .
                'meet its requirement check. Please contact the author.');
            return false;
        }
    }

    /**
     * Unregisters a plugin. This only removes it from the array of plugins,
     * it does not unregister its hooks. To fully unregister a plugin, call its
     * unload() method.
     *
     * @return bool True on success, false if the plugin didn't exist in the
     * system.
     */
    public static function unregister(Docgen_Plugin $plugin) {
        $key = array_search($plugin, self::$loaded_plugins);
        if ($key !== false) {
            unset(self::$loaded_plugins[$key]);
            return true;
        } else {
            return false;
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
            require_once $file;
        }

        // Fire a hook that signifies all plugins have been loaded.
        Docgen_Hooks::call('plugins_loaded', array(self::$loaded_plugins));
    }

    public function load(Docgen_Plugin $plugin) {
        if (!in_array($plugin, self::$loaded_plugins)) {
            self::$loaded_plugins[] = $plugin;
            $plugin->onLoad();
            return true;
        } else {
            return false;
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

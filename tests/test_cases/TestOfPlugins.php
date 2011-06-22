<?php

class TestOfPlugins extends UnitTestCase {
    public function testLoadUnload() {
        $hooks = Docgen_Hooks::getAllHooks();
        $plugins = Docgen_Plugins::getLoadedPlugins();

        foreach($plugins as $plugin) {
            $plugin->unload();
        }

        $plugins_empty = Docgen_Plugins::getLoadedPlugins();
        $hooks_empty = Docgen_Hooks::getAllHooks();

        $this->assertTrue(empty($plugins_empty));
        foreach($hooks_empty as $should_be_empty) {
            $this->assertTrue(empty($should_be_empty));
        }

        // cleanup
        foreach($plugins as $plugin) {
            Docgen_Plugins::register($plugin);
        }

        $hooks_after = Docgen_Hooks::getAllHooks();
        $plugins_after = Docgen_Plugins::getLoadedPlugins();

        $this->assertEqual($plugins, $plugins_after);
        $this->assertEqual($hooks, $hooks_after);
    }

    public function testUnloadSingle() {
        $hooks = Docgen_Hooks::getAllHooks();
        $plugins = Docgen_Plugins::getLoadedPlugins();

        foreach($plugins as $plugin) {
            // Save the hooks the current plugin has registered
            $single_plugin_hooks = $plugin->getRegisteredHooks();
            // Remove the plugin from the system
            $plugin->unload();

            // Check that the plugin has been removed
            $plugins_after = Docgen_Plugins::getLoadedPlugins();
            $this->assertEqual(sizeof($plugins), sizeof($plugins_after) + 1);

            // Get the hooks that exist after the plugin has been removed
            $hooks_after = Docgen_Hooks::getAllHooks();

            // Assert that the callbacks removed are not in the system hooks
            foreach($single_plugin_hooks as $hook_name => $callbacks) {
                foreach($hooks_after[$hook_name] as $callback) {
                    $this->assertFalse(in_array($callback, $callbacks));
                }

                // Assert that the sizes of the callback arrays are correct
                $this->assertEqual(sizeof($hooks[$hook_name]), sizeof($hooks_after[$hook_name]) + sizeof($callbacks));
            }

            // Put the plugin back into the system
            Docgen_Plugins::register($plugin);
        }
    }

    public function testPluginsAreValid() {
        $plugins = Docgen_Plugins::getLoadedPlugins();

        foreach($plugins as $plugin) {
            $this->assertNotEqual($plugin->getName(), 'Anonymous Plugin',
                'A plugin still has the name "Anonymous Plugin". Please change this.');
            $this->assertNotEqual($plugin->getRegisteredHooks(), array(),
                'Plugin "'.$plugin->getName().'" has no registered hooks. Are you sure it is working correctly?');


            /*
             * Some class introspection checking for the plugins.
             */
            $class = new Docgen_ClassParser(get_class($plugin));
            $info = $class->templateInfo();
            $constructor = null;
            $onLoad = null;

            foreach($info['methods'] as $method) {
                switch ($method['name']) {
                case '__construct': $constructor = $method; break;
                case 'onLoad': $onLoad = $method; break;
                }
            }

            /*
             * Need to make sure that if they have overridden the constructor,
             * the have called the parent constructor inside it.
             */
            if (!is_null($constructor)) {
                if ($constructor['class_name'] != 'Docgen_Plugin') {
                    $parent_called = preg_match('/parent\:\:\_\_construct\(/', $constructor['source']);
                    $this->assertTrue($parent_called > 0, 'Parent constructor not called in overridden constructor for "'.$plugin->getName().'".');
                }
            }
        }
    }
}

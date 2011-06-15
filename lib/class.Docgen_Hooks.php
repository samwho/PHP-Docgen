<?php

class Docgen_Hooks {
    private static $hooks = array();

    /**
     * Returns the master array of all hooks. This is intended for testing
     * use only. Please do not modify this in any of yuor plugins.
     *
     * @return array All hooks registered.
     */
    public static function getAllHooks() {
        return self::$hooks;
    }

    /**
     * Sets the master array of all hooks to whatever parameter you pass in.
     * This is intended for testing purposes only.
     */
    public static function restoreHooks($hooks) {
        self::$hooks = $hooks;
    }

    /**
     * Add a hook to the system to be called at a later time with arguments.
     *
     * You can add multiple callbacks to the same name. This allows for the
     * plugin system to exist :)
     *
     * @param string $name The hook name to add the callback to.
     * @param callback $callback The callback to be executed when this hook
     * is called.
     */
    public static function add($name, $callback) {
        // If the hook does not exist, create it.
        if (!Docgen_Hooks::exists($name)) {
            self::$hooks[$name] = array();
        }

        // Add callback to hook.
        self::$hooks[$name][] = $callback;
    }

    /**
     * Removes hooks from the system array of hooks. The array passed in must
     * be of the same format as the internal system array, which is an array of
     * array like so:
     *
     * array(
     *     'hook_name' => array(
     *         [0] => $some_callback,
     *         [1] => $some_other_callback
     *     ),
     *     'another_hook_name' => array(
     *         [0] => $another_callback
     *     )
     * );
     */
    public static function remove(array $hooks_to_remove) {
        foreach($hooks_to_remove as $hook_name => $callbacks) {
            foreach(self::$hooks[$hook_name] as $key => $callback) {
                if (in_array($callback, $callbacks)) {
                    unset(self::$hooks[$hook_name][$key]);
                }
            }
        }
    }

    /**
     * This function calls a callback and has two behaviours depending on
     * whether or not you pass in a second parameter.
     *
     * If you pass in only the first parameter, the callbacks that are
     * associated with the name you passed in are called without any
     * arguments and the function call does not return anything.
     *
     * If you pass in a second parameter and it is not an empty array,
     * the callbacks associated with the name you passed in are called
     * and sent the array of parameters (using call_user_func_array())
     * and each time a callback is executed, the first parameter in the
     * array is replaced by the return value of the callback. The first
     * parameter in the array is then returned by the function call.
     *
     * @param string $name Hook name to call.
     * @param array $args An optional array of arguments to pass the callbacks.
     * @return mixed No return value if no args are passed. Otherwise, the
     * first element in the argument is returned (will be modified by callbacks,
     * read above).
     */
    public static function call($name, array $args = null) {
        // Get the callbacks. If none exist for the hook name, just return
        // an empty array so that the foreach's don't complain.
        $callbacks = Docgen_Hooks::exists($name) ? self::$hooks[$name] : array();

        // If there are no arguments, just execute the callbacks and return.
        if (empty($args)) {
            foreach($callbacks as $callback) {
                call_user_func($callback);
            }
            return;
        } else {
            // If there _are_ arguments, call all of the callbacks with the
            // arguments but overwrite the first one with the return value
            // each time, then return the first one from this function.
            //
            // This allows for modification of values without having to pass
            // references around.
            $keys = array_keys($args);
            $first_key = $keys[0];
            foreach($callbacks as $callback) {
                $args[$first_key] = call_user_func_array($callback, $args);
            }
            return $args[$first_key];
        }
    }

    /**
     * Check if a specific hook exists. Returns true if it does, false if
     * it does not.
     */
    public static function exists($name) {
        return isset(self::$hooks[$name]);
    }

    /**
     * Resets the internal array of registered hooks so that nothing is
     * registered any more.
     *
     * Only used for testing purposes.
     */
    public static function reset() {
        self::$hooks = array();
    }
}

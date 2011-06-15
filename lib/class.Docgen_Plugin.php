<?php
/**
 * This is the class to extend if you want to create a Docgen plugin.
 */
abstract class Docgen_Plugin {
    /**
     * @var Docgen_Log The plugin log.
     */
    protected $log;

    /**
     * Keeps a record of all of the hooks this particular plugin has
     * registered. For instrospection purposes.
     *
     * @var array
     */
    private $registered_hooks = array();

    /**
     * The name of your plugin. Please override this.
     */
    protected $name = 'Anonymous Plugin';


    /**
     * The constructor is used for initialisation of instance variables
     * that are common to all plugins.
     *
     * Please use onLoad() instead of __construct() to do initial setup
     * of your plugin.
     *
     * If you do override the constructor (not advised), please make sure
     * you call the super constructor.
     */
    public function __construct() {
        $this->log = Docgen_Log::getPluginLog();
    }

    /**
     * Add a hook into the system. This is intended mainly for internal
     * use and as an abstraction layer for plugins.
     *
     * @param string $name The name of the hook to add to.
     * @param callback $callback The callback to add.
     */
    protected function addHook($name, $callback) {
        // Add the registered hook to the array of registered hooks
        // maintained by this class. For introspection purposes later.
        if (!isset($this->registered_hooks[$name])) {
            $this->registered_hooks[$name] = array();
        }
        $this->registered_hooks[$name][] = $callback;
        Docgen_Hooks::add($name, $callback);
    }

    /**
     * Allows you to view what hooks this plugin has registered
     * in the system.
     *
     * @return array A multidimensional associative array of hooks that
     * this plugin has registered with the system. Comes back as
     * hook_name => array of callbacks.
     */
    public function getRegisteredHooks() {
        return $this->registered_hooks;
    }

    /**
     * Removes all registered hooks this plugin has in the system and
     * unregisters it. Effectively, it is totally unknown to the system
     * after a call to this method.
     *
     * If you want to load it again, just reregister it.
     */
    public function unload() {
        Docgen_Hooks::remove($this->getRegisteredHooks());
        $this->registered_hooks = array();
        Docgen_Plugins::unregister($this);
    }

    /**
     * If your plugin has any system requirements, use this function to
     * check them.
     *
     * For example, if you require cURL for your plugin to function, you
     * might override this method to look like this:
     *
     * public function checkRequirements() {
     *    if (in_array('curl', get_loaded_extensions())) {
     *        // cURL is loaded, return true
     *        return true;
     *    } else {
     *        // cURL is NOT loaded, return false
     *        return false;
     *    }
     * }
     *
     * If you return false from this method, your onLoad() method
     * will not be called. The constructor will be called, though. If
     * you put your hook definitions in your constructor instead of
     * onLoad, bad things will happen.
     *
     * This method returns true by default.
     */
    public function checkRequirements() {
        return true;
    }

    /**
     * This method is called when the plugin is being loaded.
     * Override it if you need to do any form of setup.
     *
     * It is advised to use this instead of a constructor because
     * the constructor is called when the plugin is first introduced
     * into the sytem, thus not all of the other plugins will have
     * been loaded at that time.
     *
     * If you do feel the need to use a constructor, make sure you call
     * the super constructor first.
     */
    public function onLoad() {}

    /**
     * Gets the name of the plugin. Will default to 'Anonymous Plugin' if
     * the plugin author has not defined a name for it.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * This method allows you to add a callback that manipulates the
     * Dwoo_Compiler object that the parser uses. You callback will
     * receive a single argument, the Dwoo_Compiler object, and will
     * be expected to return a Dwoo_Compiler object.
     *
     * Example method stub (what your callback should look like):
     *
     * public function myMethod(Dwoo_Compiler $compiler) {
     *     // Some code
     *
     *     return $compiler;
     * }
     */
    protected function addCompilerHook($callback) {
        $this->addHook('compiler_created', $callback);
    }

    /**
     * This method allows you to add a hook that modifies the class
     * information before it gets sent to the parser. Your callback
     * will receive a single argument that is an associative array
     * of class information and will be expected to return an associative
     * array of class information. See the Wiki for information on what
     * this array will contain.
     *
     * Example method stub:
     *
     * public function myClassInfoModifier(array $class_info) {
     *     // Code that modifies the class info in a clever way
     *
     *     return $class_info;
     * }
     */
    protected function addClassInfoHook($callback) {
        $this->addHook('class_info', $callback);
    }

    /**
     * This method allows you to add a hook that modifies the method
     * information before it gets sent to the parser. Your callback
     * will receive a single argument that is an associative array
     * of method information and will be expected to return an associative
     * array of method information. See the Wiki for information on what
     * this array will contain.
     *
     * Example method stub:
     *
     * public function myMethodInfoModifier(array $method_info) {
     *     // Code that modifies the method info in a clever way
     *
     *     return $method_info;
     * }
     */
    protected function addMethodInfoHook($callback) {
        $this->addHook('method_info', $callback);
    }

    /**
     * This method allows you to add a hook that modifies the property
     * information before it gets sent to the parser. Your callback
     * will receive a single argument that is an associative array
     * of property information and will be expected to return an associative
     * array of property information. See the Wiki for information on what
     * this array will contain.
     *
     * Example method stub:
     *
     * public function myPropertyInfoModifier(array $property_info) {
     *     // Code that modifies the property info in a clever way
     *
     *     return $property_info;
     * }
     */
    protected function addPropertyInfoHook($callback) {
        $this->addHook('property_info', $callback);
    }

    /**
     * This method allows you to execute code after a call to the
     * loadClasses() method of the Docgen_Parser class has been
     * made.
     *
     * The callback will not receive any arguments and will not be
     * expected to return anything.
     */
    protected function addClassesLoadedHook($callback) {
        $this->addHook('classes_loaded', $callback);
    }

    /**
     * When classes are being saved to file using the parseClass()
     * method of the Docgen_Parser class, the file name is passed
     * through callbacks added to this hook first. This allows
     * you to modify the file name if you want.
     *
     * Your callback will be passed two arguments: The file name
     * and the the template information about to be passed to
     * the template parser (for classes, this will be a class info
     * array). Your callback will be expected to return the new
     * filename.
     *
     * Example method stub:
     *
     * public function myFileNameModifier($filename, $template_info) {
     *     // code to modify the $filename in a clever way
     *
     *     return $filename;
     * }
     */
    protected function addFileNameHook($callback) {
        $this->addHook('file_name', $callback);
    }

    /**
     * This method allows you to access the complete array of information
     * when it is generated by a call to getAllClassInfo() in the Docgen_Parser
     * class.
     *
     * Your callback will receive a single array of all of the template
     * information gathered by getAllClassInfo and it will be expected
     * to return that array after it has been modified.
     *
     * Example method stub:
     *
     * public function myAllClassInfoModifier(array $all_class_info) {
     *     // Some sweet code that does cool stuff to $all_class_info
     *
     *     return $all_class_info;
     * }
     */
    protected function addAllClassInfoHook($callback) {
        $this->addHook('all_class_info', $callback);
    }
}

<?php
// Require the Dwoo autoloader. This is the only class that will use Dwoo.
require_once dirname(__FILE__) . '/../extlib/dwoo/dwooAutoload.php';

class Parser {

    /**
     * @var array
     */
    private $class_list;

    /**
     * What's a Dwoo I hear you say? It's a templating engine that I'm
     * trying out for this project :)
     *
     * @var Dwoo
     */
    private $dwoo;

    /**
     * A Dwoo compiler. More info here: http://wiki.dwoo.org/index.php/Dwoo_Compiler
     *
     * @var Dwoo_Compiler
     */
    private $dwoo_compiler;

    /**
     * @var array An array of callbacks.
     */
    private static $class_filters = array();

    /**
     * @var array An array of callbacks.
     */
    private static $class_list_filters = array();

    /**
     * @var array An array of compiler hooks.
     */
    private static $compiler_hooks = array();

    /**
     * @var array An array of file name filter callbacks.
     */
    private static $file_name_filters = array();

    /**
     * Hook a class filter to the Parser. This allows you to edit class information
     * before it gets sent to the template parser.
     *
     * For every class that gets parsed, your callback function will get sent the
     * associative array of information that represents it and the return value
     * from your callback will be the new template data. Because of this, it is
     * imperative that you return something usable for your selected template.
     *
     * @param callback $callback A callback that will eventually get passed in to
     * the call_user_func method.
     */
    public static function addClassFilter($callback) {
        self::$class_filters[] = $callback;
    }

    /**
     * Hook a class list filter to the Parser. What this does is, before classes
     * are actually loaded, gives whatever callback you specify the list of classes
     * and sets the list to whatever you return.
     *
     * This gives you the power to add in files or remove files that you may or may
     * not want in the list.

     * @param callback $callback A callback that will eventually get passed in to
     * the call_user_func method.
     */
    public static function addClassListFilter($callback) {
        self::$class_list_filters[] = $callback;
    }

    /**
     * Hook a compiler modification into the Parser. Dwoo has a Dwoo_Compiler
     * class that allows you to set custom pre/post-processors and edit how
     * the templates are compiled.
     *
     * This will pass the Dwoo_Compiler object to your callback and set the
     * compiler to the result of your callback. This means after any modifications
     * you do to the compiler, be sure to return the compiler object.
     *
     * More info on the Dwoo_Compiler can be found here:
     * http://wiki.dwoo.org/index.php/Dwoo_Compiler
     *
     * @param callback $callback A callback that will be called with the compiler
     * object at the start of the program.
     */
    public static function addCompilerHook($callback) {
        self::$compiler_hooks[] = $callback;
    }

    /**
     * Want to add some extra info to a file name? The callback you pass to
     * this function will be called for each class that is parsed and it will
     * be passed the file name (unmodified) and the class data. The return value
     * of this finction will be assigned to the file name variable and it will
     * be where the file is stored.
     *
     * Make sure that your callback returns a file path. It does not have to
     * replace all of the placeholders because the file name is passed to
     * all of the file name filter plugins but at the end of being passed
     * through all of the plugins, it needs to be a valid file path.
     *
     * @param callback $callback A callback that will be called with the
     * file name and the class info for each class being parsed.
     */
    public static function addFileNameFilter($callback) {
        self::$file_name_filters[] = $callback;
    }

    /**
     * The constructor takes a single argument: a list of classes to parse.
     *
     * This class list must strictly contain a list of class_name=>file_location
     * key value pairs. e.g. "Parser"=>"/path/to/Parser.php".
     *
     * This list of classes will be loaded in before parsing. If you get any
     * unexpected errors, check for naming conflicts between your classes
     * and my classes :)
     */
    public function __construct($class_list) {
        $this->class_list = $class_list;
        $this->dwoo = new Dwoo();
        $this->dwoo_compiler = new Dwoo_Compiler();

        // Execute the compiler hook callbacks
        foreach(self::$compiler_hooks as $callback) {
            $this->dwoo_compiler = call_user_func($callback, $this->dwoo_compiler);
        }

        $this->loadClasses();
    }

    /**
     * Loads all of the classes in the $this->class_list variable.
     */
    private function loadClasses() {
        // Apply user defined class list filters.
        foreach(self::$class_list_filters as $callback) {
            $this->class_list = call_user_func($callback, $this->class_list);
        }

        // Require and remove the special __init__ file. (for adding a loader if needed)
        if (isset($this->class_list['__init__'])) {
            require_once $this->class_list['__init__'];
            unset($this->class_list['__init__']);
        }

        // Include all of the class files.
        foreach($this->class_list as $name => $file) {
            require_once $file;
        }
    }

    /**
     * This method calls the parseClass method on all classes in the list
     * you passed to the constructor.
     *
     * The class list array must be a class_name=>file_location set of key
     * value pairs. e.g. "Parser"=>"/path/to/Parser.php".
     *
     * The $template and $to parameters get passed to parseClass appropriately.
     */
    public function parseAllToFile($template, $to) {
        foreach($this->class_list as $class_name => $file) {
            $this->parseClass($class_name, $template, $to);
        }
    }

    /**
     * The parseClass method takes a class name, the path to a valid template file
     * and the path to save the parsed class to.
     *
     * The class name needs to be a valid, declared class. Before this method is
     * called, the class list that was sent to the Parser is looped through and
     * the files are loaded in turn. If you gave a valid file path to the class in
     * the constructor argument, you don't need to worry.
     *
     * The template needs to be a valid Dwoo template. More information on that
     * can be found here: http://dwoo.org/
     *
     * The directory that you want to save to needs to be writable. If the directory
     * you specify does not exist, this method will attempt to create it. Same
     * goes with the file. You can use the special ":class_name" parameter in file
     * paths, it will be replaced with the parsed class's name.
     *
     * e.g. /home/sam/:class_name.rst
     *
     * will be changed to: /home/sam/Parser.rst
     *
     * If this method is called for the Parser class.
     */
    public function parseClass($class_name, $template, $to) {
        // Begin parsing. Status messages ftw.
        echo "Parsing $class_name... ";

        // Create the ClassParser object and parse the :class_name variable in the
        // file path.
        $class = new ClassParser($class_name);

        // Get the array of information to send to the template.
        $template_info = $class->templateInfo();

        // Apply user defined class info filters.
        foreach(self::$class_filters as $callback) {
            $template_info = call_user_func($callback, $template_info);
        }

        // Apply file name filters.
        foreach(self::$file_name_filters as $callback) {
            $to = call_user_func($callback, $to, $template_info);
        }

        // Make sure that the directory exists.
        if (!file_exists(dirname($to))) {
            if (!mkdir(dirname($to), $mode = 0777, $recursive = true)) {
                // Fail if the directory cannot be created.
                echo 'Failed.';
                throw Exception('Unable to create directory ' . dirname($to));
            }
        }


        // Parse the template using the Dwoo template framework.
        $data = $this->dwoo->get($template, $template_info, $this->dwoo_compiler);

        // Write the parsed template to file.
        file_put_contents($to, $data);

        // Echo success message.
        echo "Finished. Saved to $to\n\n";
    }

    /**
     * A simple wrapper to the Dwoo get() method.
     *
     * @param string $template Path to the template to use.
     * @param string $data The data to pass to the template.
     * @return The parsed template.
     */
    public function parse($template, $data) {
        return $this->dwoo->get($template, $data);
    }

    /**
     * This is a more specific function. It takes the class list and generates
     * a TOC tree (rST specific) and puts it where you specify in the $to
     * variable.
     *
     * It uses the templates/rst_toc.tpl to create the TOC tree.
     *
     * @param string $to Where to store this TOC tree.
     */
    public function generateTocTree($to) {
        echo 'Generating TOC tree... ';
        $classes = array('classes' => array());

        // Converts the class list into an array that's more usable in a .tpl
        foreach($this->class_list as $class_name => $file) {
            $info = array();
            $info["name"] = $class_name;
            $info["location"] = $file;
            $classes['classes'][] = $info;
        }

        $data = $this->parse(dirname(__FILE__) . '/../templates/rst_toc.tpl', $classes);
        file_put_contents($to, $data);
        echo 'Finished.' . "\n";
    }
}

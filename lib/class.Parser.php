<?php
require_once dirname(__FILE__) . '/../extlib/dwoo/dwooAutoload.php';

class Parser {

    /**
     * @var array
     */
    private $class_list;

    /**
     * @var Dwoo
     */
    private $dwoo;

    /**
     * @var array An array of callbacks.
     */
    private static $class_filters = array();

    /**
     * @var array An array of callbacks.
     */
    private static $class_list_filters = array();

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

        // Include all of the class files.
        foreach($this->class_list as $name => $file) {
            require_once $file;
        }

        // Remove the special __init__ file. (for adding a loader if needed)
        if (isset($this->class_list['__init__'])) {
            unset($this->class_list['__init__']);
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
    public function parseAll($template, $to) {
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
        $to = str_replace(':class_name', $class_name, $to);

        // Make sure that the directory exists.
        if (!file_exists(dirname($to))) {
            if (!mkdir(dirname($to), $mode = 0777, $recursive = true)) {
                // Fail if the directory cannot be created.
                echo 'Failed.';
                throw Exception('Unable to create directory ' . dirname($to));
            }
        }

        // Get the array of information to send to the template.
        $template_info = $class->templateInfo();

        // Apply user defined class info filters.
        foreach(self::$class_filters as $callback) {
            $template_info = call_user_func($callback, $template_info);
        }

        // Parse the template using the Dwoo template framework.
        $data = $this->dwoo->get($template, $template_info);

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

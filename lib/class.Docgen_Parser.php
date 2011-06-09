<?php
// Require the Dwoo autoloader. This is the only class that will use Dwoo.
require_once dirname(__FILE__) . '/../extlib/dwoo/dwooAutoload.php';

class Docgen_Parser {

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
        Docgen_Hooks::add('compiler_created', $callback);
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
    public static function addFileNameHook($callback) {
        Docgen_Hooks::add('file_name', $callback);
    }

    /**
     * The constructor takes a single argument: a list of classes to parse.
     *
     * This class list must strictly contain a list of file_location => array
     * of class names key value pairs. e.g. /path/to/Parser.php" => array("Parser")
     *
     * This list of classes will be loaded in before parsing. If you get any
     * unexpected errors, check for naming conflicts between your classes
     * and my classes :)
     */
    public function __construct(array $class_list = array()) {
        $this->class_list = $class_list;
        $this->dwoo = new Dwoo();

        // Create a Dwoo_Compiler and call the hooks on it.
        $this->dwoo_compiler = Docgen_Hooks::call('compiler_created',
            array(new Dwoo_Compiler()));

        $this->loadClasses();
    }

    /**
     * Returns the internall stored list of classes that this class
     * maintains. All of the classes returned in this list will have
     * been loaded into the program and accessible already.
     *
     * @return array A list of classes in the documented class list
     * format: file_name => array of classes in that file.
     */
    public function getClassList() {
        return $this->class_list;
    }

    /**
     * Adds classes to the parser. These classes will be loaded as soon
     * as they are added. Beware of potential naming conflicts.
     *
     * @param array $classes An array of classes in the described class_list
     * format. See the docblock for the constructor to this class.
     */
    public function addClasses(array $classes) {
        $this->class_list = array_merge($this->class_list, $classes);
        $this->loadClasses();
    }

    /**
     * Loads all of the classes in the $this->class_list variable.
     */
    private function loadClasses() {
        // Include all of the class files.
        foreach($this->class_list as $file => $class_name_array) {
            require_once realpath($file);
        }

        // Call the classes_loaded hook.
        Docgen_Hooks::call('classes_loaded');
    }

    /**
     * This method calls the parseClass method on all classes in the list
     * you passed to the constructor.
     *
     * The class list array must be a file_location => class_name array set of key
     * value pairs. e.g. "/path/to/Parser.php" => array("Parser")
     *
     * The $template and $to parameters get passed to parseClass appropriately.
     */
    public function parseAllToFile($template, $to) {
        foreach($this->class_list as $file => $class_name_array) {
            foreach($class_name_array as $class_name) {
                $this->parseClass($class_name, $template, $to);
            }
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

        // Get the array of information to send to the template.
        $template_info = $this->getClassInfo($class_name);

        // Apply file name filters.
        $to = Docgen_Hooks::call('file_name', array($to, $template_info));

        // Make sure that the directory exists.
        if (!file_exists(dirname($to))) {
            if (!mkdir(dirname($to), $mode = 0777, $recursive = true)) {
                // Fail if the directory cannot be created.
                echo 'Failed.';
                throw Exception('Unable to create directory ' . dirname($to));
            }
        }

        // Parse the template using the Dwoo template framework.
        $data = $this->parse($template, $template_info, $this->dwoo_compiler);

        // Write the parsed template to file.
        file_put_contents($to, $data);

        // Echo success message.
        echo "Finished. Saved to $to\n\n";
    }

    /**
     * This is an important method. It takes a class name and returns a huge
     * array of information on that class.
     *
     * It is also responsible for applying the class filter plugins to the
     * data that is returned. The array returned from this method will have
     * been passed through all registered class filter callbacks.
     *
     * @param string $class_name The name of the class to gether info on.
     * @return
     */
    public function getClassInfo($class_name) {
        // Create the ClassParser object and parse the :class_name variable in the
        // file path.
        $class = new Docgen_ClassParser($class_name);

        // Get the array of information to send to the template.
        return $class->templateInfo();
    }

    /**
     * Gets an array of all of the class info currently stored in the CodeSearch
     * object.
     *
     * Simply loops through all of the classes in the internal class list and
     * calls getClassInfo on them.
     *
     * @return array An array of class info.
     */
    public function getAllClassInfo() {
        $return = array();
        foreach($this->class_list as $file_location => $class_name_array) {
            foreach($class_name_array as $class_name) {
                $return[] = $this->getClassInfo($class_name);
            }
        }
        return $return;
    }

    /**
     * A simple wrapper to the Dwoo get() method.
     *
     * @param string $template Path to the template to use.
     * @param string $data The data to pass to the template.
     * @param Dwoo_Compiler The compiler to use for the parse.
     * @return The parsed template.
     */
    public function parse($template, $data, $compiler = null) {
        return $this->dwoo->get($template, $data, $compiler);
    }

    /**
     * Returns the class list but in a more template friendly array format.
     *
     * For example, if you had this class list:
     *
     * 'MyClass' => '/path/to/lib/MyClass.php'
     * 'YourClass => '/path/to/lib/YourClass.php'
     *
     * This method would return:
     *
     * 'classes' => array(
     *      [0] => (
     *          'name' => 'MyClass',
     *          'location' => '/path/to/lib/MyClass.php'
     *      )
     *      [1] => (
     *          'name' => 'YourClass',
     *          'location' => '/path/to/lib/YourClass.php'
     *      )
     * );
     */
    public function templateFriendlyClassList() {
        $classes = array('classes' => array());

        // Converts the class list into an array that's more usable in a .tpl
        foreach($this->class_list as $file => $class_name_array) {
            foreach($class_name_array as $class_name) {
                $info = array();
                $info["name"] = $class_name;
                $info["location"] = $file;
                $classes['classes'][] = $info;
            }
        }

        return $classes;
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

        $classes = $this->templateFriendlyClassList();
        $data = $this->parse(dirname(__FILE__) . '/../templates/rst_toc.tpl', $classes);
        if (file_put_contents($to, $data)) {
            echo 'Finished.' . "\n";
        } else {
            echo 'Failed.' . "\n";
        }
    }
}

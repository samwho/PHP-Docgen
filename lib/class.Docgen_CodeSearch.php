<?php

class Docgen_CodeSearch {

    private static $class_regex = '/^\s*(?:abstract\s+)?(?:class|interface)\s+(\w+?)\s+(?:.*?)\s*{/m';

    private $class_list = array();

    /**
     * Simply returns the internally stored array of classes.
     *
     * If you were to perform multiple findClasses calls and it found a few
     * different sets of classes, all of them would be stored in this
     * variable and accessible from this method.
     *
     * Stores the classes as file_location => array of class names key
     * value pairs.
     *
     * @return array Internally stored list of classes.
     */
    public function getClassList() {
        return $this->class_list;
    }

    /**
     * Got a script that needs to run before your classes will play
     * nicely together? Call this function with a path to that script.
     *
     * ThinkUp, for example, requires an initialisation script that
     * registers an autoloader before its classes will work together.
     *
     * @param string $file Path to an init script. This script will
     * get loaded before anything else.
     */
    public function addInitScript($file) {
        $this->class_list['__init__'] = $file;
    }

    /**
     * Give this method a valid glob and it will scan all of the files that match
     * that glob and match class definitions.
     *
     * It will then store a list of these classes in file_location => array of
     * class names key value pairs. It will return this list and keep a copy
     * stored internally.
     *
     * The returned array is of the correct format for passing to the Parser
     * constructor.
     *
     * @param glob $glob A valid glob (docs on globs:
     * http://en.wikipedia.org/wiki/Glob_(programming) )
     */
    public function findClasses($glob) {
        foreach(glob($glob) as $file) {
            $result = $this->scanForClasses($file);

            // scanForClasses returns an array on success
            if (is_array($result)) {
                $this->class_list = array_merge($this->class_list, $result);
            }
        }

        return $this->class_list;
    }

    /**
     * scanForClasses takes a file path and returns an array with this
     * in it: file_name => array of classes found in it.
     * e.g. array('/path/to/lib/class.Parser.php' => array('Parser'));
     *
     * @param string $file Path to the file to scan for existing class.
     * @return mixed An array when a class is found (as described above), or
     * null if no class was found.
     */
    public function scanForClasses($file) {
        if (file_exists($file)) {
            Echo "Scanning $file... ";
            // Get file contents.
            $contents = file_get_contents($file);

            // Get matches for the class regex
            $matches = array();
            preg_match_all(self::$class_regex, $contents, $matches);

            // If anything matched the full regex, create a list of classes in
            // the file and return it.
            if (!empty($matches[0])) {
                echo "Matched " . sizeof($matches[0]) . ".\n\n";

                return array($file => $matches[1]);
            } else {
                echo 'No match.' . "\n\n";
                return null;
            }
        } else {
            // File does not exist. Return null.
            return null;
        }
    }
}

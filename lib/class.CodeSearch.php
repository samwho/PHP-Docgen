<?php

class CodeSearch {

    private static $class_regex = '/^\s*(?:abstract\s+)?(?:class|interface)\s+(\w+?)\s+(?:.*?)\s*{/m';

    private $class_list = array();

    /**
     * Give this method a valid glob and it will scan all of the files that match
     * that glob and match class definitions.
     *
     * It will then store a list of these classes in class_name=>file_location key
     * value pairs. It will return this list and keep a copy stored internally.
     *
     * The returned array is of the correct format for passing to the Parser
     * constructor.
     *
     * @param glob $glob A valid glob (docs on globs:
     * http://en.wikipedia.org/wiki/Glob_(programming) )
     */
    public function findClasses($glob) {
        foreach(glob($glob) as $file) {
            $result = $this->scanForClass($file);

            // scanForClass returns an array on success
            if (is_array($result)) {
                $this->class_list[$result[0]] = $result[1];
            }
        }

        return $this->class_list;
    }

    /**
     * scanForClass takes a file path and returns an array with 1 element
     * in it: class_name => file it was found in.
     * e.g. array('Parser' => '/path/to/lib/class.Parser.php');
     *
     * @param string $file Path to the file to scan for existing class.
     * @return mixed An array when a class is found (as described above), or
     * null if no class was found.
     */
    public function scanForClass($file) {
        if (file_exists($file)) {
            Echo "Scanning $file... ";
            // Get file contents.
            $contents = file_get_contents($file);

            // Get matches for the class regex
            $matches = array();
            preg_match_all(self::$class_regex, $contents, $matches);

            if (!empty($matches[0])) {
                echo "Matched. Class: {$matches[1][0]}\n\n";
                return array($matches[1][0], $file);
            } else {
                echo 'No match.' . "\n\n";
                return null;
            }
        }
    }
}

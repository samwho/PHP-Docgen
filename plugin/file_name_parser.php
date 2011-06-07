<?php
class FileNameParser {
    /**
     * Add a file name filter to the Parser class. This callback runs the
     * parse method on the current object.
     */
    public function __construct() {
        Parser::addFileNameHook(array($this, 'parse'));
    }

    /**
     * The file name filter callback will receive the file name and the class info
     * that is to be passed to the template as arguments. With these, you are allowed
     * to modify the file path.
     *
     * For example, this plugin does some simple placeholder replacement for file names
     * so that you can make them more dynamic.
     *
     * You must return your modified file path when you exit the function or you will
     * set the file path to null and kittens will die.
     *
     * @param string $file_name The file name that was specified to save files to.
     * @param array $class_info The class info that is to be passed to the template.
     * This is the class information _after_ it has been passed through the class filters.
     * @return string You _must_ return the filepath from this method.
     */
    public function parse($file_name, $class_info) {
        // Create an array of key => replacement pairs.
        $replacements = array(
            ':class_name' => $class_info["name"]
        );

        // Loop through the replacements array, replacing the keys with the values on
        // the file name.
        foreach($replacements as $key => $value) {
            $file_name = str_replace($key, $value, $file_name);
        }

        // Return the file name. Very important!
        return $file_name;
    }
}

// Create an instance of the class. This is the plugin writer's responsibility,
// the code will not instantiate plugin classes for you.
new FileNameParser();

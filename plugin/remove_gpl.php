<?php

/**
 * Both an example plugin and a useful plugin if you're storing the entire GPL
 * license string in your class level docblock and don't want it to appear in
 * the documentation.
 */
class LicenseRemoval {
    /**
     * The constructor will add all of the callbacks to the necessary places.
     */
    public function __construct() {
        // Add a class filter. This filter will be passed one argument and that
        // will be the entire array of information on that class.
        //
        // I advise having a play around with the data you will have access to
        // before starting :)
        Parser::addClassFilter(array(&$this, 'removeGplText'));
    }

    /**
     * This method is what's called before each array of class info gets passed
     * to the template parser. It receives that info as an argument and whatever
     * it returns will be the new value for the class's info. Notice how I return
     * $class_info at the end. This is imperative! If I don't do this, the template
     * will not be sent any data.
     *
     * @param array $class_info The array of information on the current class that
     * gets sent to the template parser.
     * @return array The _new_ array of info that gets sent to the template parser
     * (or other plugins if there are any).
     */
    public function removeGplText($class_info) {
        $pattern  = '/LICENSE:(.+)<http:\/\/www\.gnu\.org\/licenses\/>\.\s*/s';
        $class_info["docblock"] = preg_replace($pattern, '', $class_info["docblock"]);
        return $class_info;
    }
}

// Create an instance of the class. This is the plugin writer's responsibility,
// the code will not instantiate plugin classes for you.
new LicenseRemoval();

<?php
class ClassParser extends ReflectionClass {
    private static $hook_name = 'class_info';

    /**
     * All a class info hook. This allows you to edit class information
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
    public static function addHook($callback) {
        Hooks::add(self::$hook_name, $callback);
    }

    /**
     * Overridden from the original behaviour of ReflectionClass. This now
     * returns a string that _does not_ contain the starts multiline comment
     * stars.
     */
    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
    }

    /**
     * Gets an array of tag information from the docblock for this class.
     *
     * For example, if the class had "@author Sam Rose <samwho@lbak.co.uk>" in
     * it, this method would return:
     *
     * Array (
     *     'full_tag' => '@author Sam Rose <samwho@lbak.co.uk>',
     *     'name' => 'author',
     *     'contents' => 'Sam Rose <samwho@lbak.co.uk>'
     * )
     */
    public function getDocTags() {
        return ParserUtils::getDocTags($this->getDocComment());
    }

    /**
     * Returns a docblock comment without @tags or stars. So... just the comment bit.
     *
     * @return string Docblock comment minus stars and @tags.
     */
    public function getDocCommentWithoutTags() {
        return ParserUtils::removeDocStarsAndTags(parent::getDocComment());
    }

    /**
     * This method has been modified slightly. Instead of the original getMethods
     * array that returns an array of ReflectionMethod object, this one returns my
     * own MethodParser objects. They inherit from the ReflectionMethod so can be
     * used as such be some of the behaviour is overridden (such as getting a comment,
     * the comment returned now has stars stripped).
     *
     * @return array An array of MethodParser objects.
     */
    public function getMethods() {
        $methods = parent::getMethods();
        foreach ($methods as $key=>$method) {
            $methods[$key] = new MethodParser($method->class, $method->name);
        }

        return $methods;
    }

    /**
     * Returns an associative array of information on this class that will be
     * sent the template for parsing.
     */
    public function templateInfo() {
        $info = array();

        // Add the MethodParser object template info to this array.
        $info["methods"] = array();
        foreach($this->getMethods() as $method) {
            $info["methods"][] = $method->templateInfo();
        }

        // Add info on the class constants.
        $info["constants"] = array();
        foreach($this->getConstants() as $key => $value) {
            $info["constants"][$key] = $value;
        }

        // Add the docblock
        $info["docblock"] = $this->getDocCommentWithoutTags();

        // Add the tag information.
        $info["tags"] = $this->getDocTags();

        // Add how many lines of code there are in this class.
        $info["lines_of_code"] = $this->getEndLine() - $this->getStartLine();
        $info["start_line"] = $this->getStartLine();
        $info["end_line"] = $this->getEndLine();
        $info["file_name"] = $this->getFileName();
        $info["name"] = $this->getName();

        // Not entirely sure what a short name is. I think it's the class minus
        // any namespace it might be in. Reflection API docs didn't say.
        $info["short_name"] = $this->getShortName();

        // The following are pretty self explanatory.
        $info["is_abstract"] = $this->isAbstract();
        $info["is_final"] = $this->isFinal();
        $info["is_instantiable"] = $this->isInstantiable();
        $info["is_interface"] = $this->isInterface();
        $info["is_internal"] = $this->isInternal();
        $info["is_iterateable"] = $this->isIterateable();
        $info["is_user_defined"] = $this->isUserDefined();

        // Get the info for the parent class. This makes the array quite large if
        // there a big inheritance tree. Lots of rich information for the templates,
        // though.
        if ($this->getParentClass()) {
            $parent = new ClassParser($this->getParentClass()->name);
        } else {
            $parent = false;
        }

        // Add the parent info if a parent exists, null otherwise.
        $info["parent"] = $parent ? $parent->templateInfo() : null;
        $info["namespace"] = $this->getNamespaceName();

        // Add interface info similar to how parent class info is added. Yay for rich
        // information!
        $info["interfaces"] = array();
        foreach($this->getInterfaceNames() as $interface) {
            $interface_class = new ClassParser($interface);
            $info["interfaces"][] = $interface_class->templateInfo();
        }

        // Pass the modifiers of this class (which are returned as a very unhelpful
        // integer) to a helper class to decipher them. The result is a string like
        // "public static" or "private" etc.
        $info["modifiers"] = implode(' ', Reflection::getModifierNames($this->getModifiers()));

        // Add property information.
        $info["properties"] = array();
        foreach($this->getProperties() as $property) {
            $property_class = new PropertyParser($property->class, $property->name);
            $info["properties"][] = $property_class->templateInfo();
        }

        // Call the class info hooks and return the result.
        return Hooks::call(self::$hook_name, array($info));
    }
}

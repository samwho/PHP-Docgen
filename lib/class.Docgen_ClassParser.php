<?php
class Docgen_ClassParser extends ReflectionClass {
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
        Docgen_Hooks::add(self::$hook_name, $callback);
    }

    /**
     * Overridden from the original behaviour of ReflectionClass. This now
     * returns a string that _does not_ contain the starts multiline comment
     * stars.
     */
    public function getDocComment() {
        return Docgen_ParserUtils::stripCommentStars(parent::getDocComment());
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
        return Docgen_ParserUtils::getDocTags($this->getDocComment());
    }

    /**
     * Returns a docblock comment without @tags or stars. So... just the comment bit.
     *
     * @return string Docblock comment minus stars and @tags.
     */
    public function getDocCommentWithoutTags() {
        return Docgen_ParserUtils::removeDocStarsAndTags(parent::getDocComment());
    }

    /**
     * This method has been modified slightly. Instead of the original getMethods
     * array that returns an array of ReflectionMethod object, this one returns my
     * own Docgen_MethodParser objects. They inherit from the ReflectionMethod so
     * can be used as such be some of the behaviour is overridden (such as getting
     * a comment,
     * the comment returned now has stars stripped).
     *
     * @return array An array of MethodParser objects.
     */
    public function getMethods() {
        $methods = parent::getMethods();
        foreach ($methods as $key=>$method) {
            $methods[$key] = new Docgen_MethodParser($method->class, $method->name);
        }

        return $methods;
    }

    /**
     * Gets an array of method information that is to be sent to a template.
     *
     * @return array Template information for the methods of this class.
     */
    public function getMethodTemplateInfo() {
        $return = array();
        foreach($this->getMethods() as $method) {
            $return[] = $method->templateInfo();
        }

        return $return;
    }

    /**
     * Gets an array of information on constants that is suitable for use
     * in a template file.
     *
     * For example, if you had "const MY_CONSTANT = 12", this would
     * return:
     *
     * array (
     *     'name' => 'MY_CONSTANT',
     *     'value' => 12
     * )
     *
     * @return array An array of constants data for passing to a template.
     */
    public function getConstantsTemplateInfo() {
        $return = array();
        foreach($this->getConstants() as $key => $value) {
            $info = array();
            $info['name'] = $key;
            $info['value'] = $value;
            $return[] = $info;
        }
        return $return;
    }

    /**
     * Returns the number of lines of code in this class.
     *
     * @return int Lines of code in this clas.
     */
    public function linesOfCode() {
        // The +1 on the end does make sense, hones. Imagine a class that
        // starts on line 7 and ends on line 9. It isn't 2 lines of code,
        // it's 3.
        return $this->getEndLine() - $this->getStartLine() + 1;
    }

    /**
     * Overriding the ReflectionClass version of this method. This one does the
     * same thing but instead of returning a ReflectionClass, it returns a
     * Docgen_ClassParser.
     *
     * @return mixed Returns a ClassParser for the parent of this class if
     * a parent exists, false otherwise.
     */
    public function getParentClass() {
        if (parent::getParentClass()) {
            return new Docgen_ClassParser(parent::getParentClass()->name);
        } else {
            return false;
        }
    }

    /**
     * Returns the template information of the parent to this class. If this
     * class has no parent, false is returned.
     *
     * @return mixed False if no parent exists, an array of template info if
     * a parent does exist.
     */
    public function getParentClassTemplateInfo() {
        return $this->getParentClass() ?
            $this->getParentClass()->templateInfo() : false;
    }

    /**
     * This is an override of the ReflectionClass getInterfaces() method. The only
     * difference is that instead of returning ReflectionClass objects, it
     * returns Docgen_ClassParser objects.
     *
     * @return array An array of interface_name => class_parser_object key
     * value pairs.
     */
    public function getInterfaces() {
        $return = array();
        foreach($this->getInterfaceNames() as $interface) {
            $return[$interface] = new Docgen_ClassParser($interface);
        }
        return $return;
    }

    /**
     * Returns an array of template information for this class's interfaces.
     *
     * If no interfaces exist, an empty array is returned.
     *
     * @return array Interface template information.
     */
    public function getInterfacesTemplateInfo() {
        $return = array();
        foreach($this->getInterfaces() as $interface) {
            $return[] = $interface->templateInfo();
        }
        return $return;
    }

    /**
     * Returns the modifier string for this class.
     *
     * e.g. "public static", "private" etc.
     *
     * @return string The modifier string for this class. If there is none,
     * returns an empty string.
     */
    public function getModifierString() {
        // Pass the modifiers of this class (which are returned as a very unhelpful
        // integer) to a helper class to decipher them. The result is a string like
        // "public static" or "private" etc.
        return implode(' ', Reflection::getModifierNames($this->getModifiers()));
    }

    /**
     * This is an override of the parent method getProperties. The only difference
     * is that instead of returning instances of ReflectionProperty, it returns
     * instances of Docgen_PropertyParser (which inherit from ReflectionProperty).
     *
     * @return array An array of Docgen_PropertyParser objects. Empty if there
     * are no properties.
     */
    public function getProperties() {
        $return = array();
        foreach(parent::getProperties() as $property) {
            $return[] = new Docgen_PropertyParser($property->class, $property->name);
        }
        return $return;
    }

    /**
     * Gets an array of template information for the properties of this class.
     * Loops over the return value of $this->getProperties() and calls the
     * templateInfo() method on each of them.
     *
     * @return array An array of proeprty template information. Empty if there
     * are no proeprties.
     */
    public function getPropertiesTemplateInfo() {
        $return = array();
        foreach($this->getProperties() as $property) {
            $return[] = $property->templateInfo();
        }
        return $return;
    }

    /**
     * Returns an associative array of information on this class that will be
     * sent the template for parsing.
     */
    public function templateInfo() {
        $info = array();

        // Add the MethodParser object template info to this array.
        $info["methods"] = $this->getMethodTemplateInfo();

        // Add info on the class constants.
        $info["constants"] = $this->getConstantsTemplateInfo();

        // Add the docblock
        $info["docblock"] = $this->getDocCommentWithoutTags();

        // Add the tag information.
        $info["tags"] = $this->getDocTags();

        // Add how many lines of code there are in this class.
        $info["lines_of_code"] = $this->linesOfCode();
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

        // Add the parent info if a parent exists, null otherwise.
        $info["parent"] = $this->getParentClassTemplateInfo();
        $info["namespace"] = $this->getNamespaceName();

        // Add interface info similar to how parent class info is added. Yay for rich
        // information!
        $info["interfaces"] = $this->getInterfacesTemplateInfo();

        $info["modifiers"] = $this->getModifierString();

        // Add property information.
        $info["properties"] = $this->getPropertiesTemplateInfo();

        // Call the class info hooks and return the result.
        return Docgen_Hooks::call(self::$hook_name, array($info));
    }
}

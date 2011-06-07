<?php

class Docgen_MethodParser extends ReflectionMethod {
    private static $hook_name = 'method_info';

    /**
     * Add a method hook. This allows you to edit method information
     * before it gets sent to the template parser.
     *
     * For every method that gets parsed, your callback function will get sent the
     * associative array of information that represents it and the return value
     * from your callback will be the new template data. Because of this, it is
     * imperative that you return something usable for your selected template.
     *
     * @param callback $callback A callback that will eventually get passed in to
     * the call_user_func method.
     */
    public static function addHook($callback) {
        Docgen_Hooks:add(self::$hook_name, $callback);
    }

    /**
     * Overridden from the original behaviour of ReflectionMethod. This now
     * returns a string that _does not_ contain the starts multiline comment
     * stars.
     */
    public function getDocComment() {
        return Docgen_ParserUtils::stripCommentStars(parent::getDocComment());
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
     * Gets an array of tag information from the docblock for this method.
     *
     * For example, if the method had "@author Sam Rose <samwho@lbak.co.uk>" in
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
     * Returns the modifier string for this method.
     *
     * e.g. "public static", "private" etc.
     *
     * @return string The modifier string for this method. If there is none,
     * returns an empty string.
     */
    public function getModifierString() {
        // Pass the modifiers of this class (which are returned as a very unhelpful
        // integer) to a helper class to decipher them. The result is a string like
        // "public static" or "private" etc.
        return implode(' ', Reflection::getModifierNames($this->getModifiers()));
    }

    /**
     * Returns the number of lines of code in this method.
     *
     * @return int Lines of code in this method.
     */
    public function linesOfCode() {
        // The +1 on the end does make sense, honest. Imagine a method that
        // starts on line 7 and ends on line 9. It isn't 2 lines of code,
        // it's 3.
        return $this->getEndLine() - $this->getStartLine() + 1;
    }

    /**
     * Override of the parent method getParameters, this does exactly the same thing but
     * instead of returning an array of ReflectionParameter objects, it returns an
     * array of Docgen_ParameterParser objects.
     *
     * @return array An array of Docgen_ParameterParser objects. Array is empty if no
     * parameters exist for this method.
     */
    public function getParameters() {
        $return = array();
        foreach(parent::getParameters() as $parameter) {
            $return[$parameter->name] =
                new Docgen_ParameterParser(array($this->getDeclaringClass()->getName(), $this->getName()), $parameter->getName());
        }
        return $return;
    }

    /**
     * Gets an array of template information on this method's parameters.
     *
     * @return array Template information for this method's parameters.
     */
    public function getParametersTemplateInfo() {
        $return = array();
        foreach($this->getParameters() as $parameter) {
            $return[] = $parameter->templateInfo();
        }
        return $return;
    }

   /**
    * Gets the source code of this class.
    *
    * @return string Class source code.
    */
   public function getSource() {
       if( !file_exists( $this->getFileName() ) ) return false;

       $start_offset = ( $this->getStartLine() - 1 );
       $end_offset   = ( $this->getEndLine() - $this->getStartLine() ) + 1;

       return join( '', array_slice( file( $this->getFileName() ), $start_offset, $end_offset ) );
   }

    /**
     * Returns an associative array of information on this method that will be
     * sent the template for parsing.
     */
    public function templateInfo() {
        $info = array();
        $info["tags"] = $this->getDocTags();
        $info["docblock"] = $this->getDocCommentWithoutTags();

        $info["modifiers"] = $this->getModifierString();
        $info["lines_of_code"] = $this->linesOfCode();
        $info["name"] = $this->getName();
        $info["short_name"] = $this->getShortName();
        $info["returns_reference"] = $this->returnsReference();
        $info["no_of_parameters"] = $this->getNumberOfParameters();
        $info["no_of_required_parameters"] = $this->getNumberOfRequiredParameters();
        $info['class_name'] = $this->getDeclaringClass()->getName();
        $info['is_abstract'] = $this->isAbstract();
        $info['is_constructor'] = $this->isConstructor();
        $info['is_destructor'] = $this->isDestructor();
        $info['is_fianl'] = $this->isFinal();
        $info['is_private'] = $this->isPrivate();
        $info['is_protected'] = $this->isProtected();
        $info['is_public'] = $this->isPublic();
        $info['is_static'] = $this->isStatic();
        $info['source'] = $this->getSource();

        $info["parameters"] = $this->getParametersTemplateInfo();

        // Pass the method info through the registered hooks and return it.
        return Docgen_Hooks::call(self::$hook_name, array($info));
    }
}

<?php

class MethodParser extends ReflectionMethod {
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
        Hooks:add(self::$hook_name, $callback);
    }

    /**
     * Overridden from the original behaviour of ReflectionMethod. This now
     * returns a string that _does not_ contain the starts multiline comment
     * stars.
     */
    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
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
        return ParserUtils::getDocTags($this->getDocComment());
    }

    /**
     * Returns an associative array of information on this method that will be
     * sent the template for parsing.
     */
    public function templateInfo() {
        $info = array();
        $info["tags"] = $this->getDocTags();
        $info["docblock"] = $this->getDocCommentWithoutTags();

        $info["modifiers"] = implode(' ', Reflection::getModifierNames($this->getModifiers()));
        $info["lines_of_code"] = $this->getEndLine() - $this->getStartLine();
        $info["name"] = $this->getName();
        $info["short_name"] = $this->getShortName();
        $info["returns_reference"] = $this->returnsReference();
        $info["no_of_parameters"] = $this->getNumberOfParameters();
        $info["no_of__required_parameters"] = $this->getNumberOfRequiredParameters();

        $info["parameters"] = array();
        foreach($this->getParameters() as $parameter) {
            $parameter_info = array(
                'name' => $parameter->getName(),
                'position' => $parameter->getPosition(),
                'is_array' => $parameter->isArray(),
                'is_optional' => $parameter->isOptional(),
                'is_passed_by_reference' => $parameter->isPassedByReference(),
                'is_default_value_available' => $parameter->isDefaultvalueAvailable()
            );

            if ($parameter->isOptional()) {
                $parameter_info["default_value"] = $parameter->getDefaultValue();
            }

            $info["parameters"][] = $parameter_info;
        }

        // Pass the method info through the registered hooks and return it.
        return Hooks::call(self::$hook_name, array($info));
    }
}

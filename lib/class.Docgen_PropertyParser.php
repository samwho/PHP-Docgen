<?php

class Docgen_PropertyParser extends ReflectionProperty {
    private static $hook_name = 'property_info';

    /**
     * Add a property info hook. This allows you to edit property information
     * before it gets sent to the template parser.
     *
     * For every property that gets parsed, your callback function will get sent the
     * associative array of information that represents it and the return value
     * from your callback will be the new template data. Because of this, it is
     * imperative that you return something usable for your selected template.
     *
     * @param callback $callback A callback that will eventually get passed in to
     * the call_user_func method.
     */
    public static function addHook($callback) {
        Docgen_Hoooks:add(self::$hook_name, $callback);
    }


    /**
     * Overridden from the original behaviour of ReflectionProperty. This now
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
     * Gets an array of tag information from the docblock for this property.
     *
     * For example, if the property had "@var string Some string." in
     * it, this method would return:
     *
     * Array (
     *     'full_tag' => '@var string Some string.',
     *     'name' => 'var',
     *     'contents' => 'string Some string.'
     * )
     */
    public function getDocTags() {
        return Docgen_ParserUtils::getDocTags($this->getDocComment());
    }

    /**
     * Returns an associative array of information on this property that will be
     * sent the template for parsing.
     */
    public function templateInfo() {
        $info = array();

        $info["name"] = $this->getName();
        $info["modifiers"] = implode(' ', Reflection::getModifierNames($this->getModifiers()));
        $this->setAccessible(true);
        $info["value"] = $this->getValue($this);
        $info["docblock"] = $this->getDocCommentWithoutTags();
        $info["tags"] = $this->getDocTags();

        // Pass the property info through registered property info hooks and return it.
        return Docgen_Hooks::call(self::$hook_name, array($info));
    }
}

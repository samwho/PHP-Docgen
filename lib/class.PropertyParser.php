<?php

class PropertyParser extends ReflectionProperty {
    /**
     * Overridden from the original behaviour of ReflectionProperty. This now
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
        return ParserUtils::getDocTags($this->getDocComment());
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

        return $info;
    }
}

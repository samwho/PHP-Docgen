<?php

class PropertyParser extends ReflectionProperty {
    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
    }

    public function getDocCommentWithoutTags() {
        return ParserUtils::removeDocStarsAndTags(parent::getDocComment());
    }

    public function getDocTags() {
        return ParserUtils::getDocTags($this->getDocComment());
    }

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

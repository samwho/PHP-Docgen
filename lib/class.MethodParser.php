<?php

class MethodParser extends ReflectionMethod {
    public function __construct(ReflectionMethod $method) {
        parent::__construct($method->class, $method->name);
    }

    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
    }

    public function getDocTags() {
        return ParserUtils::getDocTags($this->getDocComment());
    }
}

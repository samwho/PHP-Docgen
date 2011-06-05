<?php

class ClassParser extends ReflectionClass {
    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
    }

    public function getDocTags() {
        return ParserUtils::getDocTags($this->getDocComment());
    }

    public function getMethods() {
        $methods = parent::getMethods();
        foreach ($methods as $key=>$method) {
            $methods[$key] = new MethodParser($method);
        }

        return $methods;
    }
}

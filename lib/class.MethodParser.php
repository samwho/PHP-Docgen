<?php

class MethodParser extends ReflectionMethod {

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

        return $info;
    }
}

<?php
class ClassParser extends ReflectionClass {
    public function getDocComment() {
        return ParserUtils::stripCommentStars(parent::getDocComment());
    }

    public function getDocTags() {
        return ParserUtils::getDocTags($this->getDocComment());
    }

    public function getDocCommentWithoutTags() {
        return ParserUtils::removeDocStarsAndTags(parent::getDocComment());
    }


    public function getMethods() {
        $methods = parent::getMethods();
        foreach ($methods as $key=>$method) {
            $methods[$key] = new MethodParser($method->class, $method->name);
        }

        return $methods;
    }

    public function templateInfo() {
        $info = array();

        $info["methods"] = array();
        foreach($this->getMethods() as $method) {
            $info["methods"][] = $method->templateInfo();
        }

        $info["constants"] = array();
        foreach($this->getConstants() as $key => $value) {
            $info["constants"][$key] = $value;
        }

        $info["docblock"] = $this->getDocCommentWithoutTags();
        $info["tags"] = $this->getDocTags();
        $info["lines_of_code"] = $this->getEndLine() - $this->getStartLine();
        $info["file_name"] = $this->getFileName();
        $info["name"] = $this->getName();
        $info["short_name"] = $this->getShortName();
        $info["name_length"] = strlen($info["name"]);

        $info["is_abstract"] = $this->isAbstract();
        $info["is_final"] = $this->isFinal();
        $info["is_instantiable"] = $this->isInstantiable();
        $info["is_interface"] = $this->isInterface();
        $info["is_internal"] = $this->isInternal();
        $info["is_iterateable"] = $this->isIterateable();
        $info["is_user_defined"] = $this->isUserDefined();

        if ($this->getParentClass()) {
            $parent = new ClassParser($this->getParentClass()->name);
        } else {
            $parent = false;
        }

        $info["parent"] = $parent ? $parent->templateInfo() : null;
        $info["namespace"] = $this->getNamespaceName();

        $info["interfaces"] = array();
        foreach($this->getInterfaceNames() as $interface) {
            $interface_class = new ClassParser($interface);
            $info["interfaces"][] = $interface_class->templateInfo();
        }

        $info["modifiers"] = implode(' ', Reflection::getModifierNames($this->getModifiers()));

        $info["properties"] = array();
        foreach($this->getProperties() as $property) {
            $property_class = new PropertyParser($property->class, $property->name);
            $info["properties"][] = $property_class->templateInfo();
        }

        return $info;
    }
}

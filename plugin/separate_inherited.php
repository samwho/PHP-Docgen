<?php

class SeparateInherited {
    public function __construct() {
        Docgen_ClassParser::addHook(array($this, 'separateInheritedMethods'));
        Docgen_ClassParser::addHook(array($this, 'separateInheritedProperties'));
    }

    /**
     * Separates all of the methods from a class if they are inherited.
     *
     * It does this by comparing the name of the class passed in with the
     * $class_info argument against the name of the declaring class in each
     * method.
     */
    public function separateInheritedMethods($class_info) {
        $new_method_array = array();
        $inherited_method_array = array();

        foreach($class_info['methods'] as $method) {
            if ($method['class_name'] == $class_info['name']) {
                $new_method_array[] = $method;
            } else {
                $method['is_inherited'] = true;
                $inherited_method_array[] = $method;
            }
        }

        $class_info['methods'] = $new_method_array;
        $class_info['inherited_methods'] = $inherited_method_array;

        return $class_info;
    }

    /**
     * Separates all of the properties from a class that were inherited.
     *
     * It does this by comparing the name of the class passed in with the
     * $class_info argument against the name of the declaring class in each
     * property.
     */
    public function separateInheritedProperties($class_info) {
        $new_property_array = array();
        $inherited_property_array = array();

        foreach($class_info['properties'] as $property) {
            if ($property['declaring_class'] == $class_info['name']) {
                $new_property_array[] = $property;
            } else {
                $property['is_inherited'] = true;
                $inherited_property_array[] = $property;
            }
        }

        $class_info['properties'] = $new_property_array;
        $class_info['inherited_properties'] = $inherited_property_array;

        return $class_info;
    }
}

// Create an instance of RemoveInheritedMethods so that the hooks
// get properly registered.
new SeparateInherited();

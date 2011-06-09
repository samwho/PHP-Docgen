<?php

class RemoveInherited {
    public function __construct() {
        Docgen_ClassParser::addHook(array($this, 'removeInheritedMethods'));
        Docgen_ClassParser::addHook(array($this, 'removeInheritedProperties'));
    }

    /**
     * Removes all of the methods from a class if they are inherited.
     *
     * It does this by comparing the name of the class passed in with the
     * $class_info argument against the name of the declaring class in each
     * method.
     */
    public function removeInheritedMethods($class_info) {
        $new_method_array = array();

        foreach($class_info['methods'] as $method) {
            if ($method['class_name'] == $class_info['name']) {
                $new_method_array[] = $method;
            }
        }

        $class_info['methods'] = $new_method_array;

        return $class_info;
    }

    /**
     * Removes all of the properties from a class that were inherited.
     *
     * It does this by comparing the name of the class passed in with the
     * $class_info argument against the name of the declaring class in each
     * property.
     */
    public function removeInheritedProperties($class_info) {
        $new_property_array = array();

        foreach($class_info['properties'] as $property) {
            if ($property['declaring_class'] == $class_info['name']) {
                $new_property_array[] = $property;
            }
        }

        $class_info['properties'] = $new_property_array;

        return $class_info;
    }
}

// Create an instance of RemoveInheritedMethods so that the hooks
// get properly registered.
new RemoveInherited();
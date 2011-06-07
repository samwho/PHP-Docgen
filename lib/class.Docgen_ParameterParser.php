<?php

class Docgen_ParameterParser extends ReflectionParameter {
    private static $hook_name = 'parameter_info';
    public static function addHook($callback) {
        Docgen_Hooks::add(self::$hook_name, $callback);
    }

    public function templateInfo() {
        $info = array();

        $info['name'] = $this->getName();
        $info['position'] = $this->getPosition();
        $info['is_array'] = $this->isArray();
        $info['is_optional'] = $this->isOptional();
        $info['is_passed_by_reference'] = $this->isPassedByReference();
        $info['is_default_value_available'] = $this->isDefaultvalueAvailable();
        $info['allows_null'] = $this->allowsNull();
        $info['class_name'] = $this->getDeclaringClass() ?
            $this->getDeclaringClass()->name : null;
        $info['function_name'] = $this->getDeclaringFunction()->name;

        // class_type is the Class of the variable if it uses type hinting.
        $info['class_type'] = $this->getClass() ? $this->getClass()->getName() : null;

        if ($this->isOptional()) {
                $info['default_value'] = $this->getDefaultValue();
        }

        return Docgen_Hooks::call(self::$hook_name, array($info));
    }
}

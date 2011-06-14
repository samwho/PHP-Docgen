<?php
/**
 * This class hooks into the 'all_class_info' hook and scans through all of the
 * classes, linking the children to their parent classes so that their parent
 * classes contain an array of all of their children.
 */
class AddChildClassesPlugin extends Docgen_Plugin {
    protected $name = 'Add Child Classes';

    public function onLoad() {
        $this->addAllClassInfoHook(array($this, 'addChildClasses'));
    }

    public function addChildClasses($all_class_info) {
        // Loop over all of the classes.
        foreach($all_class_info as $class) {
            // Because we want to modify the original array, we need to
            // do this really messy accessor stuff... ... Sorry. :p
            if ($class['parent'] != null && !empty($class['parent'])) {
                if (isset($all_class_info[$class['parent']])) {
                    if (isset($all_class_info[$class['parent']]['children'])) {
                        $all_class_info[$class['parent']]['children'][] = $class['name'];
                    } else {
                        $all_class_info[$class['parent']]['children'] = array($class['name']);
                    }
                }
            }

            if (!empty($class['interfaces'])) {
                foreach($class['interfaces'] as $interface) {
                    if (isset($all_class_info[$interface])) {
                        if (isset($all_class_info[$interface]['children'])) {
                            $all_class_info[$interface]['children'][] = $class['name'];
                        } else {
                            $all_class_info[$interface]['children'] = array($class['name']);
                        }
                    }
                }
            }
        }

        // Return the class info.
        return $all_class_info;
    }
}

Docgen_Plugins::register(new AddChildClassesPlugin());

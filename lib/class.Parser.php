<?php
require_once dirname(__FILE__) . '/../extlib/dwoo/dwooAutoload.php';

class Parser {

    private $class_list;
    private $dwoo;

    public function __construct($class_list) {
        $this->class_list = $class_list;
        $this->dwoo = new Dwoo();
        $this->loadClasses();
    }

    /**
     * Loads all of the classes in the $this->class_list variable.
     */
    private function loadClasses() {
        foreach($this->class_list as $name => $file) {
            require_once $file;
        }

        if (isset($this->class_list['init'])) {
            unset($this->class_list['init']);
        }
    }

    public function parseAll($template, $to) {
        foreach($this->class_list as $class_name => $file) {
            $this->parseClass($class_name, $template, $to);
        }
    }

    public function parseClass($class_name, $template, $to) {
        echo "Parsing $class_name... ";

        $class = new ClassParser($class_name);
        $to = str_replace(':class_name', $class_name, $to);

        if (!file_exists(dirname($to))) {
            if (!mkdir(dirname($to), $mode = 0777, $recursive = true)) {
                throw Exception('Unable to create directory ' . dirname($to));
            }
        }

        $data = $this->dwoo->get($template, $class->templateInfo());
        file_put_contents($to, $data);
        echo "Finished. Saved to $to\n\n";
    }

    public function parse($template, $data) {
        return $this->dwoo->get($template, $data);
    }

    public function generateTocTree($to) {
        echo 'Generating TOC tree... ';
        $classes = array('classes' => array());
        foreach($this->class_list as $class_name => $file) {
            $info = array();
            $info["name"] = $class_name;
            $info["location"] = $file;
            $classes['classes'][] = $info;
        }

        $data = $this->parse(dirname(__FILE__) . '/../templates/rst_toc.tpl', $classes);
        file_put_contents($to, $data);
        echo 'Finished.' . "\n";
    }
}

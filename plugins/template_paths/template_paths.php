<?php

class TemplatePaths extends Docgen_Plugin {
    protected $name = 'Template Paths';

    public function version() {
        return '0.1';
    }

    public function onLoad() {
        $this->addCompilerHook(array($this, 'addPreProcessor'));
    }

    public function addPreProcessor(Dwoo_Compiler $compiler) {
        $compiler->addPreProcessor(array($this, 'preProcess'));
        return $compiler;
    }

    public function preProcess(Dwoo_Compiler $compiler, $template) {
        $rst = Docgen::templateRstDir();

        $replacements = array(
            '$docgen.template.rst.property' => "'" . $rst . "property.tpl'",
            '$docgen.template.rst.class' => "'" . $rst . "class.tpl'",
            '$docgen.template.rst.class.source' => "'" . $rst . "class_source.tpl'",
            '$docgen.template.rst.method' => "'" . $rst . "method.tpl'"
        );

        foreach($replacements as $search => $replace) {
            $template = str_replace($search, $replace, $template);
        }

        return $template;
    }
}

Docgen_Plugins::register(new TemplatePaths());

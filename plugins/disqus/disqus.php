<?php

class DisqusIntegration extends Docgen_Plugin {
    protected $name = 'Disqus Integration';

    private $config = array();

    public function onLoad() {
        $this->addCompilerHook(array($this, 'addDisqusPreProcessor'));
        $this->addClassInfoHook(array($this, 'addDisqusConfig'));

        // Edit the shortname to whatever shortname you registered on disqus
        $this->config['shortname'] = 'thinkupdocs';

        // This should point to the disqus.tpl file, correct by default but it
        // you want to move the disqus.tpl, you will need to edit this.
        $this->config['template'] = realpath(dirname(__FILE__) . '/../../templates/disqus.tpl');
    }

    /**
     * This method adds the appropriate disqus configuration variables to each
     * class information array. In the templates, you can access the disqus config
     * directly by doing:
     *
     * $disqus.id or
     * $disqus.shortname
     *
     * But this is not recommended. This plugin also introduced a pre processor to
     * the Dwoo engine the converts {disqus} tags into the correct template syntax
     * to add disqus integration to your templates.
     */
    public function addDisqusConfig($class) {
        $class['disqus'] = $this->config;
        $class['disqus']['id'] = $class['name'];

        return $class;
    }

    public function addDisqusPreProcessor(Dwoo_Compiler $compiler) {
        $compiler->addPreProcessor(array($this, 'disqusPreProcessor'));
        return $compiler;
    }

    /**
     * The disqus pre processor simply replaces instances of {disqus} with
     * the correct Dwoo template code to generate disqus comment integration.
     *
     * So wherever you put {disqus}, you will see the disqus comment system.
     */
    public function disqusPreProcessor(Dwoo_Compiler $compiler, $template) {
        $replacement = '{if $disqus}
{include (file=$disqus.template assign=\'disqus_template\')}
.. raw:: html

{indent $disqus_template}
{/if}';

        return preg_replace('/\{disqus\}/', $replacement, $template);
    }
}

Docgen_Plugins::register(new DisqusIntegration());

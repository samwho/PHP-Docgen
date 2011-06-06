<?php

class Application {
    protected $args;
    protected $log;

    public function __construct($parsed_args) {
        $this->args = $parsed_args;
        $this->initialize();
    }

    public function go() {
        // Require the call to have a glob, a template and a file to output to.
        if (!isset($this->args['glob']) ||
            !isset($this->args['template']) ||
            !isset($this->args['file'])) {

            $this->log->error('Incorrect script usage. Please run php_docgen --help for more details.');
            exit;
        }

        // Extract command line args into separate variables.
        $glob = $this->args['glob'];
        $template = $this->args['template'];
        $output_file = $this->args['file'];

        // Fail if the template specified does not exist.
        if (!file_exists($template)) {
            $this->log->error('The template "' . $template . '" could not be found. Are you sure that' .
                ' you specified a valid path?');
            exit;
        }

        // Search for classes in the glob specified.
        $search = new CodeSearch();
        $search->findClasses($glob);

        // Add an init script if one is specified. An init script is something that your
        // code may require to load classes correctly. Such as a script that registers
        // a class loader, maybe.
        if (isset($this->args['init'])) {
            $search->addInitScript($this->args['init']);
        }

        // Parse the class list and output them to files.
        $parser = new Parser($search->getClassList());
        $parser->parseAllToFile($template, $output_file);

        // Generate a toctree if the --toctree argument is passed.
        if (isset($this->args['toctree'])) {
            $parser->generateTocTree(dirname($output_file) . 'index.rst');
        }
    }

    private function initialize() {
        // Set the color settings.
        $color = Color::getInstance();
        if (isset($this->args['color']) || isset($this->args['c'])) {
            $color->setEnabled(true);
        } else {
            $color->setEnabled(false);
        }

        // Echo a help text if -h or --help is specified.
        if (isset($this->args['h']) || isset($this->args['help'])) {
            $this->help();
            exit;
        }

        // Load the plugins. This needs to be done early.
        Plugins::loadAll();

        // Get the main application log object.
        $this->log = Log::getMainLog();
    }

    private function help() {
        //TODO Write this.
    }
}

<?php

abstract class Abstract_Docgen_Application {
    /**
     * @var array An array of parsed command line arguments. See
     * Docgen_CommandLineUtils::parseArgs().
     */
    protected $args;
    /**
     * @var Docgen_Log Main application log for writing to.
     */
    protected $log;

    /**
     * @var Docgen_CodeSearch
     */
    protected $search;

    /**
     * @var Docgen_Parser
     */
    protected $parser;

    /**
     * @var string Path to the docgen base directory.
     */
    protected $docgen_base_dir;

    /**
     * @var string Path to the docgen default templates.
     */
    protected $docgen_template_dir;

    /**
     * The constructor handles parsing command line arguments (if they are not
     * passed in to the constructor explicitly), storing those args in the
     * $this->args instance variable and calling the initialize method of
     * this class.
     *
     * Please remember to tell this constructor if you are going to write
     * your own constructor in your inheriting class.
     *
     * @param array $parsed_args Command line arguments as they would be if they
     * had been parsed with Docgen_CommandLineUtils::parseArgs().
     */
    public function __construct($parsed_args = null) {
        // If no argument is passed to the constructor, parse the command
        // line arguments personally.
        if (is_null($parsed_args)) {
            global $argv;
            $parsed_args = Docgen_CommandLineUtils::parseArgs($argv);
        }

        $this->args = $parsed_args;
        $this->initialize();
    }

    /**
     * This is the function that will execute the logic of your Docgen
     * application.
     */
    abstract public function go();

    /**
     * This method initializes the Docgen application. It is very important that,
     * if you are overriding this for any reason, you either implement its functionality
     * or you call it first in your overridden version.
     *
     * This method is responsible for loading plugins, parsing the command line
     * args for color and help and instantiating the application log.
     */
    protected function initialize() {
        // Set the color settings.
        $color = Docgen_Color::getInstance();
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
        Docgen_Plugins::loadAll();

        // Get the main application log object.
        $this->log = Docgen_Log::getMainLog();

        $this->search = new Docgen_CodeSearch();
        $this->parser = new Docgen_Parser();

        $this->docgen_base_dir = realpath(dirname(__FILE__) . '/../');
        $this->docgen_template_dir = $this->docgen_base_dir . '/templates';
    }

    /**
     * This method gets called if -h or --help is passed in at the command line.
     *
     * It does not get automatically echoed, it is your responsibility to output
     * the help to the screen.
     *
     * The program does, however, exit when this function is returned from.
     */
    protected function help() {
        echo "Help has not been implemented for this application yet.\n";
    }

}

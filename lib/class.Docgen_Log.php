<?php
class Docgen_Log {
    private $messages = array();
    private $color = null;
    private $prefix = '';

    private static $main_app_log = null;
    private static $plugin_log = null;

    /**
     * This returns the log instance that is used to log messages and errors
     * in the main application code.
     *
     * Please don't add to this. It is intended as read only :) To log for
     * plugins, use Log::getPluginLog() and write to that.
     *
     * @return Log $main_app_log The main log instance for the application.
     */
    public static function getMainLog() {
        if (is_null(self::$main_app_log)) {
            self::$main_app_log = new Docgen_Log('[MAIN]');
        }

        return self::$main_app_log;
    }

    /**
     * This returns the log instance that is used to log messages and errors
     * in plugins.
     *
     * If you are writing a plugin and want to log messages, this is for you.
     *
     * @return Log $main_app_log The main log instance for the application.
     */
    public static function getPluginLog() {
        if (is_null(self::$plugin_log)) {
            self::$plugin_log = new Docgen_Log('[PLUGIN]');
        }

        return self::$plugin_log;
    }

    /**
     * Construct a Log instance with a specified prefix. This prefix
     * will be prepended to every log message.
     */
    public function __construct($prefix) {
        $this->color = Docgen_Color::getInstance();
        $this->prefix = $prefix;
    }

    /**
     * This class stores logged messages into an array. This method gets
     * the array of stored messages.
     *
     * @return array Messages that have been logged using this class.
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Log an error messsage. This will be prepended with [ERROR] and, if coloring
     * is enabled, will appear in red.
     *
     * @param string $message The message to be logged.
     * @param bool $echo Whether or not to echo to the screen. True by default.
     */
    public function error($message, $echo = true) {
        $this->log('[ERROR] ' . $message, $echo, 'red');
    }

    /**
     * Log a messsage.
     *
     * @param string $message The message to be logged.
     * @param bool $echo Whether or not to echo to the screen. True by default.
     */
    public function message($message, $echo = true) {
        $this->log($message, $echo);
    }

    /**
     * Saves this log to file. When messages get logged, this class saves them into
     * an array (so doing a lot of logging probably isn't recommended as messages are
     * held in memory). Calling this will implode that array, joined by new lines, and
     * write it to the $file parameter specified.
     *
     * Fails if the file cannot be created.
     *
     * @param string $file Path to the file to save to.
     * @param int $flags This method writes the file with the file_put_contents
     * function. This parameter gets passed as-is as the $flags argument to that
     * function. php.net has a good reference on what can go in here.
     * @return int Returns whatever value the call to file_put_contents returns.
     */
    public function saveToFile($file, $flags = 0) {
        return file_put_contents($file, implode("\n", $this->messages), $flags);
    }

    private function log($message, $echo, $color = null) {
        // Get the debug backtrace
        $backtrace = debug_backtrace();
        // Go up two function calls
        $backtrace = $backtrace[1];

        // Build the message to display.
        $message = $this->prefix . ' ' . $backtrace['file'] . ' on line ' .
            $backtrace['line'] . ': ' . $message;

        // Color the message if a color is passed in.
        if (!is_null($color)) {
            $message = $this->color->getColoredString($message, $color);
        }

        // Add the message to the message array.
        $this->messages[] = $message;

        // Echo the message if specified to do so.
        if ($echo) echo $message . "\n";
    }
}

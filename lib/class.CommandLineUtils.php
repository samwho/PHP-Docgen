<?php

class CommandLineUtils {
    /**
     * A function that takes an array of command line arguments
     * and parses them into something meaningful.
     *
     * Examples:
     *
     * $ php test.php --foo --bar=baz
     * ["foo"]   => true
     * ["bar"]   => "baz"
     *
     * $ php test.php -abc
     * ["a"]     => true
     * ["b"]     => true
     * ["c"]     => true
     *
     * $ php test.php arg1 arg2 arg3
     * [0]       => "arg1"
     * [1]       => "arg2"
     * [2]       => "arg3"
     *
     * Found at http://php.net/manual/en/features.commandline.php
     *
     * @param array $argv An array of command line variables.
     * @return array A nicer, more meaningful array of command line vars.
     */
    public static function parseArgs($argv){
        array_shift($argv);
        $out = array();
        foreach ($argv as $arg){
            if (substr($arg,0,2) == '--'){
                $eqPos = strpos($arg,'=');
                if ($eqPos === false){
                    $key = substr($arg,2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                } else {
                    $key = substr($arg,2,$eqPos-2);
                    $out[$key] = substr($arg,$eqPos+1);
                }
            } else if (substr($arg,0,1) == '-'){
                if (substr($arg,2,1) == '='){
                    $key = substr($arg,1,1);
                    $out[$key] = substr($arg,3);
                } else {
                    $chars = str_split(substr($arg,1));
                    foreach ($chars as $char){
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            } else {
                $out[] = $arg;
            }
        }
        return $out;
    }
}

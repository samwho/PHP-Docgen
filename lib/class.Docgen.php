<?php

class Docgen {
    const VERSION = '0.1';

    public static function baseDir() {
        return realpath(dirname(__FILE__) . '/..') . '/';
    }

    public static function templateDir() {
        return self::baseDir() . 'templates/';
    }

    public static function pluginDir() {
        return self::baseDir() . 'plugins/';
    }

    public static function libDir() {
        return self::baseDir() . 'lib/';
    }

    public static function extlibDir() {
        return self::baseDir() . 'extlib/';
    }

    public static function templateRstDir() {
        return self::templateDir() . 'rst/';
    }
}

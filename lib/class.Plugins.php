<?php

class Plugins {

    /**
     * Loads all files ending in .php in the plugins directory.
     */
    public static function loadAll() {
        foreach(glob(dirname(__FILE__) . '/plugins/*.php') as $file) {
            require_once $file;
        }
    }
}

<?php

class TestOfHooks extends UnitTestCase {
    /**
     * @var mixed A variable to store your callback results in, to test
     * your callbacks are actually doing something.
     */
    private $callback_result = null;

    /**
     * @var int The number of times hooks are called. Used in the multiple
     * hook test.
     */
    private $hook_calls = 0;

    /**
     * @var array A backup of whatever is in the Hooks array before starting
     * these tests.
     */
    private $hooks_backup;

    /**
     * Set up and tear down in this class are designed to give you a totally empty
     * Docgen_Hooks class during these tests. First it saves the current state of the
     * hooks, and at the end it restores the state.
     *
     * This is so that later tests that rely on core plugins will not break due to
     * the Hooks being reset.
     */
    public function setUp() {
        $this->hooks_backup = Docgen_Hooks::getAllHooks();
        Docgen_Hooks::reset();
    }

    public function tearDown() {
        Docgen_Hooks::restoreHooks($this->hooks_backup);
        $this->callback_result = null;
        $this->hook_calls = 0;
    }

    public function callback() {
         $this->callback_result = "Hello, world!";
         $this->hook_calls++;
    }

    public function callbackWithArg($arg) {
         $this->callback_result = $arg;
         $this->hook_calls++;
         return $arg;
    }

    public function testAddHook() {
        $this->assertFalse(Docgen_Hooks::exists('test_hook'),
            'Hook already exists for some reason.');
        Docgen_Hooks::add('test_hook', array($this, 'callback'));
        $this->assertTrue(Docgen_Hooks::exists('test_hook'),
            'Hook not added for some reason.');
    }

    public function testCallHook() {
        Docgen_Hooks::add('test_hook', array($this, 'callback'));
        Docgen_Hooks::call('test_hook');
        $this->assertEqual('Hello, world!', $this->callback_result);
    }

    public function testHookWithReturnValue() {
        Docgen_Hooks::add('test_hook', array($this, 'callbackWithArg'));
        $return = Docgen_Hooks::call('test_hook', array('arg'));
        $this->assertEqual($return, 'arg');
    }

    public function testMultipleHookCalls() {
        Docgen_Hooks::add('test_hook', array($this, 'callbackWithArg'));
        Docgen_Hooks::add('test_hook', array($this, 'callbackWithArg'));

        Docgen_Hooks::call('test_hook', array('arg'));

        $this->assertEqual($this->hook_calls, 2);
    }
}

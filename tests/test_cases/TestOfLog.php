<?php

class TestOfLog extends UnitTestCase {
    private $log;

    public function setUp() {
        $this->log = Docgen_Log::getMainLog();
    }

    public function tearDown() {
        $this->log->reset();
    }

    public function testLogMessage() {
        $this->log->message('Test log message.', $echo = false);

        $this->assertEqual(sizeof($this->log->getMessages()), 1);
        $this->assertPattern('/Test log message/', $this->log->getLastMessage());
    }

    public function testLogMultipleMessages() {
        $this->log->message('Test log message 1.', $echo = false);
        $this->log->message('Test log message 2.', $echo = false);

        $this->assertEqual(sizeof($this->log->getMessages()), 2);
        $this->assertPattern('/Test log message 2/', $this->log->getLastMessage());
    }

    public function testLogError() {
        $this->log->error('Test log message.', $echo = false);

        $this->assertEqual(sizeof($this->log->getMessages()), 1);
        $this->assertPattern('/Test log message/', $this->log->getLastMessage());
    }

    public function testMainAndPluginLogsDifferent() {
        $this->assertNotEqual(Docgen_Log::getMainLog(), Docgen_Log::getPluginLog());
    }

    public function testSaveToFile() {
        if (!is_writable('.')) {
            echo "Skipping testSaveToFile. Directory not writable.\n";
            return;
        }

        $this->log->message('Test log message 1.', $echo = false);
        $this->log->message('Test log message 2.', $echo = false);

        $this->log->saveToFile('./test_log.log');
        $log_file = file_get_contents('./test_log.log');

        $this->assertPattern('/Test log message 1/', $log_file);
        $this->assertPattern('/Test log message 2/', $log_file);

        unlink('./test_log.log');
    }
}

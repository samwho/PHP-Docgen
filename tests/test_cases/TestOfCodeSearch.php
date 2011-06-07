<?php

class TestOfCodeSearch extends UnitTestCase {
    /**
     * @var Docgen_CodeSearch
     */
    private $search;
    private $test_class_dir;

    public function setUp() {
        $this->search = new Docgen_CodeSearch();

        // Get the test class directory.
        $this->test_class_dir = realpath(dirname(__FILE__) . '/../test_classes');
    }
    public function tearDown() {
        unset($this->search);
    }

    function testSearchClasses() {
        // Search for a single class.
        $this->search->findClasses($this->test_class_dir . '/class.TestClass.php');

        // Create the array output that the class search should have generated.
        $comparison = array(
            $this->test_class_dir . '/class.TestClass.php' => array(
                'TestClass'
            )
        );

        // Assert that they are equal.
        $this->assertEqual($this->search->getClassList(), $comparison,
            'Single class search does not match expected result.');
    }

    function testSearchTwoClassesOneFile() {
        // Search for a file with 2 classes in it.
        $this->search->findClasses($this->test_class_dir . '/class.TwoInOne.php');

        // Create the array output that the class search should have generated.
        $comparison = array(
            $this->test_class_dir . '/class.TwoInOne.php' => array(
                'One',
                'Two'
            )
        );

        // Assert that they are equal.
        $this->assertEqual($this->search->getClassList(), $comparison,
            'Two in one class list does not match the expected result.');
    }

    function testExecutingTwoClassSearches() {
         // Search for a file with 2 classes in it.
        $this->search->findClasses($this->test_class_dir . '/class.TwoInOne.php');
        // Search for a single class.
        $this->search->findClasses($this->test_class_dir . '/class.TestClass.php');

        // Create the array output that the class search should have generated.
        $comparison = array(
            $this->test_class_dir . '/class.TwoInOne.php' => array(
                'One',
                'Two'
            ),
            $this->test_class_dir . '/class.TestClass.php' => array(
                'TestClass'
            )
        );

        // Assert that they are equal.
        $this->assertEqual($this->search->getClassList(), $comparison,
            'Executing two searches does not yield the expected result.');

    }
}

<?php

class TestOfParserUtils extends UnitTestCase {
    public function testStripCommentStars() {
        $comment = '/**
            * This is a comment.
            *
            * With another line.
            * And another.
            *
            */';

        $comment_sans_stars = Docgen_ParserUtils::stripCommentStars($comment);

        // The \n's won't be literal. They will be actual new lines.
        $expected = "This is a comment.\n\nWith another line.\nAnd another.";

        $this->assertEqual($comment_sans_stars, $expected);
    }

    public function testGotDocTags() {
        $comment = '/**
            * This is a comment with tags.
            *
            * @param string $param A parameter.
            * @author Sam Rose <samwho@lbak.co.uk>
            */';

        // Need to strip the stars before getting the tags.
        $comment = Docgen_ParserUtils::stripCommentStars($comment);
        $tags = Docgen_ParserUtils::getDocTags($comment);

        $expected = array(
            array(
                'full_tag' => '@param string $param A parameter.',
                'name' => 'param',
                'contents' => 'string $param A parameter.'
            ),
            array(
                'full_tag' => '@author Sam Rose <samwho@lbak.co.uk>',
                'name' => 'author',
                'contents' => 'Sam Rose <samwho@lbak.co.uk>'
            )
        );

        $this->assertEqual($tags, $expected);
    }

    public function testRemoveDocTags() {
        $comment = '/**
            * This is a comment with tags.
            *
            * @param string $param A parameter.
            * @author Sam Rose <samwho@lbak.co.uk>
            */';

        // Need to strip the stars before getting the tags.
        $comment = Docgen_ParserUtils::stripCommentStars($comment);

        $comment_sans_tags = Docgen_ParserUtils::removeDocTags($comment);

        $expected = "This is a comment with tags.";

        $this->assertEqual($comment_sans_tags, $expected);
    }

    /**
     * Test that the indentJSON function does not break the JSON output.
     */
    public function testIndentJSON() {
        $test_array = array(
            'hello' => 'yay',
            'good' => array(
                'yes',
                'no'
            ),
            array(
                array(
                    array(
                        array(
                            'nested' => 'fo sho'
                        )
                    )
                )
            ),
            'more' => 'array'
        );

        $json = json_encode($test_array);
        $json = Docgen_ParserUtils::indentJSON($json);
        $comparison_array = json_decode($json, $assoc = true);

        $this->assertEqual($test_array, $comparison_array);
    }
}

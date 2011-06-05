<?php
class ParserUtils {

    private static $tag_match_regex = '/^\s*@([a-z]+)\s+(.*?)(?=\z|^\s*@[a-z]+\s)/sm';

    /**
     * Strips the stars and forward slashes from multiline comments.
     *
     * @param string $comment The comment to strip.
     * @return string The comment passed in but without the stars and slashes
     * that denote the fact that it's a comment.
     */
    public static function stripCommentStars($comment) {
        return preg_replace('/^\s*\/?\*{1,2}\s?\/?/m', '', $comment);
    }

    /**
     * Gets the @tags from a comment.
     *
     * Function assumes that the comment has already been stripped with the
     * stripCommentStars function of this class.
     */
    public static function getDocTags($comment) {
        preg_match_all(self::$tag_match_regex, $comment, $matches);
        if (!empty($matches[1])) {
            $return = array();
            foreach ($matches[0] as $key => $full_match) {
                $return[$key] = array(
                    'full_tag' => trim($full_match),
                    'name' => trim($matches[1][$key]),
                    'contents' => trim($matches[2][$key])
                );
            }
            return $return;
        } else {
            return array();
        }
    }

    public static function removeDocTags($comment) {
        return preg_replace(self::$tag_match_regex, '', $comment);
    }

    public static function removeDocStarsAndTags($comment) {
        return trim(self::removeDocTags(self::stripCommentStars($comment)));
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @author http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     * @param string $json The original JSON string to process.
     * @return string Indented version of the original JSON string.
     */
    public static function indentJSON($json) {

        $result = '';
        $pos = 0;
        $str_len = strlen($json);
        $indent_str = '    ';
        $new_line = "\n";
        $prev_char = '';
        $prev_prev_char = '';
        $out_of_quotes = true;

        for ($i = 0; $i <= $str_len; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"') {
                if ( $prev_char != "\\") {
                    $out_of_quotes = !$out_of_quotes;
                } elseif ($prev_prev_char == "\\") {
                    $out_of_quotes = !$out_of_quotes;
                }
                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $out_of_quotes) {
                $result .= $new_line;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $out_of_quotes) {
                $result .= $new_line;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            $prev_prev_char = $prev_char;
            $prev_char = $char;
        }

        return $result;
    }

}

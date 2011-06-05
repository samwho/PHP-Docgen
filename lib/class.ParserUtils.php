<?php

class ParserUtils {
    /**
     * Strips the stars and forward slashes from multiline comments.
     *
     * @param string $comment The comment to strip.
     * @return string The comment passed in but without the stars and slashes
     * the denote the fact that it's a comment.
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
        preg_match_all('/@([a-z]+)\s+(.*?)\s*(?=$|@[a-z]+\s)/s', $comment, $matches);
        return array_combine($matches[1], $matches[2]);
    }

}

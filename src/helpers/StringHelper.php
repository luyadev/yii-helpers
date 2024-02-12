<?php

namespace luya\yii\helpers;

use yii\helpers\BaseStringHelper;

/**
 * Helper methods when dealing with Strings.
 *
 * Extends the {{yii\helpers\StringHelper}} class by some useful functions like:
 *
 * + {{luya\yii\helpers\StringHelper::typeCast()}}
 * + {{luya\yii\helpers\StringHelper::isFloat()}}
 * + {{luya\yii\helpers\StringHelper::replaceFirst()}}
 * + {{luya\yii\helpers\StringHelper::contains()}}
 * + {{luya\yii\helpers\StringHelper::startsWithWildcard()}}
 * + {{luya\yii\helpers\StringHelper::typeCastNumeric()}}
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class StringHelper extends BaseStringHelper
{
    /**
     * Convert a YouTube link to an embeddable video URL.
     *
     * If the given input URL is invalid, `false` is returned.
     *
     * @param string $url
     * @return string|boolean
     * @see https://stackoverflow.com/a/48130447
     */
    public static function toYouTubeEmbed($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);

        if (isset($match[1])) {
            return 'https://www.youtube.com/embed/' . $match[1];
        }

        return false;
    }
    /**
     * TypeCast a string to its specific types.
     *
     * Arrays will passed to the {{luya\yii\helpers\ArrayHelper::typeCast()}} class.
     *
     * @param mixed $string The input string to type cast. Arrays will be passed to {{luya\yii\helpers\ArrayHelper::typeCast()}}.
     * @return mixed The new type casted value, if the input is an array the output is the typecasted array.
     */
    public static function typeCast($string)
    {
        if (is_numeric($string)) {
            return static::typeCastNumeric($string);
        } elseif (is_array($string)) {
            return ArrayHelper::typeCast($string);
        }

        return $string;
    }

    /**
     * String Wildcard Check.
     *
     * Checks whether a strings starts with the wildcard symbol and compares the string before the wild card symbol `*`
     * with the string provided. If there is NO wildcard symbol it always returns `false`.
     *
     *
     * @param string $string The string which should be checked with $with comparator
     * @param string $with The with string which must end with the wildcard symbol `*` e.g. `foo*` would match string `foobar`.
     * @param boolean $caseSensitive Whether to compare the starts with string as case-sensitive or not, defaults to `true`.
     * @return boolean Whether the string starts with the wildcard marked string or not, if no wildcard symbol is contained.
     * in the $with it always returns `false`.
     */
    public static function startsWithWildcard($string, $with, $caseSensitive = true)
    {
        if (substr($with, -1) != "*") {
            return false;
        }

        return self::startsWith($string, rtrim($with, '*'), $caseSensitive);
    }



    /**
     * See if filter conditions match the given value.
     *
     * Example filter conditions:
     *
     * + `cms_*` matches everything starting with "cms_".
     * + `cms_*,admin_*` matches booth cms_* and admin_* tables.
     * + `!cms_*` matches all not start with "cms_"
     * + `!cms_*,!admin_*` matches all not starting with "cms_" and not starting with "admin_"
     * + `cms_*,!admin_*` matches all start with "cms_" but not start with "admin_"
     *
     * Only first match is relevant:
     *
     * + "cms_*,!admin_*,admin_*" include all cms_* tables but exclude all admin_* tables (last match has no effect)
     * + "cms_*,admin_*,!admin_*" include all cms_* and admin_* tables (last match has no effect)
     *
     * Example using condition string:
     *
     * ```php
     * filterMatch('hello', 'he*'); // true
     * filterMatch('hello', 'ho,he*'); // true
     * filterMatch('hello', ['ho', 'he*']); // true
     * ```
     *
     * @param $value The value on which the filter conditions should be applied on.
     * @param array|string $conditions An array of filter conditions, if a string is given he will be exploded by commas.
     * @param boolean $caseSensitive Whether to match value even when lower/upper case is not correct/same.
     * @return bool Returns `true` if one of the given filter conditions matches.
     */
    public static function filterMatch($value, $conditions, $caseSensitive = true)
    {
        if (is_scalar($conditions)) {
            $conditions = self::explode($conditions, ",", true, true);
        }

        foreach ($conditions as $condition) {
            $isMatch = true;
            // negate
            if (substr($condition, 0, 1) == "!") {
                $isMatch = false;
                $condition = substr($condition, 1);
            }
            if ($caseSensitive) {
                $condition = strtolower($condition);
                $value = strtolower($value);
            }
            if ($condition == $value || self::startsWithWildcard($value, $condition)) {
                return $isMatch;
            }
        }

        return false;
    }

    /**
     * TypeCast a numeric value to float or integer.
     *
     * If the given value is not a numeric or float value it will be returned as it is. In order to find out whether it's float
     * or not use {{luya\yii\helpers\StringHelper::isFloat()}}.
     *
     * @param mixed $value The given value to parse.
     * @return mixed Returns the original value if not numeric or integer, float casted value.
     */
    public static function typeCastNumeric($value)
    {
        if (!self::isFloat($value)) {
            return $value;
        }

        if (intval($value) == $value) {
            return (int) $value;
        }

        return (float) $value;
    }

    /**
     * Checks whether a string is a float value.
     *
     * Compared to `is_float()` function of PHP, it only ensures whether the input variable is type float.
     *
     * @param mixed $value The value to check whether it's float or not.
     * @return boolean Whether it's a float value or not.
     */
    public static function isFloat($value)
    {
        if (is_float($value)) {
            return true;
        }

        if (!is_array($value) && preg_match('/^\d+\.$/', $value)) {
            // ordinal number of the form cardinal number followed by point, e.g. "24."
            return false;
        }

        return ($value == (string)(float) $value);
    }

    /**
     * Replace only the first occurrence found inside the string.
     *
     * The replace first method is *case-sensitive*.
     *
     * ```php
     * StringHelper::replaceFirst('abc', '123', 'abc abc abc'); // returns "123 abc abc"
     * ```
     *
     * @param string $search Search string to look for.
     * @param string $replace Replacement value for the first found occurrence.
     * @param string $subject The string you want to look up to replace the first element.
     * @return mixed Replaced string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        return preg_replace('/'.preg_quote($search, '/').'/', $replace, $subject, 1);
    }

    /**
     * Check whether a char or word exists in a string or not.
     *
     * This method is case-sensitive. The need can be an array with multiple chars or words who
     * are going to look up in the haystack string.
     *
     * If an array of needle words is provided the $strict parameter defines whether all need keys must be found
     * in the string to get the `true` response or if just one of the keys are found the response is already `true`.
     *
     * ```php
     * if (StringHelper::contains('foo', 'the foo bar Bar'')) {
     *    echo "yes!";
     * }
     * ```
     *
     * check if one of the given needles exists:
     *
     * ```php
     * if (StringHelper::contains(['jungle', 'hell0], 'Welcome to the jungle!)) {
     *    echo "yes!";
     * }
     * ```
     *
     * @param string|array $needle The char or word to find in the $haystack. Can be an array to multi find words or char in the string.
     * @param string $haystack The haystack where the $needle string should be looked up. A string or phrase with words.
     * @param boolean $strict If an array of needles is provided the $strict parameter defines whether all keys must be found ($strict = `true`) or just one result must be found ($strict = `false`).
     * @return boolean If an array of values is provided the response may change depending on $findAll.
     */
    public static function contains($needle, $haystack, $strict = false)
    {
        $needles = (array) $needle;

        $state = false;

        foreach ($needles as $item) {
            $state = (strpos($haystack, (string) $item) !== false);

            if ($strict && !$state) {
                return false;
            }

            if (!$strict && $state) {
                return true;
            }
        }

        return $state;
    }

    /**
     * "Minify" html content.
     *
     * + remove space
     * + remove tabs
     * + remove newlines
     * + remove HTML comments
     *
     * @param string $content The content to minify.
     * @param array $options Optional arguments to provide for minification:
     * - comments: boolean, where HTML comments should be removed or not, defaults to `false`.
     * @return mixed Returns the minified content.
     */
    public static function minify($content, array $options = [])
    {
        $min = preg_replace(['/[\n\r]/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', ], ['', '>', '<', '\\1'], trim($content));
        $min = str_replace(['> <'], ['><'], $min);

        if (ArrayHelper::getValue($options, 'comments', false)) {
            $min = preg_replace('/<!--(.*)-->/Uis', '', $min);
        }

        return $min;
    }

    /**
     * Cut the given word/string from the content. It's truncated to the left side and to the right side of the word.
     *
     * An example of how a sentence is cutted:
     *
     * ```php
     * $cut = StringHelper::truncateMiddle('the quick fox jumped over the lazy dog', 'jumped', 12);
     * echo $cut; // ..e quick fox jumped over the la..
     * ```
     *
     * @param string $content The content to cut the words from.
     * @param string $word The word which should be in the middle of the string.
     * @param integer $length The amount of chars to cut on the left and right side from the word.
     * @param string $affix The chars which should be used for prefix and suffix when string is cutted.
     * @param boolean $caseSensitive Whether the search word in the string even when lower/upper case is not correct.
     */
    public static function truncateMiddle($content, $word, $length, $affix = '..', $caseSensitive = false)
    {
        $content = strip_tags($content);
        $array = self::mb_str_split($content);
        $first = mb_strpos($caseSensitive ? $content : mb_strtolower($content), $caseSensitive ? $word : mb_strtolower($word));

        // we could not find any match, therefore use casual truncate method.
        if ($first === false) {
            // as the length value in truncate middle stands for to the left and to the right, we multiply this value with 2
            return self::truncate($content, ($length * 2), $affix);
        }

        $last = $first + mb_strlen($word);

        // left and right array chars from word
        $left = array_slice($array, 0, $first, true);
        $right = array_slice($array, $last, null, true);
        $middle = array_splice($array, $first, mb_strlen($word));

        // string before
        $before = (count($left) > $length) ? $affix.implode("", array_slice($left, -$length)) : implode("", $left);
        $after = (count($right) > $length) ? implode("", array_slice($right, 0, $length)) . $affix : implode("", $right);

        return $before . implode("", $middle) . $after;
    }

    /**
     * Highlight a word within a content.
     *
     * Since version 1.0.14 it's possible to provide an array with words to highlight.
     *
     * > This function IS NOT case-sensitive!
     *
     * ```php
     * StringHelper::highlightWord('Hello John!', 'john');
     * ```
     *
     * The above example would return `Hello <b>John</b>!`.
     *
     * @param string $content The content to find the word.
     * @param string|array $word The word to find within the content. It can be an array. If a word exists already in the list of words, this one will be stripped. e.g. `['test', 'testfoobar']` would remove `test` from the list as it exists in `testfoobar`.
     * @param string $markup The markup used wrap the word to highlight.
     */
    public static function highlightWord($content, $word, $markup = '<b>%s</b>')
    {
        $transliterateContent = Inflector::transliterate($content);
        $highlights = [];

        $words = array_unique((array) $word);

        // if there are multiple words, we need to ensure the same part of a word does not exists twice
        // otherwise this can generate wrong highlight results like a highlight inside of a highlight.
        if (count($words) > 1) {
            foreach ($words as $wordIndex => $word) {
                $inArrayIndex = preg_grep('/'.preg_quote($word, '/').'/', $words);
                if ((is_countable($inArrayIndex) ? count($inArrayIndex) : 0) > 1) {
                    unset($words[$wordIndex]);
                }
            }
        }

        foreach ($words as $word) {
            // search in content
            preg_match_all("/".preg_quote($word, '/')."+/i", $content, $matches);
            foreach ($matches[0] as $word) {
                $highlights[] = $word;
                // if the word is covered already, do not process further in foreach and break here
                break;
            }

            // search in transliterated content if not yet breaked from previous results
            preg_match_all("/".preg_quote($word, '/')."+/i", $transliterateContent, $matches);
            foreach ($matches[0] as $word) {
                $highlights[] = self::sliceTransliteratedWord($word, $transliterateContent, $content);
            }
        }

        // highlight all results in text with [[$word]]
        foreach (array_unique($highlights) as $highlight) {
            $content = str_replace($highlight, '[['.$highlight.']]', $content);
        }

        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches, PREG_SET_ORDER);

        $searchReplace = [];
        foreach ($matches as $match) {
            if (!array_key_exists($match[0], $searchReplace)) {
                $searchReplace[$match[0]] = sprintf($markup, $match[1]);
            }
        }

        foreach ($searchReplace as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    /**
     * Search a word within a transliterated text and cut out the original word in the original text.
     *
     * For example when you search for the transliterated word in text and want to return the original:
     *
     * ```php
     * StringHelper::sliceTransliteratedWord('frederic', 'Hello frederic', 'Hello fréderic');
     * ```
     *
     * The above example would return `fréderic`
     *
     * @param string $word
     * @param string $transliteratedText
     * @param string $originalText
     * @return string
     * @since 1.1.0
     */
    public static function sliceTransliteratedWord($word, $transliteratedText, $originalText)
    {
        return mb_substr($originalText, mb_strpos($transliteratedText, $word), mb_strlen($word));
    }

    /**
     * Multibyte-safe `str_split()` function.
     *
     * @param string $string The string to split into an array.
     * @param integer $length The length of the chars to cut.
     * @see https://www.php.net/manual/de/function.str-split.php#115703
     */
    public static function mb_str_split($string, $length = 1)
    {
        return preg_split('/(.{'.$length.'})/us', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * Check whether a value is numeric or not.
     *
     * There are situations where `is_numeric()` does not provide the desired result,
     * like for example `is_numeric('3e30')` would return true, as `e` can be considered
     * as exponential operator.
     *
     * Therefore this function checks with regex whether values or 0-9 if strict is enabled,
     * which is default behavior.
     *
     * @param mixed $value The value to check.
     * @param boolean $strict
     * @return boolean
     */
    public static function isNummeric($value, $strict = true)
    {
        if (!is_scalar($value)) {
            return false;
        }

        if (is_bool($value)) {
            return false;
        }

        if ($strict) {
            return preg_match('/^[0-9]+$/', $value) == 1 ? true : false;
        }

        return is_numeric($value);
    }

    /**
     * Templating a string with Variables
     *
     * The variables should be declared as `{{username}}` while the variables array key should contain `username`.
     *
     * Usage example:
     *
     * ```php
     * $content = StringHelper::template('<p>{{ name }}</p>', ['name' => 'John']);
     *
     * // output: <p>John</p>
     * ```
     *
     * If a variable is not found, the original curly brackets will be returned.
     *
     * @param string $template The template to parse. The template may contain double curly brackets variables.
     * @param array $variables The variables which should be available in the template.
     * @param boolean $removeEmpty Whether variables in double curly brackets should be removed, even the have not be assigned by $variables array.
     * @param string $leftDelimiter The delimiter for the variable on the left, default is `{{` {@since 1.2.0}
     * @param string $rightDelimiter The delimiter for the variable on the right, default is `}}` {@since 1.2.0}
     * @return string
     */
    public static function template($template, array $variables = [], $removeEmpty = false, $leftDelimiter = '{{', $rightDelimiter = '}}')
    {
        preg_match_all("/$leftDelimiter(.*?)$rightDelimiter/", $template, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $template;
        }

        foreach ($matches as $match) {
            $exposedVariableName = trim($match[1]);
            if (array_key_exists($exposedVariableName, $variables)) {
                $template = str_replace($match[0], (string) $variables[$exposedVariableName], $template);
            } elseif ($removeEmpty) {
                $template = str_replace($match[0], '', $template);
            }
        }

        return $template;
    }

    /**
     * Convert a text with different separators to an array.
     *
     * It's very common to use separators when working with user input, for example a list of domains separated by commas. Therefore
     * this function will use common separators the generate an array from a text string.
     *
     * Explodes the string by: "Newline", ";", ","
     *
     * + newline
     * + comma
     * + point comma
     *
     * @param string $text A text which contains a list of items separated by separators like commas.
     * @return array
     */
    public static function textList($text, array $separators = [PHP_EOL, "\n", "\r", "\n\r", ";", ","])
    {
        return StringHelper::explode(str_replace($separators, ';', $text), ";", true, true);
    }
}

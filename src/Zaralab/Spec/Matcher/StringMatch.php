<?php
/**
 * Project: zaralab
 * Filename: StringMatch.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;

use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert string is matching against provided regex.
 */
class StringMatch
{
    public function __invoke($string, $regex)
    {
        if (!is_string($string)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not a string", gettype($string))
            );
        }

        return preg_match($regex, $string);
    }
}
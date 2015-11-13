<?php
/**
 * Project: zaralab
 * Filename: StringEmpty.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;


use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert subject string is empty.
 */
class StringEmpty
{
    public function __invoke($string)
    {
        if (!is_string($string)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not a string",  gettype($string))
            );
        }

        return empty($string);
    }
}
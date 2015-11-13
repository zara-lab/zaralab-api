<?php
/**
 * Project: zaralab
 * Filename: StringMatchAny.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;

use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert any of the expressions match the subject string.
 */
class StringMatchAny
{
    public function __invoke($string, $expressions)
    {
        if (!is_string($string)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not a string", gettype($string))
            );
        }
        $matcher = new StringMatch();

        foreach($expressions as $regex)
        {
            if($matcher($string, $regex))
            {
                return true;
            }
        }

        return false;
    }
}
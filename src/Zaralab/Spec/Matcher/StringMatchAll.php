<?php
/**
 * Project: zaralab
 * Filename: StringMatchAll.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;


use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert all expressions match against the subject string.
 */
class StringMatchAll
{
    public function __invoke($string, $expressions)
    {
        if (!is_string($string)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not a string", gettype($string))
            );
        }

        $matcher = new StringMatch();

        foreach ($expressions as $regex) {
            if(!$matcher($string, $regex))
            {
                return false;
            }
        }

        return true;
    }
}
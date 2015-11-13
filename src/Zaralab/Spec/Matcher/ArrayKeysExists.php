<?php
/**
 * Project: zaralab
 * Filename: ArrayKeysExists.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;

use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert all keys exist in the subject array.
 */
class ArrayKeysExists
{
    public function __invoke($subject, $keys)
    {
        if (!is_array($subject)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not an array", gettype($subject))
            );
        }

        $matcher = new ArrayKeyExists();

        foreach($keys as $key)
        {
            if(!$matcher($subject, $key))
            {
                return false;
            }
        }

        return true;
    }
}
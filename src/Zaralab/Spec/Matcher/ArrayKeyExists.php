<?php
/**
 * Project: zaralab
 * Filename: ArrayKeyExists.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;

use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert key exists in the given
 * subject array.
 */
class ArrayKeyExists
{
    public function __invoke($subject, $key = null)
    {
        if (!is_array($subject)) {
            throw new MatcherException(
                sprintf("\t- subject \"%s\" not an array", gettype($subject))
            );
        }

        if (array_key_exists($key, $subject)) {
            return true;
        }

        do {
            $value = current($subject);
            if (is_array($value) && $this($value, $key)) {
                return true;
            }

        } while(next($subject));

        return false;
    }
}
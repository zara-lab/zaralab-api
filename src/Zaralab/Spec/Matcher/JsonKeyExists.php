<?php
/**
 * Project: zaralab
 * Filename: JsonKeyExists.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;

use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert key exists in the given
 * subject json.
 */
class JsonKeyExists
{
    public function __invoke($subject, $key)
    {
        $json = json_decode($subject, true);
        if (!is_array($json)) {
            throw new MatcherException(
                sprintf("\t- can not decode the JSON string \"%s\"", (string) $subject)
            );
        }
        $matcher = new ArrayKeyExists();

        return $matcher($json, $key);
    }
}
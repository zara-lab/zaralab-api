<?php
/**
 * Project: zaralab
 * Filename: JsonKeysExist.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec\Matcher;


use PhpSpec\Exception\Example\MatcherException;

/**
 * Assert all keys exist in the subject json array.
 */
class JsonKeysExist
{
    public function __invoke($subject, $keys)
    {
        $json = json_decode($subject, true);
        if (!is_array($json)) {
            throw new MatcherException(
                sprintf("\t- can not decode the JSON string \"%s\"", (string) $subject)
            );
        }

        $matcher = new ArrayKeyExists();

        foreach($keys as $key)
        {
            if(!$matcher($json, $key))
            {
                return false;
            }
        }

        return true;
    }
}
<?php
/**
 * Project: zaralab
 * Filename: MatcherInterface.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 12.11.15
 */

namespace Zaralab\Spec;

/**
 * Phpspec Matcher Interface.
 * @package Zaralab\Spec
 */
interface MatcherInterface
{
    public function __invoke($subject, $arg = null);
}
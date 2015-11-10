<?php
/**
 * Project: zaralab
 * Filename: functions.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 10.11.15
 */

/**
 * Use these to combine isset() and use of the set value. or defined and use of a constant
 * i.e. to fix  if($pref['foo']) ==> if ( varset($pref['foo']) ) will use the pref, or ''.
 * Can set 2nd param to any other default value you like (e.g. false, 0, or whatever)
 * $testvalue adds additional test of the value (not just isset())
 * WARNING - it triggers parse error if casting e.g. null as array
 * Examples:
 * <code>
 * $something = pref[key];  Notice if key not set     ==> $something = varset(pref);
 * $something = isset(pref) ? pref : "";              ==> $something = varset(pref);
 * $something = isset(pref) ? pref : default;         ==> $something = varset(pref,default);
 * $something = isset(pref) && pref ? pref : default; ==> use vartrue(pref,default)
 * $something = null; varset($something['key']        ==> Parse error!
 * </code>
 *
 * @param mixed $val
 * @param mixed $default [optional]
 *
 * @return mixed
 */
function varset(&$val, $default = null)
{
    if (isset($val)) {
        return $val;
    }

    return $default;
}

/**
 * Variant of {@link varset()}, but only return the value if both set AND not empty
 *
 * @param mixed $val
 * @param mixed $default [optional]
 *
 * @return mixed
 */
function vartrue(&$val, $default = null)
{
    if (isset($val) && !empty($val)) {
        return $val;
    }

    return $default;
}
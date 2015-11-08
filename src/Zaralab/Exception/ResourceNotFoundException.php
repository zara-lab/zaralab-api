<?php
/**
 * Project: zaralab
 * Filename: ResourceNotFoundException.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 31.10.15
 */

namespace Zaralab\Exception;

use Slim\Exception\NotFoundException;

class ResourceNotFoundException extends NotFoundException
{
    public function __construct($message = "", $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
<?php

use Behat\RestTestingContext\BaseContext;

/**
 * Defines application features from the specific context.
 */
class ApiContext extends BaseContext
{
    const PRINT_HOOKS = false;

    use DatabaseTrait;
}

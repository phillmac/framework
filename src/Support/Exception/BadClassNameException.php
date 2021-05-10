<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Support\Exception;

/**
 * Bad class name exception.  Used when a class name is dynamically invoked, but the class does not exist.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class BadClassNameException extends \LogicException
{
    //
}

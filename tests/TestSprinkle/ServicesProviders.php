<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Tests\TestSprinkle;

use UserFrosting\ServicesProvider\ServicesProviderInterface;

class ServicesProviders implements ServicesProviderInterface
{
    public function register(): array
    {
        return [
            'testMessageGenerator' => \DI\create(MessageGenerator::class),
        ];
    }
}

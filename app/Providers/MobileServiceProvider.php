<?php

declare(strict_types=1);

namespace Modules\Mobile\Providers;

use Modules\Xot\Providers\XotBaseServiceProvider;

class MobileServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Mobile';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;
}
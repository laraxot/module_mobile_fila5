<?php
namespace Modules\Mobile\Tests\Unit;

use Modules\Mobile\Tests\TestCase;
use Modules\Mobile\Models\OrderQueue;

it('has main model class', function () {
    expect(class_exists(OrderQueue::class))->toBeTrue();
});

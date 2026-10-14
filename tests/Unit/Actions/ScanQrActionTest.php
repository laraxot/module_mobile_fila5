<?php

declare(strict_types=1);

use Modules\Mobile\Actions\Mobile\ScanQrAction;

test('scan qr rejects an unknown format without querying the catalog', function (): void {
    expect((new ScanQrAction())->execute('unknown:1'))->toBe([
        'success' => false,
        'message' => 'Invalid QR code format',
    ]);
});


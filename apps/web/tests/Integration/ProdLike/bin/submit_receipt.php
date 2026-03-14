<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Entries\EntrySubmissionService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../../../../vendor/autoload.php';

$app = require __DIR__.'/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$payload = json_decode(base64_decode($argv[1] ?? '', true), true, 512, JSON_THROW_ON_ERROR);
$user = User::query()->findOrFail((int) $payload['user_id']);
$receiptPayload = $payload['receipt'];

$request = Request::create('/participant/receipts', 'POST', $receiptPayload, server: [
    'REMOTE_ADDR' => $payload['ip'] ?? '127.0.0.1',
]);

try {
    $entry = app(EntrySubmissionService::class)->submit($user, $receiptPayload, $request);

    echo json_encode([
        'status' => 'success',
        'entry_id' => $entry->id,
    ], JSON_THROW_ON_ERROR);
} catch (ValidationException $exception) {
    echo json_encode([
        'status' => 'validation_error',
        'errors' => $exception->errors(),
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->__toString());

    exit(1);
}

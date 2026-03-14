<?php

declare(strict_types=1);

use App\Models\DrawSchedule;
use App\Services\Draw\DrawService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../../../../vendor/autoload.php';

$app = require __DIR__.'/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$payload = json_decode(base64_decode($argv[1] ?? '', true), true, 512, JSON_THROW_ON_ERROR);
$schedule = DrawSchedule::query()->findOrFail((int) $payload['schedule_id']);

try {
    $drawRun = app(DrawService::class)->runSchedule($schedule);

    echo json_encode([
        'status' => 'success',
        'draw_run_id' => $drawRun->id,
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->__toString());

    exit(1);
}

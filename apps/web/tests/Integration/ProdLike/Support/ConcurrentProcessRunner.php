<?php

namespace Tests\Integration\ProdLike\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

class ConcurrentProcessRunner
{
    /** @param array<string, mixed> $payload */
    public static function start(string $script, array $payload): Process
    {
        $encodedPayload = base64_encode(json_encode($payload, JSON_THROW_ON_ERROR));

        $process = new Process(['php', base_path($script), $encodedPayload], base_path());
        $process->start();

        return $process;
    }

    /** @return array<string, mixed> */
    public static function wait(Process $process): array
    {
        $process->wait();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput().' '.$process->getOutput()));
        }

        $output = trim($process->getOutput());

        if ($output === '') {
            throw new RuntimeException('Concurrent process finished without output.');
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($output, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}

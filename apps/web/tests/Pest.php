<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class)->in('Unit');
uses(TestCase::class, DatabaseMigrations::class)->in('Integration');

beforeEach(function () {
    $this->withoutVite();
})->in('Feature');

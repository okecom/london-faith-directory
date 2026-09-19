<?php

use Illuminate\Support\Facades\DB;

it('uses the dedicated testing database', function () {
    expect(DB::connection()->getDatabaseName())
        ->toBe('london_faith_directory_testing');
});
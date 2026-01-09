<?php

use Miguelenes\FilamentHorizon\Facades\FilamentHorizon;

it('resolves facade correctly', function () {
    $resolved = FilamentHorizon::getFacadeRoot();

    expect($resolved)->toBeInstanceOf(\Miguelenes\FilamentHorizon\FilamentHorizon::class);
});

it('has correct facade accessor', function () {
    $reflection = new ReflectionClass(FilamentHorizon::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    expect($method->invoke(null))->toBe(\Miguelenes\FilamentHorizon\FilamentHorizon::class);
});

<?php

use Eloquage\FilamentHorizon\Facades\FilamentHorizon;

it('resolves facade correctly', function () {
    $resolved = FilamentHorizon::getFacadeRoot();

    expect($resolved)->toBeInstanceOf(\Eloquage\FilamentHorizon\FilamentHorizon::class);
});

it('has correct facade accessor', function () {
    $reflection = new ReflectionClass(FilamentHorizon::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    expect($method->invoke(null))->toBe(\Eloquage\FilamentHorizon\FilamentHorizon::class);
});

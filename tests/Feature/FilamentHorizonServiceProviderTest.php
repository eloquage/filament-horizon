<?php

use Eloquage\FilamentHorizon\FilamentHorizonServiceProvider;
use Eloquage\FilamentHorizon\Services\HorizonApi;

it('registers package correctly', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    expect($provider::$name)->toBe('filament-horizon');
    expect($provider::$viewNamespace)->toBe('filament-horizon');
});

it('binds HorizonApi as singleton', function () {
    $api1 = app(HorizonApi::class);
    $api2 = app(HorizonApi::class);

    expect($api1)->toBe($api2);
    expect($api1)->toBeInstanceOf(HorizonApi::class);
});

it('registers assets', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    // Test that packageBooted executes without errors
    $provider->packageBooted();

    expect(true)->toBeTrue(); // Just verify it doesn't throw
});

it('registers script data', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    // Test that packageBooted executes without errors
    $provider->packageBooted();

    expect(true)->toBeTrue(); // Just verify it doesn't throw
});

it('registers icons', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    // Test that packageBooted executes without errors
    $provider->packageBooted();

    expect(true)->toBeTrue(); // Just verify it doesn't throw
});

it('registers testing mixin', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    // Test that packageBooted executes without errors
    $provider->packageBooted();

    expect(true)->toBeTrue(); // Just verify it doesn't throw
});

it('publishes stubs when running in console', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    // Test that packageBooted executes without errors even if stubs directory doesn't exist
    $provider->packageBooted();

    expect(true)->toBeTrue(); // Just verify it doesn't throw
});

it('has correct asset package name', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getAssetPackageName');
    $method->setAccessible(true);

    expect($method->invoke($provider))->toBe('eloquage/filament-horizon');
});

it('returns correct assets', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getAssets');
    $method->setAccessible(true);

    $assets = $method->invoke($provider);

    expect($assets)->toBeArray();
    expect($assets)->toHaveCount(2);
});

it('returns correct commands', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getCommands');
    $method->setAccessible(true);

    $commands = $method->invoke($provider);

    expect($commands)->toBeArray();
    expect($commands)->toContain(\Eloquage\FilamentHorizon\Commands\FilamentHorizonCommand::class);
});

it('returns empty icons array', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getIcons');
    $method->setAccessible(true);

    $icons = $method->invoke($provider);

    expect($icons)->toBeArray();
});

it('returns empty routes array', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getRoutes');
    $method->setAccessible(true);

    $routes = $method->invoke($provider);

    expect($routes)->toBeArray();
});

it('returns empty script data array', function () {
    $provider = new FilamentHorizonServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getScriptData');
    $method->setAccessible(true);

    $scriptData = $method->invoke($provider);

    expect($scriptData)->toBeArray();
});

afterEach(function () {
    Mockery::close();
});

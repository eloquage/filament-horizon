<?php

use Eloquage\FilamentHorizon\Services\HorizonApi;
use Eloquage\FilamentHorizon\Widgets\WorkersWidget;

beforeEach(function () {
    $this->api = Mockery::mock(HorizonApi::class);
    app()->instance(HorizonApi::class, $this->api);
});

it('returns workers in view data', function () {
    $masters = [
        (object) ['id' => 'master-1', 'status' => 'running'],
        (object) ['id' => 'master-2', 'status' => 'paused'],
    ];

    $this->api->shouldReceive('getMasters')->andReturn($masters);

    $widget = new WorkersWidget;
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('getViewData');
    $method->setAccessible(true);
    $viewData = $method->invoke($widget);

    expect($viewData)->toBeArray();
    expect($viewData['workers'])->toBe($masters);
});

it('handles empty workers', function () {
    $this->api->shouldReceive('getMasters')->andReturn([]);

    $widget = new WorkersWidget;
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('getViewData');
    $method->setAccessible(true);
    $viewData = $method->invoke($widget);

    expect($viewData['workers'])->toBeEmpty();
});

it('has correct polling interval', function () {
    $widget = new WorkersWidget;
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('getPollingInterval');
    $method->setAccessible(true);

    expect($method->invoke($widget))->toBe('5s');
});

it('has full column span', function () {
    $widget = new WorkersWidget;
    expect($widget->getColumnSpan())->toBe('full');
});

afterEach(function () {
    Mockery::close();
});

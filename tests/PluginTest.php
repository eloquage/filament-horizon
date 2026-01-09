<?php

use Eloquage\FilamentHorizon\Clusters\Horizon;
use Eloquage\FilamentHorizon\FilamentHorizonPlugin;
use Eloquage\FilamentHorizon\Pages\Batches;
use Eloquage\FilamentHorizon\Pages\Dashboard;
use Eloquage\FilamentHorizon\Pages\FailedJobs;
use Eloquage\FilamentHorizon\Pages\Metrics;
use Eloquage\FilamentHorizon\Pages\Monitoring;
use Eloquage\FilamentHorizon\Pages\RecentJobs;

it('can create the plugin instance', function () {
    $plugin = FilamentHorizonPlugin::make();

    expect($plugin)->toBeInstanceOf(FilamentHorizonPlugin::class);
    expect($plugin->getId())->toBe('filament-horizon');
});

it('has a horizon cluster', function () {
    expect(class_exists(Horizon::class))->toBeTrue();
});

it('has all required pages', function () {
    $pages = [
        Dashboard::class,
        RecentJobs::class,
        FailedJobs::class,
        Batches::class,
        Monitoring::class,
        Metrics::class,
    ];

    foreach ($pages as $page) {
        expect(class_exists($page))->toBeTrue();
    }
});

it('pages belong to horizon cluster', function () {
    $pages = [
        Dashboard::class,
        RecentJobs::class,
        FailedJobs::class,
        Batches::class,
        Monitoring::class,
        Metrics::class,
    ];

    foreach ($pages as $page) {
        $reflection = new ReflectionClass($page);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);

        expect($property->getValue())->toBe(Horizon::class);
    }
});

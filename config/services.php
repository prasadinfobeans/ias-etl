<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

return function (ContainerConfigurator $c) {
    $services = $c->services();

    // 1) Auto‑load & register all controllers in src/Controller
    $services->load('IASETL\\Controller\\', __DIR__ . '/../src/Controller')
        ->autowire()
        ->autoconfigure()
        ->public()
        ->tag('controller.service_arguments');

    // 2) ETL DBAL connection service
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->set('ias_etl.connection', Connection::class)
        ->factory([DriverManager::class, 'getConnection'])
        ->args(['%ias_etl.db_params%']);

    // Alias the default Connection type to our ETL connection
    $services->alias(Connection::class, 'ias_etl.connection');
};

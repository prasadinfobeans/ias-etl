<?php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
return function (RoutingConfigurator $routes) {
    $routes->add('ias_etl_test', '/etl/test')
        ->controller([IASETL\Controller\ETLController::class, 'test']);
    $routes->add('ias_etl_test_db', '/etl/test-db')
        ->controller([IASETL\Controller\ETLController::class, 'testDb']);
};

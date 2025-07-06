<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
return function (ContainerConfigurator $c) {
    $s = $c->services();
    $s->defaults()->autowire()->autoconfigure();
    $s->set('ias_etl.connection', Connection::class)
      ->factory([DriverManager::class, 'getConnection'])
      ->args(['%ias_etl.db_params%']);
    $s->alias(Connection::class, 'ias_etl.connection');
};

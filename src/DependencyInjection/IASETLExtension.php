<?php
namespace IASETL\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;
class IASETLExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        if (file_exists(__DIR__.'/../../config/services.php')) {
            $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
            $loader->load('services.php');
        }
        $container->setParameter('ias_etl.db_params', [
            'url' => '%env(ETL_DATABASE_URL)%'
        ]);
    }
}

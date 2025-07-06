<?php
namespace IASETL\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD              => 'onPostInstallOrUpdate',
            ScriptEvents::POST_UPDATE_CMD               => 'onPostInstallOrUpdate',
            PackageEvents::POST_PACKAGE_UNINSTALL       => 'onPostUninstall',
            ScriptEvents::POST_REMOVE_CMD   => 'onPostUninstall',    // <-- add this
        ];
    }

    public function onPostInstallOrUpdate(Event $event): void
{
    $this->io->write("› Importing ETL routes…");

    // Detect vendor-dir correctly
    $vendorDirFromConfig = $this->composer->getConfig()->get('vendor-dir');
    if (DIRECTORY_SEPARATOR === substr($vendorDirFromConfig, 0, 1)) {
        $vendorDir = rtrim($vendorDirFromConfig, '/');
    } else {
        $vendorDir = getcwd() . '/' . trim($vendorDirFromConfig, '/');
    }

    $bundleConfig = $vendorDir . '/ias/ias-etl/config';
    $appRoutes    = getcwd() . '/config/routes';

    if (!is_dir($appRoutes)) {
        mkdir($appRoutes, 0755, true);
    }

    // Copy PHP routes
    $srcPhp = $bundleConfig . '/routes.php';
    $dstPhp = $appRoutes . '/ias_etl_routes.php';
    if (file_exists($srcPhp)) {
        copy($srcPhp, $dstPhp);
        $this->io->write("✔️  Copied PHP routes to config/routes/ias_etl_routes.php");
    } else {
        $this->io->write("❌  Bundle routes.php not found at {$srcPhp}");
        return;
    }

    // Write YAML wrapper
    $wrapper = <<<YAML
ias_etl:
    resource: 'ias_etl_routes.php'
    type: php
YAML;
    $dstYaml = $appRoutes . '/ias_etl.yaml';
    if (!file_exists($dstYaml)) {
        file_put_contents($dstYaml, $wrapper);
        $this->io->write("✔️  Generated wrapper at config/routes/ias_etl.yaml");
    } else {
        $this->io->write("ℹ️  config/routes/ias_etl.yaml already exists, skipping");
    }
}


public function onPostUninstall(Event $event): void
{
    $this->io->write("› Removing ETL routes…");

    $appRoutes = getcwd() . '/config/routes';
    foreach (['ias_etl_routes.php', 'ias_etl.yaml'] as $file) {
        $path = "{$appRoutes}/{$file}";
        if (file_exists($path)) {
            unlink($path);
            $this->io->write("✔️  Removed config/routes/{$file}");
        }
    }
}

    public function deactivate(Composer $composer, IOInterface $io): void {}
    public function uninstall(Composer $composer, IOInterface $io): void {}
}


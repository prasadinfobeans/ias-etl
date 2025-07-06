<?php
namespace IASETL\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\Installer\PackageEvents;
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
            ScriptEvents::POST_INSTALL_CMD  => 'onPostInstallOrUpdate',
            ScriptEvents::POST_UPDATE_CMD   => 'onPostInstallOrUpdate',
            PackageEvents::POST_PACKAGE_UNINSTALL => 'onPostUninstall',
        ];
    }

    public function onPostInstallOrUpdate(Event $event): void
{
    $this->io->write("› Importing ETL routes…");

    $vendorDir = $this->composer->getConfig()->get('vendor-dir');            // e.g. "vendor"
    $bundleConfig = getcwd() . "/{$vendorDir}/ias/ias-etl/config";         // bundle’s config dir
    $appRoutesDir = getcwd() . '/config/routes';

    if (!is_dir($appRoutesDir)) {
        mkdir($appRoutesDir, 0755, true);
    }

    // 1) Copy the bundle’s PHP routes.php into the app
    $srcPhp = "{$bundleConfig}/routes.php";
    $dstPhp = "{$appRoutesDir}/ias_etl_routes.php";
    if (file_exists($srcPhp) && !file_exists($dstPhp)) {
        copy($srcPhp, $dstPhp);
        $this->io->write("✔  Copied routes.php to config/routes/ias_etl_routes.php");
    }

    // 2) Generate a YAML wrapper that points at that local copy
    $wrapper = <<<YAML
ias_etl:
    resource: 'ias_etl_routes.php'
    type: php
YAML;

    $dstYaml = "{$appRoutesDir}/ias_etl.yaml";
    if (!file_exists($dstYaml)) {
        file_put_contents($dstYaml, $wrapper);
        $this->io->write("✔  Generated wrapper at config/routes/ias_etl.yaml");
    } else {
        $this->io->write("ℹ  config/routes/ias_etl.yaml already exists, skipping.");
    }
}




public function onPostUninstall(): void
{
    $this->io->write("› Removing ETL routes…");

    $appRoutesDir = getcwd() . '/config/routes';
    foreach (['ias_etl_routes.php','ias_etl.yaml'] as $file) {
        $path = "{$appRoutesDir}/{$file}";
        if (file_exists($path)) {
            unlink($path);
            $this->io->write("✔  Removed config/routes/{$file}");
        }
    }
}

    // deactivate() and uninstall() no‑ops
    public function deactivate(Composer $composer, IOInterface $io): void {}
    public function uninstall(Composer $composer, IOInterface $io): void {}
}


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

        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $source    = $vendorDir . '/ias/ias-etl/config/routes/ias_etl.yaml';
        $targetDir = getcwd() . '/config/routes';
        $target    = $targetDir . '/ias_etl.yaml';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!file_exists($target)) {
            copy($source, $target);
            $this->io->write("✔  ETL routes imported to config/routes/ias_etl.yaml");
        } else {
            $this->io->write("ℹ  config/routes/ias_etl.yaml already exists, skipping.");
        }
    }

    public function onPostUninstall(): void
    {
        $this->io->write("› Removing ETL routes…");

        $file = getcwd() . '/config/routes/ias_etl.yaml';
        if (file_exists($file)) {
            unlink($file);
            $this->io->write("✔  Removed config/routes/ias_etl.yaml");
        } else {
            $this->io->write("ℹ  No ETL route file to remove.");
        }
    }

    // deactivate() and uninstall() no‑ops
    public function deactivate(Composer $composer, IOInterface $io): void {}
    public function uninstall(Composer $composer, IOInterface $io): void {}
}


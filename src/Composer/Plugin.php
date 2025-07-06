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
	    $bundleDir = getcwd().'/'.$vendorDir.'/ias/ias-etl/config';

	    $targetDir = getcwd() . '/config/routes';
	    if (!is_dir($targetDir)) {
		mkdir($targetDir, 0755, true);
	    }

	    // 1) Copy PHP routes file
	    $srcPhp = $bundleDir . '/routes.php';
	    $dstPhp = $targetDir . '/ias_etl_routes.php';
	    if (!file_exists($dstPhp)) {
		copy($srcPhp, $dstPhp);
		$this->io->write("✔  Copied ETL PHP routes to config/routes/ias_etl_routes.php");
	    }

	    // 2) Copy wrapper YAML
	    $srcYaml = $bundleDir . '/routes/ias_etl.yaml';
	    $dstYaml = $targetDir . '/ias_etl.yaml';
	    if (!file_exists($dstYaml)) {
		copy($srcYaml, $dstYaml);
		$this->io->write("✔  Copied ETL wrapper to config/routes/ias_etl.yaml");
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


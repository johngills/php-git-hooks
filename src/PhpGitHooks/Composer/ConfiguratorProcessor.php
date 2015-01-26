<?php

namespace PhpGitHooks\Composer;

use PhpGitHooks\Infrastructure\PhpMD\PhpMDInitConfigFile;
use PhpGitHooks\Infrastructure\Config\CheckConfigFile;
use PhpGitHooks\Infrastructure\Config\ConfigFileWriter;
use PhpGitHooks\Infrastructure\PhpUnit\PhpUnitInitConfigFile;

/**
 * Class ConfiguratorProcessor
 * @package PhpGitHooks\Composer
 */
class ConfiguratorProcessor extends Processor
{
    private $configData = array();
    /** @var  CheckConfigFile */
    private $checkConfigFile;
    /** @var PreCommitProcessor */
    private $preCommitProcessor;
    /** @var ConfigFileWriter */
    private $configFileWriter;
    /** @var PhpUnitInitConfigFile */
    private $phpUnitInitConfigFile;
    /** @var PhpMDInitConfigFile */
    private $pmdInitConfigFile;

    /**
     * @param CheckConfigFile       $checkConfigFile
     * @param PreCommitProcessor    $preCommitProcessor
     * @param ConfigFileWriter      $configFileWriter
     * @param PhpUnitInitConfigFile $phpUnitInitConfigFile
     * @param PhpMDInitConfigFile   $phpMDInitConfigFile
     */
    public function __construct(
        CheckConfigFile $checkConfigFile,
        PreCommitProcessor $preCommitProcessor,
        ConfigFileWriter $configFileWriter,
        PhpUnitInitConfigFile $phpUnitInitConfigFile,
        PhpMDInitConfigFile $phpMDInitConfigFile
    ) {
        $this->checkConfigFile = $checkConfigFile;
        $this->preCommitProcessor = $preCommitProcessor;
        $this->configFileWriter = $configFileWriter;
        $this->phpUnitInitConfigFile = $phpUnitInitConfigFile;
        $this->pmdInitConfigFile = $phpMDInitConfigFile;
    }

    public function process()
    {
        $this->initConfigFile();
        $this->phpunitConfigFile();
        $this->phpMDConfigFile();
    }

    /**
     * Create php-git-hooks.yml file
     */
    private function initConfigFile()
    {
        if (false === $this->checkConfigFile->exists()) {
            $generate = $this->setQuestion('Do you want generate a php-git.hooks.yml file?', 'Y/n', 'Y');

            if ('N' === strtoupper($generate)) {
                $this->io->write(
                    '<error>Remember that you need a configuration file to use php-git-hooks library.</error>'
                );

                return;
            }

            $this->preCommitProcessor->setIO($this->io);
            $this->configData = $this->preCommitProcessor->execute();

            $this->configFileWriter->write($this->checkConfigFile->getFile(), $this->configData);
        }
    }

    /**
     * Create phpunit.xml file
     */
    private function phpunitConfigFile()
    {
        $this->phpUnitInitConfigFile->setIO($this->io);
        $this->phpUnitInitConfigFile->process();
    }

    /**
     * Create phpRules.xml file
     */
    private function phpMDConfigFile()
    {
        $this->pmdInitConfigFile->setIO($this->io);
        $this->pmdInitConfigFile->process();
    }
}

<?php

namespace PhpGitHooks\Infrastructure\PhpMD;

use PhpGitHooks\Composer\Processor;
use PhpGitHooks\Infrastructure\Common\ConfigFileToolValidator;

/**
 * Class PhpMDInitConfigFile
 * @package PhpGitHooks\Infraestructure\PhpMD
 */
class PhpMDInitConfigFile extends Processor
{
    /** @var  ConfigFileToolValidator */
    private $validator;

    /**
     * @param ConfigFileToolValidator $configFileToolValidator
     */
    public function __construct(ConfigFileToolValidator $configFileToolValidator)
    {
        $this->validator = $configFileToolValidator;
        $this->validator->setFiles(['PmdRules.xml-1']);
    }

    public function process()
    {
        if (!$this->validator->existsConfigFile()) {
            $answer = $this->setQuestion('Do you want create a PmdRules.xml file?.', 'Y/n', 'Y');

            if (strtoupper($answer) === 'Y') {
                copy(__DIR__.'/../../../../PmdRules.xml', 'PmdRules.xml-a');
            }
        }
    }
}

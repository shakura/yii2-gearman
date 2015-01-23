<?php
namespace shakura\yii2\gearman\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use shakura\yii2\gearman\Config;
use shakura\yii2\gearman\Process;
use shakura\yii2\gearman\Application as GearmanApplication;

class StopCommand extends Command
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var bool
     */
    private $result = false;

    /**
     * @var GearmanApplication
     */
    private $gearmanApplication;

    protected function configure()
    {
        $this->setName('stop')
            ->setDescription('Stop the gearman workers daemon');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Stoping gearman-handler: ');

        $process = $this->getProcess();

        if ($process->isRunning()) {
            $this->setResult(true);
            $output->write('[ <fg=green>OK</fg=green> ]', true);
        } else {
            $output->write('[ <fg=red>FAILED</fg=red> ]', true);
        }
        $process->stop();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->setConfig(new Config);
        }
        return $this->config;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        if (null === $this->process) {
            $this->setProcess((new Process($this->getConfig())));
        }
        return $this->process;
    }

    /**
     * @param Process $process
     * @return $this
     */
    public function setProcess(Process $process)
    {
        if (null === $this->getConfig() && $process->getConfig() instanceof Config) {
            $this->setConfig($process->getConfig());
        }
        $this->process = $process;
        return $this;
    }

    /**
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param bool $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return GearmanApplication
     */
    public function getGearmanApplication()
    {
        return $this->gearmanApplication;
    }

    /**
     * @param GearmanApplication $gearmanApplication
     * @return $this
     */
    public function setGearmanApplication(GearmanApplication $gearmanApplication)
    {
        $this->gearmanApplication = $gearmanApplication;
        return $this;
    }
}


<?php
namespace shakura\yii2\gearman\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use shakura\yii2\gearman\Process;
use shakura\yii2\gearman\Config;
use shakura\yii2\gearman\Application as GearmanApplication;

class RestartCommand extends Command
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
     * @var callable
     */
    private $runtime;

    /**
     * @var GearmanApplication
     */
    private $gearmanApplication;

    protected function configure()
    {
        $this->setName('restart')
            ->setDescription('Restart the gearman workers daemon')
            ->addOption('bootstrap', null, InputOption::VALUE_OPTIONAL)
            ->addOption('class', null, InputOption::VALUE_OPTIONAL)
            ->addOption('server', null, InputOption::VALUE_OPTIONAL)
            ->addOption('servers', null, InputOption::VALUE_OPTIONAL)
            ->addOption('user', null, InputOption::VALUE_OPTIONAL)
            ->addOption('auto_update', null, InputOption::VALUE_OPTIONAL)
            ->addOption('autoUpdate', null, InputOption::VALUE_OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stop = new StopCommand();
        $stop->setProcess($this->getProcess());
        $stop->run($input, $output);

        if ($stop->getResult()) {
            $process = $this->getProcess();
            $int = 0;
            while ($int < 1000) {
                if (file_exists($process->getPidFile())) {
                    usleep(1000);
                    $int++;
                } elseif (file_exists($process->getLockFile())) {
                    $process->release();
                    usleep(1000);
                    $int++;
                } else {
                    $int = 1000;
                }
            }
        }

        $start = new StartCommand();
        $start->setGearmanApplication($this->getGearmanApplication());
        $start->setProcess($this->getProcess());
        $start->setRuntime($this->getRuntime());
        $start->run($input, $output);
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
     * @return callable
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param null|callable $runtime
     * @return $this
     */
    public function setRuntime(callable $runtime = null)
    {
        $this->runtime = $runtime;
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

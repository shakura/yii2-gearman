<?php
namespace shakura\yii2\gearman;

use Serializable;
use GearmanException;
use GearmanWorker;
use Psr\Log\LoggerInterface;
use shakura\yii2\gearman\exception\ServerConnectionException;

class Worker
{
    /**
     * @var GearmanWorker
     */
    private $worker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $functions = [];

    /**
     * @param Config $config
     * @param null|LoggerInterface $logger
     * @throws ServerConnectionException
     */
    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->setConfig($config);
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    public function resetWorker()
    {
        if ($this->worker instanceof GearmanWorker) {
            $this->worker->unregisterAll();
        }
        $this->worker = null;
        $this->createWorker();
    }

    /**
     * @throws Exception\ServerConnectionException
     */
    private function createWorker()
    {
        $this->worker = new GearmanWorker();
        $servers = $this->getConfig()->getServers();
        $exceptions = [];
        foreach ($servers as $server) {
            try {
                $this->worker->addServer($server->getHost(), $server->getPort());
            } catch (GearmanException $e) {
                $message = 'Unable to connect to Gearman Server ' . $server->getHost() . ':' . $server->getPort();
                if (null !== $this->logger) {
                    $this->logger->info($message);
                }
                $exceptions[] = $message;
            }
        }

        if (count($exceptions)) {
            foreach ($exceptions as $exception) {
                throw new ServerConnectionException($exception);
            }
        }
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
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
     * @return GearmanWorker
     */
    public function getWorker()
    {
        if (null === $this->worker) {
            $this->createWorker();
        }
        return $this->worker;
    }

    /**
     * @param GearmanWorker $worker
     * @return $this
     */
    public function setWorker(GearmanWorker $worker)
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param null|LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        return $this;
    }
}

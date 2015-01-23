<?php
namespace shakura\yii2\gearman;

use GearmanException;
use GearmanClient;
use Psr\Log\LoggerInterface;
use shakura\yii2\gearman\exception\ServerConnectionException;

class Client
{
    /**
     * @var GearmanClient
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $hasServers = false;

    /**
     * @param Config $config
     * @param null|LoggerInterface $logger
     */
    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->setClient(new GearmanClient());
        $this->setConfig($config);
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @return $this
     * @throws Exception\ServerConnectionException
     */
    private function addServers()
    {
        $this->hasServers = true;
        $client = $this->getClient();
        $servers = $this->getConfig()->getServers();
        $exceptions = [];
        foreach ($servers as $server) {
            try {
                $client->addServer($server->getHost(), $server->getPort());
            } catch (GearmanException $e) {
                $message = 'Unable to connect to Gearman Server ' . $server->getHost() . ':' . $server->getPort();
                if (null !== $this->logger) {
                    $this->logger->error($message);
                }
                $exceptions[] = $message;
            }
        }

        if (count($exceptions)) {
            foreach ($exceptions as $exception) {
                throw new ServerConnectionException($exception);
            }
        }

        return $this;
    }

    /**
     * @return GearmanClient
     */
    public function getClient()
    {
        if (!$this->hasServers) {
            $this->addServers();
        }
        return $this->client;
    }

    /**
     * @param GearmanClient $client
     * @return $this
     */
    public function setClient(GearmanClient $client)
    {
        $this->client = $client;
        return $this;
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
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}

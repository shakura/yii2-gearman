<?php
namespace shakura\yii2\gearman;

use Psr\Log\LoggerInterface;

class Dispatcher
{
    const NORMAL = 0;
    const LOW = 1;
    const HIGH = 2;

    /**
     * @var Client
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
     * @param Config $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->setConfig($config);
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     * @return string $jobHandle
     */
    public function background($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        $client = $this->getClient()->getClient();

        if (null !== $this->logger) {
            $this->logger->debug("Sent background job \"{$name}\" to GearmanClient");
        }

        $jobHandle = null;
        switch ($priority) {
            case self::LOW:
                $jobHandle = $client->doLowBackground($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $jobHandle = $client->doHighBackground($name, self::serialize($data), $unique);
                break;
            default:
                $jobHandle = $client->doBackground($name, self::serialize($data), $unique);
                break;
        }

        if ($client->returnCode() !== GEARMAN_SUCCESS) {
            if (null !== $this->logger) {
                $this->logger->error("Bad return code");
            }
        }

        if (null !== $this->logger) {
            $this->logger->info("Sent job \"{$jobHandle}\" to GearmanWorker");
        }
        
        return $jobHandle;
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $priority
     * @param string $unique
     * @return mixed
     */
    public function execute($name, $data = null, $priority = self::NORMAL, $unique = null)
    {
        $client = $this->getClient()->getClient();

        if (null !== $this->logger) {
            $this->logger->debug("Sent job \"{$name}\" to GearmanClient");
        }

        $result = null;
        switch ($priority) {
            case self::LOW:
                $result = $client->doLow($name, self::serialize($data), $unique);
                break;
            case self::HIGH:
                $result = $client->doHigh($name, self::serialize($data), $unique);
                break;
            default:
                $result = $client->doNormal($name, self::serialize($data), $unique);
                break;
        }

        if ($client->returnCode() !== GEARMAN_SUCCESS) {
            if (null !== $this->logger) {
                $this->logger->error("Bad return code");
            }
        }

        if (null !== $this->logger) {
            $this->logger->debug("Job \"{$name}\" returned {$result}");
        }

        return unserialize($result);
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serialize($data = [])
    {
        return serialize($data);
    }

    /**
     * @param Client|null $client
     * @return $this
     */
    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client|null
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->setClient(new Client($this->getConfig(), $this->getLogger()));
        }
        return $this->client;
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
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
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

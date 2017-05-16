<?php

namespace PgAsync;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\EventLoop\LoopInterface;
use Rx\Subject\Subject;

class Client implements EventEmitterInterface
{
    use EventEmitterTrait;

    /** @var  string */
    protected $connectString;

    /** @var LoopInterface */
    protected $loop;

    protected $params = [];

    private $parameters = [];

    /** @var Connection[] */
    private $connections = [];

    /** @var boolean */
    private $autoDisconnect;

    public function __construct(array $parameters, LoopInterface $loop = null)
    {
        $this->parameters = $parameters;
        $this->loop       = $loop ?: \EventLoop\getLoop();

        if (isset($parameters['auto_disconnect'])) {
            $this->autoDisconnect = $parameters['auto_disconnect'];
        }
    }

    public function query($s)
    {
        $conn = $this->getIdleConnection();

        return $conn->query($s);
    }

    public function executeStatement(string $queryString, array $parameters = [], Subject $backpressureSubject = null, int $backpressureRows = 100)
    {
        $conn = $this->getIdleConnection();

        return $conn->executeStatement($queryString, $parameters, $backpressureSubject, $backpressureRows);
    }

    public function getIdleConnection(): Connection
    {
        // we want to get the first available one
        // this will keep the connections at the front the busiest
        // and then we can add an idle timer to the connections
        foreach ($this->connections as $connection) {
            // need to figure out different states (in trans etc.)
            if ($connection->getState() === Connection::STATE_READY) {
                return $connection;
            }
        }

        // no idle connections were found - spin up new one
        $connection = new Connection($this->parameters, $this->loop);
        if (!$this->autoDisconnect) {
            $this->connections[] = $connection;

            $connection->on('close', function () use ($connection) {
                $this->connections = array_filter($this->connections, function ($c) use ($connection) {
                    return $connection !== $c;
                });
            });
        }

        return $connection;
    }

    public function getConnectionCount(): int
    {
        return count($this->connections);
    }

    /**
     * This is here temporarily so that the tests can disconnect
     * Will be setup better/more gracefully at some point hopefully
     *
     * @deprecated
     */
    public function closeNow()
    {
        foreach ($this->connections as $connection) {
            $connection->disconnect();
        }
    }
}

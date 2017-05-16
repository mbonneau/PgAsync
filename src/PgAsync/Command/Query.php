<?php

namespace PgAsync\Command;

use PgAsync\Message\Message;
use Rx\Subject\Subject;

class Query implements CommandInterface
{
    use CommandTrait;

    protected $queryString = "";

    private $backpressureSubject;
    private $backpressureRows;

    public function __construct(string $queryString, Subject $backpressureSubject = null, int $backpressureRows = 100)
    {
        $this->queryString = $queryString;

        $this->subject = new Subject();

        $this->backpressureSubject = $backpressureSubject;
        $this->backpressureRows    = $backpressureRows;
    }

    public function encodedMessage(): string
    {
        return 'Q' . Message::prependLengthInt32($this->queryString . "\0");
    }

    public function shouldWaitForComplete(): bool
    {
        return true;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getBackpressureSubject(): ?Subject
    {
        return $this->backpressureSubject;
    }

    public function getBackpressureRows(): int
    {
        return $this->backpressureRows;
    }
}

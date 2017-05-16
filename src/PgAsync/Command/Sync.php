<?php

namespace PgAsync\Command;

use Rx\Subject\Subject;

class Sync implements CommandInterface
{
    use CommandTrait;

    private $description;

    private $backpressureSubject;
    private $backpressureRows;

    public function __construct(string $description = "", Subject $backpressureSubject = null, int $backpressureRows = 100)
    {
        $this->backpressureSubject = $backpressureSubject;
        $this->backpressureRows = $backpressureRows;
        $this->description = $description;
        $this->getSubject();
    }

    public function encodedMessage(): string
    {
        return "S\0\0\0\x04";
    }

    public function shouldWaitForComplete(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return $this->description;
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

<?php

namespace PgAsync\Tests\Integration;

use PgAsync\Client;
use Rx\Observer\CallbackObserver;

class SimpleQueryTest extends TestCase
{
    public function testSimpleQuery()
    {
        $client = new Client(["user" => $this::getDbUser(), "database" => $this::getDbName()]);

        $count = $client->query("SELECT count(*) AS the_count FROM thing");

        $theCount = -1;

        $count->subscribe(new CallbackObserver(
            function ($x) use (&$theCount) {
                $this->assertTrue($theCount == -1);
                $theCount = $x["the_count"];
            },
            function ($e) use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
                $this->fail("onError");
            },
            function () use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
            }
        ));

        $this->runLoopWithTimeout(2);

        $this->assertEquals(3, $theCount);
    }

    public function testSimpleQueryNoResult()
    {
        $client = new Client(["user" => $this->getDbUser(), "database" => $this->getDbName()], $this->getLoop());

        $count = $client->query("SELECT count(*) AS the_count FROM thing WHERE thing_type = 'non-thing'");

        $theCount = -1;

        $count->subscribe(new CallbackObserver(
            function ($x) use (&$theCount) {
                $this->assertTrue($theCount == -1); // make sure we only run once
                $theCount = $x["the_count"];
            },
            function ($e) use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
                $this->fail("onError");
            },
            function () use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
            }
        ));

        $this->runLoopWithTimeout(2);

        $this->assertEquals(0, $theCount);
    }

    public function testSimpleQueryError()
    {
        $client = new Client(["user" => $this->getDbUser(), "database" => $this::getDbName()], $this->getLoop());

        $count = $client->query("SELECT count(*) abcdef AS the_count FROM thing WHERE thing_type = 'non-thing'");

        $theCount = -1;

        $count->subscribe(new CallbackObserver(
            function ($x) use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
                $this->fail("Should not get result");
            },
            function ($e) use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
            },
            function () use ($client) {
                $client->closeNow();
                $this->cancelCurrentTimeoutTimer();
                $this->fail("Should not complete");
            }
        ));

        $this->runLoopWithTimeout(2);

        $this->assertEquals(-1, $theCount);
    }
    
    public function testIssue9() {
        $cols = [
            'numero_id',
            'empresa_id',
            'tg_username',
            'tg_phone',
            'tg_photo',
            'tg_lastseen',
            'tg_printname',
            'tg_firstname',
            'tg_lastname',
            'tg_peer',
            'tg_id',
            'profile',
            'st_ativo',
            'st_instalado'
        ];

        $cols = implode(', ', $cols);

        $client = new Client(["user" => $this->getDbUser(), "database" => $this::getDbName()], $this->getLoop());

        $rowCount = 0;

        $client->query("SELECT {$cols} FROM numero LIMIT 2")->subscribe(
            new \Rx\Observer\CallbackObserver(
                function ($row) use (&$rowCount) {
                    $rowCount++;
                },
                function ($e) use ($client) {
                    $this->fail();
                    $client->closeNow();
                    $this->cancelCurrentTimeoutTimer();
                },
                function () use ($client) {
                    echo "Complete.\n";
                    $client->closeNow();
                    $this->cancelCurrentTimeoutTimer();
                }
            )
        );
        
        $this->runLoopWithTimeout(2);
        
        $this->assertEquals(2, $rowCount);
    }
}

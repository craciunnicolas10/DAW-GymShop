<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Interfaces\LoggerInterface;
use App\Services\FileLogger;
use PHPUnit\Framework\TestCase;

final class FileLoggerTest extends TestCase
{
    private string $directory;
    private string $logFile;
    private FileLogger $logger;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/gymshop-logger-' . bin2hex(random_bytes(6));
        $this->logFile = $this->directory . '/logs/activity.log';
        $this->logger = new FileLogger($this->logFile);
    }

    protected function tearDown(): void
    {
        if (is_file($this->logFile)) {
            unlink($this->logFile);
        }
        foreach ([$this->directory . '/logs', $this->directory] as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }

    public function testImplementsLoggerInterface(): void
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->logger);
    }

    public function testLogCreatesTheFileAndWritesOneLinePerAction(): void
    {
        $this->logger->log('info', 'login', ['user' => 'client@gymshop.test (#2)', 'ip' => '127.0.0.1']);
        $this->logger->log('info', 'cart_add', ['user' => 'client@gymshop.test (#2)', 'ip' => '127.0.0.1', 'productId' => 5]);

        $this->assertFileExists($this->logFile);
        $this->assertCount(2, file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }

    public function testRecentEntriesAreParsedNewestFirst(): void
    {
        $this->logger->log('INFO', 'LOGIN', ['user' => 'admin@gymshop.test (#1)', 'ip' => '10.0.0.1']);
        $this->logger->log('INFO', 'ORDER_PLACED', ['user' => 'admin@gymshop.test (#1)', 'ip' => '10.0.0.1', 'orderId' => 3]);

        $entries = $this->logger->getRecentEntries();

        $this->assertCount(2, $entries);
        $this->assertSame('ORDER_PLACED', $entries[0]['action']);
        $this->assertSame('admin@gymshop.test (#1)', $entries[0]['user']);
        $this->assertSame('10.0.0.1', $entries[0]['ip']);
        $this->assertSame('{"orderId":3}', $entries[0]['details']);
        $this->assertSame('LOGIN', $entries[1]['action']);
    }

    public function testNewLinesAndSeparatorsAreRemovedToPreventLogInjection(): void
    {
        $this->logger->log('INFO', "LOGIN\nFAKE", ['user' => "evil\nuser | admin", 'ip' => '1.1.1.1']);

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entry = $this->logger->getRecentEntries()[0];

        $this->assertCount(1, $lines);
        $this->assertSame('evil user / admin', $entry['user']);
        $this->assertSame('LOGIN FAKE', $entry['action']);
    }

    public function testEntriesCanBeFilteredByLevelAndText(): void
    {
        $this->logger->log(LoggerInterface::LEVEL_INFO, 'LOGIN', ['user' => 'a@gymshop.test']);
        $this->logger->log(LoggerInterface::LEVEL_WARNING, 'LOGIN_FAILED', ['email' => 'b@gymshop.test']);
        $this->logger->log(LoggerInterface::LEVEL_INFO, 'CART_ADD', ['user' => 'a@gymshop.test']);

        $this->assertCount(1, $this->logger->getRecentEntries(10, 'warning'));
        $this->assertCount(1, $this->logger->getRecentEntries(10, '', 'cart_add'));
        $this->assertCount(1, $this->logger->getRecentEntries(1));
    }

    public function testMissingFileReturnsNoEntries(): void
    {
        $this->assertSame([], $this->logger->getRecentEntries());
    }
}

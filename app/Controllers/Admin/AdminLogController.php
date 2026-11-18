<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Interfaces\LoggerInterface;

/**
 * Lets the administrator read and download the activity log.
 */
final class AdminLogController extends AdminController
{
    private const LEVELS = [LoggerInterface::LEVEL_INFO, LoggerInterface::LEVEL_WARNING, LoggerInterface::LEVEL_ERROR];

    public function index(): void
    {
        $level = strtoupper($this->request->query('level'));
        $search = mb_substr($this->request->query('search'), 0, 100);

        if (!in_array($level, self::LEVELS, true)) {
            $level = '';
        }

        $this->renderAdmin('admin/logs/index', 'logs', [
            'pageTitle' => 'Jurnal de activitate',
            'entries' => $this->logger->getRecentEntries(300, $level, $search),
            'levels' => self::LEVELS,
            'level' => $level,
            'search' => $search,
        ]);
    }

    public function download(): void
    {
        $file = (string) Config::get('logging.file');

        $this->logActivity('ADMIN_LOG_DOWNLOAD');

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="activity-' . date('Y-m-d') . '.log"');
        header('X-Content-Type-Options: nosniff');

        if (is_file($file)) {
            readfile($file);
        }
    }
}

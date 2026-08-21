<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\WithPagination;

class SecurityLogs extends Component
{
    use WithPagination;

    public function getLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return [];
        }

        $logs = [];
        $file = new \SplFileObject($logPath, 'r');
        while (!$file->eof()) {
            $line = $file->fgets();
            if (strpos($line, 'SECURITY ALERT') !== false) {
                // Parse date, environment, severity, and message
                preg_match('/^\[(.*?)\] (.*?)\.WARNING: SECURITY ALERT: (.*)$/', $line, $matches);
                if (count($matches) >= 4) {
                    $logs[] = [
                        'timestamp' => $matches[1],
                        'message' => $matches[3]
                    ];
                } else {
                    $logs[] = [
                        'timestamp' => 'N/A',
                        'message' => trim($line)
                    ];
                }
            }
        }

        return array_reverse($logs);
    }

    public function clearLogs()
    {
        // Only allow superadmin to clear logs
        if (session('active_role_name') !== 'superadmin') {
            $this->dispatch('toast', message: 'Hanya Superadmin yang dapat mengosongkan log keamanan.');
            return;
        }

        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        $this->dispatch('toast', message: 'Log keamanan berhasil dikosongkan.');
        $this->resetPage();
    }

    public function render()
    {
        $allLogs = $this->getLogs();
        $currentPage = $this->getPage();
        $perPage = 15;

        $offset = ($currentPage - 1) * $perPage;
        $paginatedLogs = array_slice($allLogs, $offset, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedLogs,
            count($allLogs),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.management.security-logs', [
            'logs' => $paginator
        ]);
    }
}

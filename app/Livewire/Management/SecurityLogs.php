<?php

namespace App\Livewire\Management;

use Livewire\Component;

class SecurityLogs extends Component
{
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

        // Return latest first, limit to last 200 logs
        return array_slice(array_reverse($logs), 0, 200);
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
    }

    public function render()
    {
        return view('livewire.management.security-logs', [
            'logs' => $this->getLogs()
        ]);
    }
}

<?php

namespace App\Livewire\Guide;

use App\Models\User;
use App\Models\CashierAttendance;
use App\Models\CashierTaskSubmission;
use App\Models\Transaction;
use Livewire\Component;

class Leaderboard extends Component
{
    public $selectedUserId = null;
    public $showUserDetailModal = false;

    public function viewUserDetail($userId)
    {
        $this->selectedUserId = $userId;
        $this->showUserDetailModal = true;
    }

    public function closeUserDetailModal()
    {
        $this->showUserDetailModal = false;
        $this->selectedUserId = null;
    }

    public function render()
    {
        $leaderboard = User::select('*')
            ->selectRaw('(points + pending_points) as total_score')
            ->orderByDesc('total_score')
            ->get();

        $currentUser = auth()->user();
        $currentUserRank = null;
        $totalUsers = $leaderboard->count();

        foreach ($leaderboard as $index => $u) {
            if ($u->id === $currentUser->id) {
                $currentUserRank = $index + 1;
                break;
            }
        }

        // Dynamic Motivation Message & Badge based on Rank
        $motivation = [
            'title' => 'Kerja Bagus!',
            'message' => 'Terus pertahankan semangat dan kualitas kerja Anda!',
            'badge' => 'Pejuang Kasir',
            'type' => 'info'
        ];

        if ($currentUserRank === 1) {
            $motivation = [
                'title' => 'Luar Biasa, ' . $currentUser->name . '!',
                'message' => 'Anda memimpin papan skor di Posisi Pertama! Pertahankan tahta juara Anda minggu ini!',
                'badge' => 'Juara 1 Utama',
                'type' => 'gold'
            ];
        } elseif ($currentUserRank === 2) {
            $motivation = [
                'title' => 'Hebat Sekali, ' . $currentUser->name . '!',
                'message' => 'Anda berada di Peringkat 2! Sedikit lagi dorongan poin untuk merebut posisi puncak!',
                'badge' => 'Runner Up (Peringkat 2)',
                'type' => 'silver'
            ];
        } elseif ($currentUserRank === 3) {
            $motivation = [
                'title' => 'Mantap, ' . $currentUser->name . '!',
                'message' => 'Selamat! Anda mengamankan Peringkat 3 dan berada di jajaran Top 3 Kasir Terbaik!',
                'badge' => 'Top 3 Podiums',
                'type' => 'bronze'
            ];
        } elseif ($currentUserRank && $currentUserRank <= 5) {
            $motivation = [
                'title' => 'Kerja Bagus, ' . $currentUser->name . '!',
                'message' => 'Anda berada di Peringkat ' . $currentUserRank . '! Tembus 3 besar dengan menyelesaikan lebih banyak transaksi & tugas!',
                'badge' => 'Top 5 Elite',
                'type' => 'purple'
            ];
        } else {
            $motivation = [
                'title' => 'Ayo Semangat, ' . $currentUser->name . '!',
                'message' => 'Anda saat ini berada di Peringkat ke-' . ($currentUserRank ?? '-') . ' dari ' . $totalUsers . ' kasir. Jangan menyerah, tingkatkan terus transaksi & tugas untuk masuk ke 3 besar!',
                'badge' => 'Tetap Semangat',
                'type' => 'danger'
            ];
        }

        // Details calculation for modal audit
        $detailUser = null;
        $userStats = [
            'total_transactions' => 0,
            'pos_points' => 0,
            'completed_tasks' => 0,
            'task_points' => 0,
            'attendance_count' => 0,
            'attendance_points' => 0,
        ];
        $recentLogs = collect();

        if ($this->selectedUserId) {
            $detailUser = User::find($this->selectedUserId);
            if ($detailUser) {
                // Count transactions handled by user
                $userStats['total_transactions'] = Transaction::where('user_id', $detailUser->id)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->distinct('reference')
                    ->count('reference');
                $userStats['pos_points'] = $userStats['total_transactions'] * 5;

                // Count completed tasks and calculate points based on task priority
                $approvedSubmissions = CashierTaskSubmission::where('submitted_by', $detailUser->id)
                    ->where('approval_status', 'approved')
                    ->with('assignment.taskDefinition')
                    ->get();

                $userStats['completed_tasks'] = $approvedSubmissions->count();
                $userStats['task_points'] = $approvedSubmissions->sum(function ($sub) {
                    $priority = $sub->assignment->taskDefinition->priority ?? 'medium';
                    return match ($priority) {
                        'low' => 5,
                        'high' => 20,
                        'critical' => 30,
                        default => 10,
                    };
                });

                // Count attendances and calculate attendance & session points
                $attendances = CashierAttendance::where('user_id', $detailUser->id)->get();
                $userStats['attendance_count'] = $attendances->count();
                $userStats['attendance_points'] = $attendances->sum(function ($att) {
                    $pts = 0;
                    if ($att->clock_in) {
                        $pts += 15;
                    }
                    if ($att->clock_out) {
                        $pts += 15;
                    }
                    if ($att->clock_out_status === 'overtime' || $att->clock_out_status === 'on_time') {
                        $pts += 10;
                    }
                    return $pts;
                });

                // Build detailed activity log timeline
                $taskLogs = $approvedSubmissions->map(function ($sub) {
                    $def = $sub->assignment->taskDefinition ?? null;
                    $priority = $def->priority ?? 'medium';
                    $pts = match ($priority) {
                        'low' => 5,
                        'high' => 20,
                        'critical' => 30,
                        default => 10,
                    };
                    return [
                        'type' => 'Tugas',
                        'title' => 'Penyelesaian Tugas: ' . ($def->task_name ?? 'Tugas Kasir'),
                        'date' => $sub->reviewed_at ?? $sub->submitted_at,
                        'points' => '+' . $pts . ' Pts',
                        'badge' => 'Tugas ' . ucfirst($priority),
                        'badge_color' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800'
                    ];
                });

                $attendanceLogs = $attendances->map(function ($att) {
                    $pts = 0;
                    if ($att->clock_in) $pts += 15;
                    if ($att->clock_out) $pts += 15;
                    if ($att->clock_out_status === 'overtime' || $att->clock_out_status === 'on_time') $pts += 10;

                    return [
                        'type' => 'Absensi',
                        'title' => 'Sesi Laci & Absensi Shift (' . \Carbon\Carbon::parse($att->date)->format('d M Y') . ')',
                        'date' => $att->clock_out ?? $att->clock_in ?? $att->created_at,
                        'points' => '+' . $pts . ' Pts',
                        'badge' => 'Absensi Shift',
                        'badge_color' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                    ];
                });

                $posSummaryLog = collect([
                    [
                        'type' => 'POS Penjualan',
                        'title' => 'Akumulasi Penjualan Kasir (' . $userStats['total_transactions'] . ' Transaksi)',
                        'date' => now(),
                        'points' => '+' . $userStats['pos_points'] . ' Pts',
                        'badge' => 'Transaksi POS',
                        'badge_color' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400 border-blue-200 dark:border-blue-800'
                    ]
                ]);

                $recentLogs = $taskLogs->concat($attendanceLogs)->concat($posSummaryLog)
                    ->sortByDesc('date')
                    ->values();
            }
        }

        return view('livewire.guide.leaderboard', [
            'leaderboard' => $leaderboard,
            'currentUserRank' => $currentUserRank,
            'motivation' => $motivation,
            'detailUser' => $detailUser,
            'userStats' => $userStats,
            'recentLogs' => $recentLogs,
            'isManager' => in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan'])
        ])->layout('layouts.app', ['title' => 'Sistem Peringkat & Poin']);
    }
}

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

        if ($this->selectedUserId) {
            $detailUser = User::find($this->selectedUserId);
            if ($detailUser) {
                // Count transactions handled by user
                $userStats['total_transactions'] = Transaction::where('user_id', $detailUser->id)
                    ->whereIn('status', ['uang_diterima', 'belum_kembalian'])
                    ->distinct('reference')
                    ->count('reference');
                $userStats['pos_points'] = $userStats['total_transactions'] * 5;

                // Count completed tasks
                $userStats['completed_tasks'] = CashierTaskSubmission::where('submitted_by', $detailUser->id)
                    ->where('approval_status', 'approved')
                    ->count();
                $userStats['task_points'] = CashierTaskSubmission::where('cashier_task_submissions.submitted_by', $detailUser->id)
                    ->where('cashier_task_submissions.approval_status', 'approved')
                    ->join('cashier_task_assignments', 'cashier_task_submissions.task_assignment_id', '=', 'cashier_task_assignments.id')
                    ->join('cashier_task_definitions', 'cashier_task_assignments.task_definition_id', '=', 'cashier_task_definitions.id')
                    ->sum('cashier_task_definitions.points');

                // Count attendances
                $userStats['attendance_count'] = CashierAttendance::where('user_id', $detailUser->id)->count();
                $userStats['attendance_points'] = CashierAttendance::where('user_id', $detailUser->id)->sum('points_awarded');
            }
        }

        return view('livewire.guide.leaderboard', [
            'leaderboard' => $leaderboard,
            'currentUserRank' => $currentUserRank,
            'motivation' => $motivation,
            'detailUser' => $detailUser,
            'userStats' => $userStats,
            'isManager' => in_array(session('active_role_name'), ['superadmin', 'pengelola_jurusan'])
        ])->layout('layouts.app', ['title' => 'Sistem Peringkat & Poin']);
    }
}

<?php

namespace App\Livewire\Reports;

use App\Models\CashierNote;
use App\Models\NoteReply;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CashierNotes extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal state for Create / Edit Note
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    // Modal state for Replies
    public bool $showReplyModal = false;
    public $activeNoteId = null;
    public string $replyContent = '';

    public $noteId = null;
    public string $title = '';
    public string $content = '';
    public string $color = 'default';
    public bool $is_pinned = false;
    public ?string $target_user_id = null;
    public ?string $date = null;

    public $deletingNoteId = null;

    protected array $rules = [
        'title' => 'nullable|string|max:255',
        'content' => 'required|string',
        'color' => 'required|string|in:default,blue,emerald,amber,rose,purple',
        'is_pinned' => 'boolean',
        'target_user_id' => 'nullable|exists:users,id',
        'date' => 'nullable|date',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function togglePin($id): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $activeJurusanId = session('active_jurusan_id');

        $note = CashierNote::when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })->find($id);

        if ($note) {
            $note->update(['is_pinned' => ! $note->is_pinned]);
            $msg = $note->is_pinned ? 'Catatan disematkan ke paling atas!' : 'Sematkan catatan dilepas.';
            $this->dispatch('toast', message: $msg);
        }
    }    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('quill-update', content: '');
    }

    public function openEditModal($id): void
    {
        $user = auth()->user();
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $activeJurusanId = session('active_jurusan_id');

        $note = CashierNote::when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
            return $q->where('jurusan_id', $activeJurusanId);
        })->find($id);

        if ($note) {
            $this->noteId = $note->id;
            $this->title = $note->title ?? '';
            $this->content = $note->content;
            $this->color = $note->color ?? 'default';
            $this->is_pinned = (bool) $note->is_pinned;
            $this->target_user_id = $note->target_user_id;
            $this->date = $note->date ? $note->date->format('Y-m-d') : null;
            $this->showModal = true;
            $this->dispatch('quill-update', content: $this->content);
        }
    }
    public function saveNote(): void
    {
        $this->validate();

        $user = auth()->user();
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $activeJurusanId = session('active_jurusan_id');

        if ($this->noteId) {
            $note = CashierNote::when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->find($this->noteId);

            if ($note) {
                $note->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'color' => $this->color,
                    'is_pinned' => $this->is_pinned,
                    'target_user_id' => $this->target_user_id ?: null,
                    'date' => $this->date ?: null,
                ]);
                $this->dispatch('toast', message: 'Catatan berhasil diperbarui!');
            }
        } else {
            $note = CashierNote::create([
                'jurusan_id' => $activeJurusanId ?: null,
                'user_id' => auth()->id(),
                'target_user_id' => $this->target_user_id ?: null,
                'title' => $this->title,
                'content' => $this->content,
                'color' => $this->color,
                'is_pinned' => $this->is_pinned,
                'date' => $this->date ?: null,
            ]);

            if ($this->target_user_id && $this->target_user_id !== auth()->id()) {
                \App\Models\Notification::create([
                    'user_id' => $this->target_user_id,
                    'title' => 'Catatan Baru Ditujukan Untuk Anda',
                    'body' => auth()->user()->name . ' membuat catatan: "' . $this->title . '"',
                    'type' => 'note',
                    'action_url' => '/cashier-notes'
                ]);
            }
            $this->dispatch('toast', message: 'Catatan baru berhasil ditambahkan!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id): void
    {
        $this->deletingNoteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteNote(): void
    {
        if ($this->deletingNoteId) {
            $user = auth()->user();
            $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
            $activeJurusanId = session('active_jurusan_id');

            $note = CashierNote::when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })->find($this->deletingNoteId);

            if ($note) {
                $note->delete();
                $this->dispatch('toast', message: 'Catatan berhasil dihapus!');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingNoteId = null;
    }

    public function openReplyModal($id): void
    {
        $this->activeNoteId = $id;
        $this->replyContent = '';
        $this->showReplyModal = true;
    }

    public function addReply(): void
    {
        $this->validate([
            'replyContent' => 'required|string',
        ]);

        if ($this->activeNoteId) {
            $reply = NoteReply::create([
                'cashier_note_id' => $this->activeNoteId,
                'user_id' => auth()->id(),
                'content' => trim($this->replyContent),
            ]);

            $note = \App\Models\CashierNote::find($this->activeNoteId);
            if ($note) {
                $recipients = collect([$note->user_id, $note->target_user_id])
                    ->filter()
                    ->unique()
                    ->reject(fn($id) => $id === auth()->id());
                
                foreach ($recipients as $recipientId) {
                    \App\Models\Notification::create([
                        'user_id' => $recipientId,
                        'title' => 'Balasan Catatan Baru',
                        'body' => auth()->user()->name . ' membalas catatan "' . $note->title . '"',
                        'type' => 'note',
                        'action_url' => '/cashier-notes'
                    ]);
                }
            }

            $this->replyContent = '';
            $this->dispatch('toast', message: 'Balasan berhasil dikirim!');
        }
    }

    public function deleteReply($replyId): void
    {
        $reply = NoteReply::find($replyId);
        $user = auth()->user();
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $isAdminOrManager = $user->roles()->whereIn('name', ['admin', 'pengelola_jurusan'])->exists();

        if ($reply && ($reply->user_id === $user->id || $isSuperAdmin || $isAdminOrManager)) {
            $reply->delete();
            $this->dispatch('toast', message: 'Balasan berhasil dihapus.');
        }
    }

    public function resetForm(): void
    {
        $this->noteId = null;
        $this->title = '';
        $this->content = '';
        $this->color = 'default';
        $this->is_pinned = false;
        $this->target_user_id = null;
        $this->date = now()->toDateString();
        $this->resetValidation();
    }

    public function render()
    {
        $user = auth()->user();
        $currentUserId = $user->id;
        $isSuperAdmin = $user->roles()->where('name', 'superadmin')->exists();
        $activeJurusanId = session('active_jurusan_id');

        // Get target users list (Superadmin sees all users across the system)
        $usersList = User::where('id', '!=', $currentUserId)
            ->when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->whereHas('roles', function ($sq) use ($activeJurusanId) {
                    $sq->where('jurusan_id', $activeJurusanId);
                });
            })
            ->orderBy('name')
            ->get();

        // Query notes: Superadmin can see all notes or scope to active jurusan if selected
        $notes = CashierNote::with(['user', 'targetUser', 'replies.user'])
            ->when(! $isSuperAdmin && $activeJurusanId, function ($q) use ($activeJurusanId) {
                return $q->where('jurusan_id', $activeJurusanId);
            })
            ->when(! $isSuperAdmin, function ($q) use ($currentUserId) {
                return $q->where(function ($sq) use ($currentUserId) {
                    $sq->whereNull('target_user_id')
                        ->orWhere('user_id', $currentUserId)
                        ->orWhere('target_user_id', $currentUserId);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%')
                        ->orWhere('date', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $activeNote = $this->activeNoteId
            ? CashierNote::with(['user', 'targetUser', 'replies.user'])->find($this->activeNoteId)
            : null;

        return view('livewire.reports.cashier-notes', [
            'notes' => $notes,
            'usersList' => $usersList,
            'activeNote' => $activeNote,
            'isSuperAdmin' => $isSuperAdmin,
        ])->layout('layouts.app', ['title' => 'Catatan Kasir']);
    }
}

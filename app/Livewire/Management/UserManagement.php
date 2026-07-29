<?php

namespace App\Livewire\Management;

use App\Models\Jurusan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads;

    public $search = '';

    // Form State
    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $assignedAccesses = []; // Array of ['role_id' => ..., 'role_label' => ..., 'jurusan_id' => ..., 'jurusan_name' => ...]

    // Temp inputs for adding access
    public $selectedRoleId = '';
    public $selectedJurusanId = '';

    // UI state
    public $showModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    // Excel import state
    public $excelFile;
    public $showImportModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        // Load accesses
        $accesses = $user->getAvailableAccesses();
        foreach ($accesses as $acc) {
            $this->assignedAccesses[] = [
                'role_id' => $acc->role_id,
                'role_label' => $acc->role_label,
                'jurusan_id' => $acc->jurusan_id,
                'jurusan_name' => $acc->jurusan_name ?? 'Global',
            ];
        }

        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->assignedAccesses = [];
        $this->selectedRoleId = '';
        $this->selectedJurusanId = '';
    }

    public function addAccess()
    {
        $this->validate([
            'selectedRoleId' => 'required',
        ]);

        $role = Role::find($this->selectedRoleId);
        $jurusan = $this->selectedJurusanId ? Jurusan::find($this->selectedJurusanId) : null;

        if (! $role) {
            return;
        }

        // Prevent duplicate accesses
        foreach ($this->assignedAccesses as $acc) {
            if ($acc['role_id'] === $role->id && $acc['jurusan_id'] === ($jurusan ? $jurusan->id : null)) {
                $this->dispatch('toast', message: 'Hak akses ini sudah ditambahkan.');

                return;
            }
        }

        $this->assignedAccesses[] = [
            'role_id' => $role->id,
            'role_label' => $role->label,
            'jurusan_id' => $jurusan ? $jurusan->id : null,
            'jurusan_name' => $jurusan ? $jurusan->name : 'Global',
        ];

        // Reset selections
        $this->selectedRoleId = '';
        $this->selectedJurusanId = '';
    }

    public function removeAccess($index)
    {
        unset($this->assignedAccesses[$index]);
        $this->assignedAccesses = array_values($this->assignedAccesses);
    }

    public function saveUser()
    {
        $this->validate();

        if (! $this->userId) {
            $this->validate([
                'email' => 'unique:users,email',
                'password' => 'required|min:6',
            ]);
        } else {
            $this->validate([
                'email' => 'unique:users,email,'.$this->userId,
                'password' => 'nullable|min:6',
            ]);
        }

        DB::transaction(function () {
            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->name = $this->name;
                $user->email = $this->email;
                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }
                $user->save();
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);
            }

            // Sync accesses in pivot table
            DB::table('role_user')->where('user_id', $user->id)->delete();

            foreach ($this->assignedAccesses as $acc) {
                DB::table('role_user')->insert([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $acc['role_id'],
                    'jurusan_id' => $acc['jurusan_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $this->showModal = false;
        $this->dispatch('toast', message: 'Data user berhasil disimpan.');
        $this->resetForm();
    }

    public function getAllUsersForExport()
    {
        return User::all()->map(function ($user) {
            $userAccesses = $user->getAvailableAccesses();
            $roleLabel = count($userAccesses) > 0 ? $userAccesses[0]->role_label : 'User';
            $isKasir = false;
            foreach ($userAccesses as $acc) {
                if ($acc->role_name === 'kasir') {
                    $isKasir = true;
                }
            }

            return [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleLabel,
                'initials' => $user->initials(),
                'isKasir' => $isKasir,
            ];
        })->toArray();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deleteId) {
            // Prevent deleting self
            if ($this->deleteId === auth()->id()) {
                $this->dispatch('toast', message: 'Anda tidak dapat menghapus akun Anda sendiri.');
                $this->showDeleteModal = false;

                return;
            }

            User::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', message: 'Akun user berhasil dihapus.');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers (Tanpa Password)
        $sheet->setCellValue('A1', 'Nama Lengkap');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Role');
        $sheet->setCellValue('D1', 'Jurusan');

        // Style header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        foreach(range('A','D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions or example values
        $activeRole = session('active_role_name');
        $activeJurusan = Jurusan::find(session('active_jurusan_id'));

        if ($activeRole === 'pengelola_jurusan' && $activeJurusan) {
            $sheet->setCellValue('A2', 'Kasir Teladan');
            $sheet->setCellValue('B2', 'kasir1@mail.com');
            $sheet->setCellValue('C2', 'kasir');
            $sheet->setCellValue('D2', $activeJurusan->name);
        } else {
            $sheet->setCellValue('A2', 'Ahmad Dani');
            $sheet->setCellValue('B2', 'ahmad@mail.com');
            $sheet->setCellValue('C2', 'kasir');
            $sheet->setCellValue('D2', 'RPL');
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, 'template_import_user.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $filePath = $this->excelFile->getRealPath();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Remove header row
        array_shift($rows);

        $activeRole = session('active_role_name');
        $activeJurusanId = session('active_jurusan_id');

        $imported = 0;
        $errors = [];

        DB::transaction(function() use ($rows, $activeRole, $activeJurusanId, &$imported, &$errors) {
            foreach ($rows as $index => $row) {
                if (empty($row[0]) || empty($row[1])) {
                    continue; // Skip empty rows
                }

                $name = trim($row[0]);
                $email = trim($row[1]);
                $roleName = strtolower(trim($row[2] ?? 'kasir'));
                $jurusanName = trim($row[3] ?? '');

                // Validation: Check duplicate email
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris " . ($index + 2) . ": Email {$email} sudah digunakan.";
                    continue;
                }

                // Validation: Pengelola Jurusan is restricted to 'kasir' role only
                if ($activeRole === 'pengelola_jurusan') {
                    $roleName = 'kasir';
                }

                $role = Role::where('name', $roleName)->first();
                if (!$role) {
                    $errors[] = "Baris " . ($index + 2) . ": Role '{$roleName}' tidak valid.";
                    continue;
                }

                // Resolve Jurusan
                $jurusan = null;
                if ($activeRole === 'pengelola_jurusan') {
                    $jurusan = Jurusan::find($activeJurusanId);
                } elseif (!empty($jurusanName)) {
                    $jurusan = Jurusan::where('name', $jurusanName)->first();
                    
                    if (!$jurusan && strtolower($jurusanName) !== 'global') {
                        $errors[] = "Baris " . ($index + 2) . ": Jurusan '{$jurusanName}' tidak ditemukan.";
                        continue;
                    }
                }

                // Create User (default password is '00000000')
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('00000000'),
                ]);

                // Assign role and jurusan access
                DB::table('role_user')->insert([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'jurusan_id' => $jurusan ? $jurusan->id : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported++;
            }
        });

        $this->showImportModal = false;
        $this->excelFile = null;

        if (count($errors) > 0) {
            $msg = "Berhasil mengimpor {$imported} user. Beberapa baris gagal:\n" . implode("\n", $errors);
            $this->dispatch('toast', message: $msg);
        } else {
            $this->dispatch('toast', message: "Berhasil mengimpor {$imported} user.");
        }
    }

    public function render()
    {
        $activeRole = session('active_role_name');
        $activeJurusanId = session('active_jurusan_id');

        // Superadmin & Pengelola Jurusan can access this page
        if (!in_array($activeRole, ['superadmin', 'pengelola_jurusan'])) {
            abort(403, 'Unauthorized action.');
        }

        // Query users
        $users = User::where(function ($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        })
        ->when($activeRole === 'pengelola_jurusan', function ($q) use ($activeJurusanId) {
            // Pengelola Jurusan only sees users that have cashier role in their jurusan
            return $q->whereHas('roles', function($sq) use ($activeJurusanId) {
                $sq->where('roles.name', 'kasir')
                  ->where('role_user.jurusan_id', $activeJurusanId);
            });
        })
        ->latest()
        ->paginate(10);

        // Filter roles and jurusans list for form selection
        if ($activeRole === 'superadmin') {
            $roles = Role::orderBy('label')->get();
            $jurusans = Jurusan::orderBy('name')->get();
        } else {
            // Pengelola Jurusan can only assign cashier role for their active jurusan
            $roles = Role::where('name', 'kasir')->get();
            $jurusans = Jurusan::where('id', $activeJurusanId)->get();
        }

        return view('livewire.management.user-management', [
            'users' => $users,
            'roles' => $roles,
            'jurusans' => $jurusans,
        ])->layout('layouts.app', ['title' => 'Manajemen User']);
    }
}

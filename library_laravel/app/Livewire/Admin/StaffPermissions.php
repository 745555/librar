<?php

namespace App\Livewire\Admin;

use App\Models\LibraryStaff;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class StaffPermissions extends Component
{
    public $users;
    public $selectedUserId;
    public $selectedUserName;
    public $userPermissions = [];
    public $userRoles = [];
    public $allPermissions = [];
    public $allRoles = [];
    public $search = '';

    protected $queryString = ['search'];

    public function mount()
    {
        $this->authorize('system.manage');
        $this->loadUsers();
        $this->allPermissions = Permission::all()->groupBy(function($perm) {
            return explode('.', $perm->name)[0];
        })->map->toArray()->toArray();

        $this->allRoles = Role::all()->toArray();
    }

    public function loadUsers()
    {
        $this->authorize('system.manage');
        $this->users = LibraryStaff::where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('username', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->get()
            ->toArray();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function selectUser($userId)
    {
        $this->authorize('system.manage');
        $user = LibraryStaff::findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name ?? $user->full_name;
        $this->userPermissions = $user->permissions->pluck('name')->toArray();
        $this->userRoles = $user->roles->pluck('name')->toArray();
    }

    public function togglePermission($permissionName)
    {
        $this->authorize('system.manage');
        if (!$this->selectedUserId) return;

        try {
            $user = LibraryStaff::findOrFail($this->selectedUserId);
            
            if ($user->hasDirectPermission($permissionName)) {
                $user->revokePermissionTo($permissionName);
            } else {
                $user->givePermissionTo($permissionName);
            }

            $this->userPermissions = $user->refresh()->permissions->pluck('name')->toArray();
            session()->flash('success', 'تم تحديث الصلاحية بنجاح');
            $this->dispatch('swal:success', ['message' => 'تم تحديث الصلاحية بنجاح']);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء تحديث الصلاحية.']);
            session()->flash('error', 'حدث خطأ أثناء تحديث الصلاحية.');
        }
    }

    public function toggleRole($roleName)
    {
        $this->authorize('system.manage');
        if (!$this->selectedUserId) return;

        try {
            $user = LibraryStaff::findOrFail($this->selectedUserId);
            
            if ($user->hasRole($roleName)) {
                $user->removeRole($roleName);
            } else {
                $user->assignRole($roleName);
            }

            $this->userRoles = $user->refresh()->roles->pluck('name')->toArray();
            session()->flash('success', 'تم تحديث الرتبة بنجاح');
            $this->dispatch('swal:success', ['message' => 'تم تحديث الرتبة بنجاح']);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء تحديث الرتبة.']);
            session()->flash('error', 'حدث خطأ أثناء تحديث الرتبة.');
        }
    }

    public function render()
    {
        return view('livewire.admin.staff-permissions')->layout('layouts.app');
    }
}

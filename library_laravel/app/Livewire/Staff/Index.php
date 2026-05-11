<?php

namespace App\Livewire\Staff;

use App\Models\LibraryStaff;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Index extends Component
{
    public $staffId = null;
    public $username;
    public $password;
    public $full_name;
    public $email;
    public $phone;
    public $role = 'staff';
    public $new_password;

    public $isEditMode = false;

    public function mount()
    {
        $this->authorize('staff.view');
    }

    public function resetForm()
    {
        $this->staffId = null;
        $this->username = '';
        $this->password = '';
        $this->full_name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'staff';
        $this->new_password = '';
        $this->isEditMode = false;
    }

    public function addStaff()
    {
        $this->authorize('staff.create');
        $this->validate([
            'username' => 'required|unique:library_staff,username',
            'password' => 'required|min:6',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'required|in:admin,staff',
        ]);

        try {
            LibraryStaff::create([
                'username' => $this->username,
                'password' => Hash::make($this->password),
                'full_name' => $this->full_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
            ]);

            session()->flash('success', 'تم إضافة الموظف بنجاح');
            $this->dispatch('swal:success', ['message' => 'تم إضافة الموظف بنجاح']);
            $this->resetForm();
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء إضافة الموظف.']);
            session()->flash('error', 'حدث خطأ أثناء إضافة الموظف.');
        }
    }

    public function editStaff($id)
    {
        $this->authorize('staff.edit');
        $staff = LibraryStaff::findOrFail($id);
        $this->staffId = $staff->id;
        $this->username = $staff->username;
        $this->full_name = $staff->full_name;
        $this->email = $staff->email;
        $this->phone = $staff->phone;
        $this->role = $staff->role;
        $this->isEditMode = true;
    }

    public function updateStaff()
    {
        $this->authorize('staff.edit');
        $this->validate([
            'username' => 'required|string|max:50|unique:library_staff,username,' . $this->staffId,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'required|in:admin,staff',
            'new_password' => 'nullable|min:6',
        ]);

        try {
            $staff = LibraryStaff::findOrFail($this->staffId);
            $data = [
                'username' => $this->username,
                'full_name' => $this->full_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
            ];

            if ($this->new_password) {
                $data['password'] = Hash::make($this->new_password);
            }

            $staff->update($data);

            session()->flash('success', 'تم تحديث بيانات الموظف بنجاح');
            $this->dispatch('swal:success', ['message' => 'تم تحديث بيانات الموظف بنجاح']);
            $this->resetForm();
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء تحديث بيانات الموظف.']);
            session()->flash('error', 'حدث خطأ أثناء تحديث بيانات الموظف.');
        }
    }

    public function deleteStaff($id)
    {
        $this->authorize('staff.delete');
        if ($id == Auth::id()) {
            session()->flash('error', 'لا يمكنك حذف حسابك الخاص');
            return;
        }

        try {
            LibraryStaff::findOrFail($id)->delete();
            $this->dispatch('swal:success', ['message' => 'تم حذف الموظف بنجاح']);
            session()->flash('success', 'تم حذف الموظف بنجاح');
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'لا يمكن حذف الموظف لوجود سجلات مرتبطة به.']);
            session()->flash('error', 'لا يمكن حذف الموظف لوجود سجلات مرتبطة به.');
        }
    }

    public function render()
    {
        return view('livewire.staff.index', [
            'staff_list' => LibraryStaff::all()
        ])->layout('layouts.app');
    }
}

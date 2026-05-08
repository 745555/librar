<?php

namespace App\Livewire\Staff;

use App\Models\LibraryStaff;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
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
        $this->validate([
            'username' => 'required|unique:library_staff,username',
            'password' => 'required|min:6',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'required|in:admin,staff',
        ]);

        LibraryStaff::create([
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
        ]);

        session()->flash('success', 'تم إضافة الموظف بنجاح');
        $this->resetForm();
    }

    public function editStaff($id)
    {
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
        $this->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'required|in:admin,staff',
            'new_password' => 'nullable|min:6',
        ]);

        $staff = LibraryStaff::find($this->staffId);
        $data = [
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
        $this->resetForm();
    }

    public function deleteStaff($id)
    {
        if ($id == Auth::id()) {
            session()->flash('error', 'لا يمكنك حذف حسابك الخاص');
            return;
        }

        LibraryStaff::find($id)->delete();
        session()->flash('success', 'تم حذف الموظف بنجاح');
    }

    public function render()
    {
        return view('livewire.staff.index', [
            'staff_list' => LibraryStaff::all()
        ])->layout('layouts.app');
    }
}

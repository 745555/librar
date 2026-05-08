<?php

namespace App\Livewire;

use App\Models\LibraryStaff;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Account extends Component
{
    use WithFileUploads;

    public $full_name;
    public $email;
    public $phone;
    public $avatar;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function saveProfile()
    {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'تم تحديث البيانات بنجاح');
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048', // 2MB Max
        ]);

        $user = Auth::user();
        
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $filename = 'user_' . $user->id . '_' . time() . '.' . $this->avatar->extension();
        $this->avatar->storeAs('avatars', $filename, 'public');

        $user->update(['avatar' => $filename]);

        session()->flash('success', 'تم تحديث الصورة الشخصية بنجاح');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'كلمة المرور الحالية غير صحيحة');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('success', 'تم تغيير كلمة المرور بنجاح');
    }

    public function render()
    {
        return view('livewire.account')->layout('layouts.app');
    }
}

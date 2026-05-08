<?php

namespace App\Livewire\Borrowings;

use App\Models\FacultyBorrowing;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $borrowId = null;
    public $faculty_name;
    public $faculty_department;
    public $faculty_contact;
    public $book_title;
    public $book_isbn;
    public $borrow_date;
    public $expected_return_date;
    public $notes;

    public $search = '';

    protected $rules = [
        'faculty_name' => 'required|string|max:255',
        'faculty_department' => 'required|string|max:255',
        'faculty_contact' => 'nullable|string|max:255',
        'book_title' => 'required|string|max:255',
        'book_isbn' => 'nullable|string|max:20',
        'borrow_date' => 'required|date',
        'expected_return_date' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->borrowId = null;
        $this->faculty_name = '';
        $this->faculty_department = '';
        $this->faculty_contact = '';
        $this->book_title = '';
        $this->book_isbn = '';
        $this->borrow_date = date('Y-m-d');
        $this->expected_return_date = date('Y-m-d', strtotime('+14 days'));
        $this->notes = '';
    }

    public function editBorrowing($id)
    {
        $borrow = FacultyBorrowing::findOrFail($id);
        $this->borrowId = $borrow->id;
        $this->faculty_name = $borrow->faculty_name;
        $this->faculty_department = $borrow->faculty_department;
        $this->faculty_contact = $borrow->faculty_contact;
        $this->book_title = $borrow->book_title;
        $this->book_isbn = $borrow->book_isbn;
        $this->borrow_date = $borrow->borrow_date->format('Y-m-d');
        $this->expected_return_date = $borrow->expected_return_date->format('Y-m-d');
        $this->notes = $borrow->notes;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'faculty_name' => $this->faculty_name,
            'faculty_department' => $this->faculty_department,
            'faculty_contact' => $this->faculty_contact,
            'book_title' => $this->book_title,
            'book_isbn' => $this->book_isbn,
            'borrow_date' => $this->borrow_date,
            'expected_return_date' => $this->expected_return_date,
            'notes' => $this->notes,
        ];

        if ($this->borrowId) {
            FacultyBorrowing::find($this->borrowId)->update($data);
            session()->flash('success', 'تم تحديث طلب الإعارة بنجاح');
        } else {
            FacultyBorrowing::create($data);
            session()->flash('success', 'تم تسجيل طلب الإعارة بنجاح');
        }

        $this->resetForm();
    }

    public function deleteBorrowing($id)
    {
        FacultyBorrowing::find($id)->delete();
        session()->flash('success', 'تم حذف طلب الإعارة بنجاح');
    }

    public function render()
    {
        $borrowings = FacultyBorrowing::where(function($q) {
                $q->where('faculty_name', 'like', '%' . $this->search . '%')
                  ->orWhere('book_title', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.borrowings.index', [
            'borrowings' => $borrowings
        ])->layout('layouts.app');
    }
}

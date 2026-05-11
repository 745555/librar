<?php

namespace App\Livewire\Borrowings;

use App\Actions\Borrowings\UpsertBorrowing;
use App\Models\FacultyBorrowing;
use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

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
        $this->authorize('borrowings.view');
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
        $this->authorize('borrowings.edit');
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
        if ($this->borrowId) {
            $this->authorize('borrowings.edit');
        } else {
            $this->authorize('borrowings.create');
        }

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

        try {
            app(UpsertBorrowing::class)->execute($this->borrowId ? (int) $this->borrowId : null, $data);

            $successMessage = $this->borrowId ? 'تم تحديث طلب الإعارة بنجاح' : 'تم تسجيل طلب الإعارة بنجاح';
            session()->flash('success', $successMessage);
            $this->dispatch('swal:success', ['message' => $successMessage]);

            $this->resetForm();
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء حفظ البيانات.']);
            session()->flash('error', 'حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function deleteBorrowing($id)
    {
        $this->authorize('borrowings.delete');
        try {
            FacultyBorrowing::findOrFail($id)->delete();
            session()->flash('success', 'تم حذف طلب الإعارة بنجاح');
            $this->dispatch('swal:success', ['message' => 'تم حذف طلب الإعارة بنجاح']);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء محاولة الحذف.']);
            session()->flash('error', 'حدث خطأ أثناء محاولة الحذف.');
        }
    }

    public function returnBook($id)
    {
        $this->authorize('borrowings.edit');
        
        try {
            $borrowing = FacultyBorrowing::findOrFail($id);
            
            // Check if already returned
            if ($borrowing->actual_return_date) {
                session()->flash('error', 'هذا الكتاب تم إرجاعه بالفعل');
                $this->dispatch('swal:error', ['message' => 'هذا الكتاب تم إرجاعه بالفعل']);
                return;
            }
            
            // Update borrowing record
            $borrowing->actual_return_date = now();
            $borrowing->status = 'returned';
            $borrowing->save();
            
            // Update book available quantity - try multiple ways to find the book
            $book = null;
            
            // First try by exact title match
            if (!empty($borrowing->book_title)) {
                $book = Book::where('book_title', $borrowing->book_title)->first();
            }
            
            // If not found, try by ISBN
            if (!$book && !empty($borrowing->book_isbn)) {
                $book = Book::where('isbn', $borrowing->book_isbn)->first();
            }
            
            // If still not found, try partial title match
            if (!$book && !empty($borrowing->book_title)) {
                $book = Book::where('book_title', 'like', '%' . $borrowing->book_title . '%')->first();
            }
            
            if ($book) {
                $book->increment('available_quantity');
                session()->flash('success', 'تم إرجاع الكتاب بنجاح وتم تحديث الكمية المتاحة');
                $this->dispatch('swal:success', ['message' => 'تم إرجاع الكتاب بنجاح وتم تحديث الكمية المتاحة']);
            } else {
                session()->flash('warning', 'تم إرجاع الكتاب ولكن لم يتم العثور على سجل الكتاب لتحديث الكمية');
                $this->dispatch('swal:success', ['message' => 'تم إرجاع الكتاب بنجاح']);
            }
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'لم يتم العثور على سجل الإعارة');
            $this->dispatch('swal:error', ['message' => 'لم يتم العثور على سجل الإعارة']);
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'حدث خطأ أثناء إرجاع الكتاب: ' . $e->getMessage());
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء إرجاع الكتاب']);
        }
    }

    public function exportBorrowings()
    {
        $borrowings = FacultyBorrowing::where(function($q) {
                $q->where('faculty_name', 'like', '%' . $this->search . '%')
                  ->orWhere('book_title', 'like', '%' . $this->search . '%');
            })
            ->orderBy('borrow_date', 'desc')
            ->get();

        $filename = 'borrowings-export-' . date('Y-m-d-H-i-s') . '.html';
        
        return response()->streamDownload(function() use ($borrowings) {
            echo view('exports.borrowings-html', [
                'borrowings' => $borrowings,
                'search' => $this->search
            ])->render();
        }, $filename, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
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

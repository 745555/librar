<?php

namespace App\Livewire\Books;

use App\Actions\Books\UpsertBook;
use App\Models\Book;
use App\Models\Department;
use App\Rules\Isbn;
use Livewire\Component;
use Throwable;

class BookForm extends Component
{
    public $bookId = null;
    public $book_title;
    public $author;
    public $isbn;
    public $publisher;
    public $publication_year;
    public $quantity = null;
    public $department_id;
    public $custom_department;
    public $description;

    public function mount($id = null)
    {
        if ($id) {
            $this->authorize('books.edit');
            $this->bookId = $id;
            $book = Book::findOrFail($id);
            $this->book_title = $book->book_title;
            $this->author = $book->author;
            $this->isbn = $book->isbn;
            $this->publisher = $book->publisher;
            $this->publication_year = $book->publication_year;
            $this->quantity = (int) $book->quantity; // Ensure integer
            $this->department_id = $book->department_id;
            $this->custom_department = $book->custom_department;
            $this->description = $book->description;
        }
    }

    public function rules(): array
    {
        return [
            'book_title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => ['nullable', 'string', 'max:20', new Isbn],
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1',
            'department_id' => 'nullable|exists:departments,id',
            'custom_department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        if ($this->bookId) {
            $this->authorize('books.edit');
        } else {
            $this->authorize('books.create');
        }

        $this->validate();
        
        // Custom validation for quantity
        if (is_null($this->quantity) || $this->quantity < 1) {
            $this->addError('quantity', 'يجب إدخال كمية صحيحة وموجبة');
            return;
        }

        // Ensure quantity is properly handled - cast to integer and validate
        $cleanQuantity = (int) $this->quantity;
        if ($cleanQuantity < 1) {
            $cleanQuantity = 1;
        }

        // Calculate available_quantity correctly
        if ($this->bookId) {
            // When editing: adjust available_quantity by the quantity difference
            // This preserves the count of currently borrowed copies
            $oldBook = Book::find($this->bookId);
            $quantityDiff = $cleanQuantity - (int) $oldBook->quantity;
            $availableQuantity = (int) $oldBook->available_quantity + $quantityDiff;
            // Ensure available_quantity doesn't exceed total quantity or go below 0
            $availableQuantity = max(0, min($availableQuantity, $cleanQuantity));
        } else {
            // When creating: all copies are available
            $availableQuantity = $cleanQuantity;
        }

        $data = [
            'book_title' => $this->book_title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'publisher' => $this->publisher,
            'publication_year' => $this->publication_year,
            'quantity' => $cleanQuantity,
            'available_quantity' => $availableQuantity,
            'department_id' => $this->department_id,
            'custom_department' => $this->custom_department,
            'description' => $this->description,
        ];

        try {
            app(UpsertBook::class)->execute($this->bookId ? (int) $this->bookId : null, $data);

            session()->flash('success', $this->bookId ? 'تم تحديث الكتاب بنجاح' : 'تم إضافة الكتاب بنجاح');

            return redirect()->route('books.index');
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'حدث خطأ أثناء حفظ بيانات الكتاب.');
        }
    }

    public function updatedQuantity($value)
    {
        // Only process if value is not null/empty
        if ($value !== null && $value !== '') {
            // Ensure quantity is always an integer and positive
            $this->quantity = (int) $value;
            if ($this->quantity < 1) {
                $this->quantity = 1;
            }
        }
    }

    public function render()
    {
        return view('livewire.books.form', [
            'departments' => Department::all()
        ])->layout('layouts.app');
    }
}

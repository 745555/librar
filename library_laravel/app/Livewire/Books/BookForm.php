<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\Department;
use Livewire\Component;

class BookForm extends Component
{
    public $bookId = null;
    public $book_title;
    public $author;
    public $isbn;
    public $publisher;
    public $publication_year;
    public $quantity = 1;
    public $department_id;
    public $custom_department;
    public $description;

    public function mount($id = null)
    {
        if ($id) {
            $this->bookId = $id;
            $book = Book::findOrFail($id);
            $this->book_title = $book->book_title;
            $this->author = $book->author;
            $this->isbn = $book->isbn;
            $this->publisher = $book->publisher;
            $this->publication_year = $book->publication_year;
            $this->quantity = $book->quantity;
            $this->department_id = $book->department_id;
            $this->custom_department = $book->custom_department;
            $this->description = $book->description;
        }
    }

    protected $rules = [
        'book_title' => 'required|string|max:255',
        'author' => 'nullable|string|max:255',
        'isbn' => 'nullable|string|max:20',
        'publisher' => 'nullable|string|max:255',
        'publication_year' => 'nullable|integer',
        'quantity' => 'required|integer|min:1',
        'department_id' => 'nullable|exists:departments,id',
        'custom_department' => 'nullable|string|max:100',
        'description' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'book_title' => $this->book_title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'publisher' => $this->publisher,
            'publication_year' => $this->publication_year,
            'quantity' => $this->quantity,
            'available_quantity' => $this->quantity, // Simple logic for now
            'department_id' => $this->department_id,
            'custom_department' => $this->custom_department,
            'description' => $this->description,
        ];

        if ($this->bookId) {
            Book::find($this->bookId)->update($data);
            session()->flash('success', 'تم تحديث الكتاب بنجاح');
        } else {
            Book::create($data);
            session()->flash('success', 'تم إضافة الكتاب بنجاح');
        }

        return redirect()->route('books.index');
    }

    public function render()
    {
        return view('livewire.books.form', [
            'departments' => Department::all()
        ])->layout('layouts.app');
    }
}

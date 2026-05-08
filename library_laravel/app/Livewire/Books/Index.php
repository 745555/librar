<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedBook = null;

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteBook($id)
    {
        $book = Book::find($id);
        if ($book) {
            try {
                $book->delete();
                session()->flash('success', 'تم حذف الكتاب بنجاح');
            } catch (\Exception $e) {
                session()->flash('error', 'لا يمكن حذف الكتاب لأنه مرتبط بإعارات');
            }
        }
    }

    public function showBook($id)
    {
        $this->selectedBook = Book::with('department')->find($id)->toArray();
        $this->dispatch('open-modal', 'book-preview');
    }

    public function render()
    {
        $books = Book::with('department')
            ->where(function($q) {
                $q->where('book_title', 'like', '%' . $this->search . '%')
                  ->orWhere('isbn', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.books.index', [
            'books' => $books
        ])->layout('layouts.app');
    }
}

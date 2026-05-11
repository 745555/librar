<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedBook = null;
    
    // Advanced search filters
    public $department_id = '';
    public $publication_year = '';
    public $publisher = '';
    public $min_quantity = '';
    public $max_quantity = '';
    public $showAdvancedSearch = false;

    protected $updatesQueryString = ['search', 'department_id', 'publication_year', 'publisher', 'min_quantity', 'max_quantity'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingPublicationYear()
    {
        $this->resetPage();
    }

    public function updatingPublisher()
    {
        $this->resetPage();
    }

    public function updatingMinQuantity()
    {
        $this->resetPage();
    }

    public function updatingMaxQuantity()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'department_id', 'publication_year', 'publisher', 'min_quantity', 'max_quantity']);
        $this->resetPage();
    }

    public function toggleAdvancedSearch()
    {
        $this->showAdvancedSearch = !$this->showAdvancedSearch;
    }

    public function exportBooks()
    {
        $books = Book::with('department')
            ->when($this->department_id, function($q) {
                $q->where('department_id', $this->department_id);
            })
            ->when($this->publication_year, function($q) {
                $q->where('publication_year', $this->publication_year);
            })
            ->when($this->publisher, function($q) {
                $q->where('publisher', 'like', '%' . $this->publisher . '%');
            })
            ->when($this->min_quantity, function($q) {
                $q->where('quantity', '>=', $this->min_quantity);
            })
            ->when($this->max_quantity, function($q) {
                $q->where('quantity', '<=', $this->max_quantity);
            })
            ->when($this->search, function($q) {
                $q->where(function($subQuery) {
                    $subQuery->where('book_title', 'like', '%' . $this->search . '%')
                          ->orWhere('isbn', 'like', '%' . $this->search . '%')
                          ->orWhere('author', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('book_title')
            ->get();

        $filename = 'books-export-' . date('Y-m-d-H-i-s') . '.html';
        
        return response()->streamDownload(function() use ($books) {
            echo view('exports.books-html', [
                'books' => $books,
                'filters' => $this->getActiveFilters()
            ])->render();
        }, $filename, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function getActiveFilters()
    {
        $filters = [];
        if ($this->search) $filters['بحث'] = $this->search;
        if ($this->department_id) {
            $dept = \App\Models\Department::find($this->department_id);
            $filters['القسم'] = $dept?->name_ar;
        }
        if ($this->publication_year) $filters['سنة النشر'] = $this->publication_year;
        if ($this->publisher) $filters['الناشر'] = $this->publisher;
        if ($this->min_quantity) $filters['الكمية من'] = $this->min_quantity;
        if ($this->max_quantity) $filters['الكمية إلى'] = $this->max_quantity;
        return $filters;
    }

    public function deleteBook($id)
    {
        $this->authorize('books.delete');
        $book = Book::find($id);
        if ($book) {
            try {
                $book->delete(); // Soft delete
                session()->flash('success', 'تم نقل الكتاب إلى سلة المحذوفات');
                $this->dispatch('swal:success', ['message' => 'تم نقل الكتاب إلى سلة المحذوفات']);
            } catch (Throwable $e) {
                report($e);
                $this->dispatch('swal:error', ['message' => 'لا يمكن حذف الكتاب لأنه مرتبط بإعارات']);
                session()->flash('error', 'لا يمكن حذف الكتاب لأنه مرتبط بإعارات');
            }
        }
    }

    public function restoreBook($id)
    {
        $this->authorize('books.delete');
        $book = Book::withTrashed()->find($id);
        if ($book) {
            try {
                $book->restore();
                session()->flash('success', 'تم استرجاع الكتاب بنجاح');
                $this->dispatch('swal:success', ['message' => 'تم استرجاع الكتاب بنجاح']);
            } catch (Throwable $e) {
                report($e);
                $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء استرجاع الكتاب']);
                session()->flash('error', 'حدث خطأ أثناء استرجاع الكتاب.');
            }
        }
    }

    public function forceDeleteBook($id)
    {
        $this->authorize('books.delete');
        $book = Book::withTrashed()->find($id);
        if ($book) {
            try {
                $book->forceDelete();
                session()->flash('success', 'تم حذف الكتاب نهائياً');
                $this->dispatch('swal:success', ['message' => 'تم حذف الكتاب نهائياً']);
            } catch (Throwable $e) {
                report($e);
                $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء الحذف النهائي']);
                session()->flash('error', 'حدث خطأ أثناء الحذف النهائي.');
            }
        }
    }

    public function showBook($id)
    {
        $book = Book::with(['department', 'borrowings' => function($query) {
            $query->orderBy('borrow_date', 'desc');
        }])->find($id);
        
        if (! $book) {
            session()->flash('error', 'الكتاب غير موجود');
            $this->dispatch('swal:error', ['message' => 'الكتاب غير موجود']);
            return;
        }

        $this->selectedBook = $book->toArray();
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
            ->when($this->department_id, function($q) {
                $q->where('department_id', $this->department_id);
            })
            ->when($this->publication_year, function($q) {
                $q->where('publication_year', $this->publication_year);
            })
            ->when($this->publisher, function($q) {
                $q->where('publisher', 'like', '%' . $this->publisher . '%');
            })
            ->when($this->min_quantity, function($q) {
                $q->where('quantity', '>=', $this->min_quantity);
            })
            ->when($this->max_quantity, function($q) {
                $q->where('quantity', '<=', $this->max_quantity);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.books.index', [
            'books' => $books,
            'departments' => \App\Models\Department::orderBy('name_ar')->get()
        ])->layout('layouts.app');
    }
}

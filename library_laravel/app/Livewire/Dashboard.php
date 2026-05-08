<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Project;
use App\Models\FacultyBorrowing;
use App\Models\LibraryStaff;
use App\Models\Department;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $stats = [];
    public $books_per_dept = [];
    public $projects_per_dept = [];
    public $dashboard_alerts = [];
    public $recent_activity = [];
    public $overdue_loans_details = [];
    public $max_books_count = 0;
    public $max_projects_count = 0;
    public $search_term = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->stats = $this->getStatistics();
        $this->books_per_dept = $this->getBooksPerDepartment();
        $this->projects_per_dept = $this->getProjectsPerDepartment();
        $this->dashboard_alerts = $this->getDashboardAlerts();
        $this->recent_activity = $this->getRecentDashboardActivity(5);
        $this->overdue_loans_details = $this->getOverdueLoansDetails();

        $this->max_books_count = collect($this->books_per_dept)->max('book_count') ?? 0;
        $this->max_projects_count = collect($this->projects_per_dept)->max('project_count') ?? 0;
    }

    private function getStatistics()
    {
        $total_books = Book::sum('quantity');
        $total_projects = Project::count();
        $active_loans = FacultyBorrowing::count();
        $overdue_loans = FacultyBorrowing::where('expected_return_date', '<', now())->count();
        $unavailable_books = Book::where('quantity', '<=', 0)->count();
        $total_faculty = LibraryStaff::count();

        $top_department_row = FacultyBorrowing::select('faculty_department', DB::raw('COUNT(*) as borrow_count'))
            ->whereMonth('borrow_date', now()->month)
            ->whereYear('borrow_date', now()->year)
            ->groupBy('faculty_department')
            ->orderBy('borrow_count', 'desc')
            ->first();

        $monthly_borrow_total = FacultyBorrowing::whereMonth('borrow_date', now()->month)
            ->whereYear('borrow_date', now()->year)
            ->count();

        return [
            'total_books' => $total_books,
            'total_projects' => $total_projects,
            'active_loans' => $active_loans,
            'total_faculty' => $total_faculty,
            'overdue_loans' => $overdue_loans,
            'unavailable_books' => $unavailable_books,
            'top_department' => $top_department_row?->faculty_department ?? 'لا يوجد',
            'top_department_borrow_count' => $top_department_row?->borrow_count ?? 0,
            'monthly_borrow_total' => $monthly_borrow_total
        ];
    }

    private function getBooksPerDepartment()
    {
        return Book::select(
                DB::raw('CASE WHEN books.custom_department IS NOT NULL THEN books.custom_department ELSE departments.name_ar END as department'),
                DB::raw('SUM(books.quantity) as book_count')
            )
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->groupBy('department')
            ->orderBy('book_count', 'desc')
            ->get()
            ->toArray();
    }

    private function getProjectsPerDepartment()
    {
        return Project::select('departments.name_ar as department', DB::raw('COUNT(projects.id) as project_count'))
            ->leftJoin('departments', 'projects.department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name_ar')
            ->orderBy('project_count', 'desc')
            ->get()
            ->toArray();
    }

    private function getOverdueLoansDetails()
    {
        return FacultyBorrowing::select(
                'id', 'faculty_name', 'borrow_date', 'expected_return_date',
                DB::raw('DATEDIFF(CURDATE(), expected_return_date) as days_overdue'),
                'book_title', 'book_isbn', 'faculty_department as department'
            )
            ->where('expected_return_date', '<', now())
            ->orderBy('expected_return_date', 'asc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getDashboardAlerts()
    {
        $alerts = [];

        $due_soon_count = FacultyBorrowing::whereBetween('expected_return_date', [now(), now()->addDays(3)])->count();
        if ($due_soon_count > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-clock',
                'message' => "يوجد {$due_soon_count} إعارة ستنتهي خلال 3 أيام."
            ];
        }

        $overdue_count = FacultyBorrowing::where('expected_return_date', '<', now())->count();
        if ($overdue_count > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa-triangle-exclamation',
                'message' => "يوجد {$overdue_count} إعارة متأخرة تحتاج متابعة."
            ];
        }

        $unclassified_books = Book::whereNull('department_id')
            ->where(function($q) {
                $q->whereNull('custom_department')->orWhere('custom_department', '');
            })
            ->count();
        if ($unclassified_books > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-layer-group',
                'message' => "يوجد {$unclassified_books} مادة بدون تصنيف قسم."
            ];
        }

        return $alerts;
    }

    private function getRecentDashboardActivity($limit = 5)
    {
        $activities = [];

        $books = Book::latest()->limit(5)->get()->map(fn($b) => [
            'icon' => 'fa-book',
            'text' => 'تمت إضافة مادة: ' . $b->book_title,
            'created_at' => $b->created_at
        ]);

        $projects = Project::latest()->limit(5)->get()->map(fn($p) => [
            'icon' => 'fa-folder-open',
            'text' => 'تمت إضافة مشروع: ' . $p->project_name,
            'created_at' => $p->created_at
        ]);

        $borrowings = FacultyBorrowing::latest()->limit(5)->get()->map(fn($fb) => [
            'icon' => 'fa-hand-holding',
            'text' => "تم تسجيل إعارة: {$fb->book_title} ({$fb->faculty_name})",
            'created_at' => $fb->created_at
        ]);

        return collect($books)->concat($projects)->concat($borrowings)
            ->sortByDesc('created_at')
            ->take($limit)
            ->toArray();
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->search_term) < 2) {
            return [];
        }

        $books = Book::where('book_title', 'like', '%' . $this->search_term . '%')
            ->orWhere('author', 'like', '%' . $this->search_term . '%')
            ->orWhere('isbn', 'like', '%' . $this->search_term . '%')
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'type' => 'book',
                'title' => $b->book_title,
                'subtitle' => $b->author,
                'icon' => 'fa-book',
                'route' => route('books.edit', $b->id)
            ]);

        $projects = Project::where('project_name', 'like', '%' . $this->search_term . '%')
            ->orWhere('student_name', 'like', '%' . $this->search_term . '%')
            ->orWhere('archive_number', 'like', '%' . $this->search_term . '%')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'type' => 'project',
                'title' => $p->project_name,
                'subtitle' => $p->student_name,
                'icon' => 'fa-folder-open',
                'route' => route('projects.edit', $p->id)
            ]);

        return $books->concat($projects);
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'search_results' => $this->search_results
        ])->layout('layouts.app');
    }
}

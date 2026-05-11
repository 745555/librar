<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $subjectType = '';
    public $userId = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 20;

    protected $queryString = [
        'search',
        'subjectType',
        'userId',
        'dateFrom',
        'dateTo',
        'perPage'
    ];

    public function mount()
    {
        $this->authorize('system.manage');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSubjectType()
    {
        $this->resetPage();
    }

    public function updatingUserId()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'subjectType', 'userId', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function exportCsv()
    {
        $this->authorize('system.manage');
        
        $logs = $this->getLogsQuery()->get();
        
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv .= "التاريخ,المستخدم,الإجراء,النوع,التفاصيل\n";
        
        foreach ($logs as $log) {
            $csv .= '"' . $log->created_at->format('Y-m-d H:i:s') . '",';
            $csv .= '"' . str_replace('"', '""', $log->causer?->full_name ?? 'N/A') . '",';
            $csv .= '"' . str_replace('"', '""', $log->description ?? 'N/A') . '",';
            $csv .= '"' . str_replace('"', '""', class_basename($log->subject_type) ?? 'N/A') . '",';
            
            $details = '';
            if ($log->properties) {
                $properties = $log->properties;
                $changes = null;
                
                if (is_object($properties) && method_exists($properties, 'toArray')) {
                    $changes = $properties->toArray();
                } elseif (is_array($properties)) {
                    $changes = $properties;
                } elseif (isset($properties->attributes)) {
                    $attributes = $properties->attributes;
                    if (is_object($attributes) && method_exists($attributes, 'toArray')) {
                        $changes = $attributes->toArray();
                    } elseif (is_array($attributes)) {
                        $changes = $attributes;
                    }
                }
                
                if ($changes && is_array($changes)) {
                    $details_array = [];
                    foreach ($changes as $key => $value) {
                        $details_array[] = $key . ': ' . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value);
                    }
                    $details = implode(' | ', $details_array);
                }
            }
            $csv .= '"' . str_replace('"', '""', $details) . '"' . "\n";
        }
        
        $filename = 'activity-logs-' . date('Y-m-d-H-i-s') . '.csv';
        
        return response()->streamDownload(function() use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function exportHtml()
    {
        $this->authorize('system.manage');
        
        $logs = $this->getLogsQuery()->get();
        
        $html = view('livewire.admin.activity-logs-pdf', [
            'logs' => $logs,
            'filters' => $this->getActiveFilters()
        ])->render();
        
        $filename = 'activity-logs-' . date('Y-m-d-H-i-s') . '.html';
        
        return response()->streamDownload(function() use ($html) {
            echo $html;
        }, $filename, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    private function getLogsQuery()
    {
        $query = Activity::with(['causer', 'subject'])
            ->when($this->search, function($q) {
                $q->where(function($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->search . '%')
                           ->orWhereHas('causer', function($userQuery) {
                               $userQuery->where('full_name', 'like', '%' . $this->search . '%')
                                        ->orWhere('username', 'like', '%' . $this->search . '%');
                           })
                           ->orWhere('subject_type', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->subjectType, function($q) {
                $q->where('subject_type', 'like', '%' . $this->subjectType . '%');
            })
            ->when($this->userId, function($q) {
                $q->where('causer_id', $this->userId);
            })
            ->when($this->dateFrom, function($q) {
                $q->whereDate('created_at', '>=', Carbon::parse($this->dateFrom));
            })
            ->when($this->dateTo, function($q) {
                $q->whereDate('created_at', '<=', Carbon::parse($this->dateTo));
            });

        return $query->orderBy('created_at', 'desc');
    }

    private function getActiveFilters()
    {
        $filters = [];
        if ($this->search) $filters['بحث'] = $this->search;
        if ($this->subjectType) $filters['النوع'] = $this->subjectType;
        if ($this->userId) $filters['المستخدم'] = \App\Models\LibraryStaff::find($this->userId)?->full_name;
        if ($this->dateFrom) $filters['من تاريخ'] = $this->dateFrom;
        if ($this->dateTo) $filters['إلى تاريخ'] = $this->dateTo;
        return $filters;
    }

    public function render()
    {
        $logs = $this->getLogsQuery()->paginate($this->perPage);
        
        $subjectTypes = Activity::distinct('subject_type')
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(fn($type) => class_basename($type))
            ->unique()
            ->sort()
            ->toArray();

        $users = \App\Models\LibraryStaff::orderBy('full_name')
            ->pluck('full_name', 'id')
            ->toArray();

        return view('livewire.admin.activity-logs', [
            'logs' => $logs,
            'subjectTypes' => $subjectTypes,
            'users' => $users,
            'activeFilters' => $this->getActiveFilters()
        ])->layout('layouts.app');
    }
}

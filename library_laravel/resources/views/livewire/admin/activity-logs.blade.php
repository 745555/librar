@section('title', 'سجلات الأنشطة')

<div>
    <style>
        .logs-container {
            padding: 20px;
        }
        
        .filters-section {
            background: var(--bg-off-white);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-primary);
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        
        .logs-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .logs-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .logs-table th {
            background: var(--bg-off-white);
            padding: 12px;
            text-align: right;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        .logs-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .log-description {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .log-details {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .log-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .type-book { background: #e3f2fd; color: #1976d2; }
        .type-project { background: #f3e5f5; color: #7b1fa2; }
        .type-facultyborrowing { background: #e8f5e8; color: #388e3c; }
        .type-librarystaff { background: #fff3e0; color: #f57c00; }
        
        .active-filters {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            border: 1px solid var(--border-color);
        }
        
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .logs-table {
                overflow-x: auto;
            }
            
            .export-buttons {
                flex-direction: column;
            }
        }
    </style>

    <div class="logs-container">
        <div class="page-header">
            <h3><i class="fas fa-history"></i> سجلات الأنشطة</h3>
            <span class="logo-badge"><i class="fas fa-shield-alt"></i> تتبع جميع التغييرات في النظام</span>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <h4><i class="fas fa-filter"></i> فلترة السجلات</h4>
            
            @if(!empty($activeFilters))
                <div class="active-filters">
                    <span><strong>الفلاتر النشطة:</strong></span>
                    @foreach($activeFilters as $key => $value)
                        <span class="filter-badge">
                            {{ $key }}: {{ $value }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="filter-grid">
                <div class="filter-group">
                    <label>بحث</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="ابحث في الوصف أو المستخدم...">
                </div>

                <div class="filter-group">
                    <label>نوع السجل</label>
                    <select wire:model.live.debounce.300ms="subjectType">
                        <option value="">الكل</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>المستخدم</label>
                    <select wire:model.live.debounce.300ms="userId">
                        <option value="">الكل</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>من تاريخ</label>
                    <input type="date" wire:model.live.debounce.300ms="dateFrom">
                </div>

                <div class="filter-group">
                    <label>إلى تاريخ</label>
                    <input type="date" wire:model.live.debounce.300ms="dateTo">
                </div>

                <div class="filter-group">
                    <label>عدد النتائج</label>
                    <select wire:model.live.debounce.300ms="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button wire:click="clearFilters" class="btn-secondary">
                    <i class="fas fa-times"></i> مسح الفلاتر
                </button>
                
                <div class="export-buttons">
                    <button wire:click="exportCsv" class="btn-primary">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </button>
                    <button wire:click="exportHtml" class="btn-primary">
                        <i class="fas fa-file-code"></i> تصدير HTML
                    </button>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="logs-table">
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>الإجراء</th>
                        <th>النوع</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <div class="log-details">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                {{ $log->causer?->full_name ?? 'N/A' }}
                                <div class="log-details">
                                    {{ $log->causer?->username ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="log-description">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <span class="log-type type-{{ strtolower(class_basename($log->subject_type ?? '')) }}">
                                    {{ class_basename($log->subject_type ?? 'N/A') }}
                                </span>
                                @if($log->subject_id)
                                    <div class="log-details">
                                        #{{ $log->subject_id }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($log->properties)
                                    @php
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
                                    @endphp
                                    
                                    @if($changes && is_array($changes) && !empty($changes))
                                        @foreach(array_slice($changes, 0, 3) as $key => $value)
                                            <div class="log-details">
                                                <strong>{{ $key }}:</strong> 
                                                {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}
                                            </div>
                                        @endforeach
                                        @if(count($changes) > 3)
                                            <div class="log-details">
                                                <em>... و {{ count($changes) - 3 }} حقول أخرى</em>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">لا توجد تفاصيل</span>
                                    @endif
                                @else
                                    <span class="text-muted">لا توجد تفاصيل</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--bg-beige); display: block; margin-bottom: 15px;"></i>
                                <span class="text-muted">لا توجد سجلات أنشطة حالياً</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $logs->links() }}
        </div>
    </div>
</div>

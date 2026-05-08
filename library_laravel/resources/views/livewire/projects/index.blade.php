<div>
    <div class="borrow-theme">
        <div class="card">
            <div class="page-header">
                <h3><i class="fas fa-chalkboard-user"></i> أرشيف مشاريع التخرج</h3>
                <a href="{{ route('projects.create') }}" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> إضافة مشروع جديد</a>
            </div>
            
            <div class="search-box" style="margin-bottom: 25px;">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث برقم الأرشيف، اسم المشروع، المشرف، أو الطالب..." style="padding: 15px 25px; border-radius: 50px; background: var(--bg-off-white);">
            </div>

            @if (session()->has('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>الرقم الأرشيفي</th>
                            <th>المشروع</th>
                            <th>الطالب / المشرف</th>
                            <th>الفصل / السنة</th>
                            <th>القسم</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td class="mono-id" style="font-family: 'Courier New', monospace; font-weight: bold; color: var(--primary-green);">{{ $project->archive_number }}</td>
                            <td style="max-width: 300px;">
                                <div style="font-weight: 600; color: var(--text-dark);">{{ $project->project_name }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;"><i class="fas fa-user-graduate" style="width: 20px;"></i> {{ $project->student_name }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;"><i class="fas fa-chalkboard-user" style="width: 20px;"></i> {{ $project->supervisor }}</div>
                            </td>
                            <td>
                                <span class="badge staff">{{ $project->semester }}</span>
                                <span class="badge admin">{{ $project->year }}</span>
                            </td>
                            <td>
                                <span class="badge admin" style="background: rgba(24, 95, 132, 0.1); color: var(--primary-blue);">{{ $project->department->name_ar ?? 'بدون قسم' }}</span>
                            </td>
                            <td class="action-buttons">
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn-edit"><i class="fas fa-edit"></i></a>
                                <button onclick="confirm('هل أنت متأكد من حذف هذا المشروع؟') || event.stopImmediatePropagation()" wire:click="deleteProject({{ $project->id }})" class="btn-delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin: 10px 0;"></i>
                                لا توجد مشاريع مضافة حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>

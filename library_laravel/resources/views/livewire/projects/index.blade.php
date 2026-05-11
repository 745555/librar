@section('title', 'أرشيف مشاريع التخرج')

<div>
    <div class="borrow-theme">
        <div class="card">
            <div class="page-header">
                <h3><i class="fas fa-chalkboard-user"></i> أرشيف مشاريع التخرج</h3>
                @can('projects.create')
                    <a href="{{ route('projects.create') }}" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> إضافة مشروع جديد</a>
                @endcan
            </div>
            
            <div class="search-box" style="margin-bottom: 25px;">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث برقم الأرشيف، اسم المشروع، المشرف، أو الطالب..." style="padding: 15px 25px; border-radius: 50px; background: var(--bg-off-white);">
            </div>

            {{-- Alerts handled by SweetAlert2 globally --}}
            
            <div class="table-responsive">
                <table wire:loading.remove>
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
                        <tr wire:key="project-{{ $project->id }}">
                            <td data-label="الرقم الأرشيفي" class="mono-id" style="font-family: 'Courier New', monospace; font-weight: bold; color: var(--primary-green);">{{ $project->archive_number }}</td>
                            <td data-label="المشروع" style="max-width: 300px;">
                                <div style="font-weight: 600; color: var(--text-dark);">{{ $project->project_name }}</div>
                            </td>
                            <td data-label="الطالب / المشرف">
                                <div style="font-size: 0.9rem;"><i class="fas fa-user-graduate" style="width: 20px;"></i> {{ $project->student_name }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;"><i class="fas fa-chalkboard-user" style="width: 20px;"></i> {{ $project->supervisor }}</div>
                            </td>
                            <td data-label="الفصل / السنة">
                                <span class="badge staff">{{ $project->semester }}</span>
                                <span class="badge admin">{{ $project->year }}</span>
                            </td>
                            <td data-label="القسم">
                                <span class="badge admin" style="background: rgba(24, 95, 132, 0.1); color: var(--primary-blue);">{{ $project->department->name_ar ?? 'بدون قسم' }}</span>
                            </td>
                            <td data-label="إجراءات" class="action-buttons">
                                @can('projects.edit')
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn-edit"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('projects.delete')
                                    <button type="button" onclick="confirmDelete({{ $project->id }}, (id) => @this.deleteProject(id))" class="btn-delete"><i class="fas fa-trash"></i></button>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <h4>أرشيف المشاريع فارغ</h4>
                                    <p>لم يتم العثور على مشاريع تخرج مطابقة للبحث حالياً.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Loading Skeleton -->
                <div wire:loading style="width: 100%; padding: 20px;">
                    @for($i = 0; $i < 5; $i++)
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div class="skeleton" style="height: 40px; flex: 1.5;"></div>
                            <div class="skeleton" style="height: 40px; flex: 3;"></div>
                            <div class="skeleton" style="height: 40px; flex: 2;"></div>
                            <div class="skeleton" style="height: 40px; flex: 1;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div style="margin-top: 20px;">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>

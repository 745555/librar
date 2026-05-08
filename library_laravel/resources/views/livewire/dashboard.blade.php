<div>
    <!-- Notifications -->
    <div class="notification-bell-container" wire:ignore>
        <div class="notification-bell" id="dashboardBell">
            <i class="fas fa-bell"></i>
            @if(count($dashboard_alerts) > 0)
                <span class="notification-badge">{{ count($dashboard_alerts) }}</span>
            @endif
        </div>
        
        <div class="notification-dropdown" id="notificationDropdown">
            <div class="notification-header">
                <h4><i class="fas fa-bell"></i> الإشعارات</h4>
                <button class="clear-all-btn" onclick="clearAllNotifications()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                    <i class="fas fa-trash"></i> مسح
                </button>
            </div>
            <div class="notification-list">
                @forelse($dashboard_alerts as $index => $alert)
                    <div class="notification-item {{ $alert['type'] }}" data-index="{{ $index }}">
                        <div class="notification-icon">
                            <i class="fas {{ $alert['icon'] }}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-message">{{ $alert['message'] }}</div>
                            <div class="notification-time">الآن</div>
                        </div>
                    </div>
                @empty
                    <div class="notification-item empty">
                        <div class="notification-content">
                            <div class="notification-message">لا توجد إشعارات حالياً</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-info clickable-card" onclick="window.location.href='{{ route('books.index') }}'">
            <div class="stat-info">
                <h3>إجمالي الكـتب</h3>
                <div class="stat-number">{{ number_format($stats['total_books']) }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-book"></i></div>
        </div>
        <div class="stat-card stat-card-warning clickable-card" onclick="window.location.href='{{ route('borrowings.index') }}'">
            <div class="stat-info">
                <h3>الإعارات النشطة</h3>
                <div class="stat-number">{{ $stats['active_loans'] }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-hand-holding"></i></div>
        </div>
        <div class="stat-card stat-card-danger clickable-card" onclick="showOverdueNotifications()">
            <div class="stat-info">
                <h3>الإعارات المتأخرة</h3>
                <div class="stat-number">{{ $stats['overdue_loans'] }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
        </div>
        <div class="stat-card stat-card-info clickable-card" onclick="window.location.href='{{ route('projects.index') }}'">
            <div class="stat-info">
                <h3>المشاريع</h3>
                <div class="stat-number">{{ $stats['total_projects'] }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-archive"></i></div>
        </div>
    </div>

    <!-- SMART SEARCH -->
    <div class="card" style="margin-bottom: 30px; border: 2px solid var(--bg-beige); box-shadow: var(--shadow-md); position: relative;">
        <div class="page-header" style="margin-bottom: 20px;">
            <h3><i class="fas fa-bolt" style="color: var(--primary-green);"></i> البحث الذكي السريع</h3>
            <span class="logo-badge">{{ $stats['total_books'] + $stats['total_projects'] }} عنصر متوفر</span>
        </div>
        
        <div class="search-box" style="margin-bottom: 10px; position: relative;">
            <input type="text" wire:model.live.debounce.300ms="search_term" placeholder="🔍 ابحث عن كتاب، مشروع، مؤلف، طالب..." style="padding: 18px 25px; font-size: 1.1rem; border-radius: 50px; background: var(--bg-off-white);">
            
            @if(!empty($search_results))
                <div class="search-results-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border-radius: 15px; box-shadow: var(--shadow-lg); z-index: 1000; margin-top: 10px; overflow: hidden; border: 1px solid var(--bg-beige);">
                    @foreach($search_results as $result)
                        <a href="{{ $result['route'] }}" style="display: flex; align-items: center; gap: 15px; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid var(--bg-off-white); transition: background 0.2s;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--bg-off-white); display: flex; align-items: center; justify-content: center; color: var(--primary-blue);">
                                <i class="fas {{ $result['icon'] }}"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-dark);">{{ $result['title'] }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $result['subtitle'] }}</div>
                            </div>
                            <div class="badge {{ $result['type'] == 'book' ? 'staff' : 'admin' }}" style="font-size: 0.7rem;">
                                {{ $result['type'] == 'book' ? 'كتاب' : 'مشروع' }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif(strlen($search_term) >= 2)
                <div class="search-results-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border-radius: 15px; box-shadow: var(--shadow-lg); z-index: 1000; margin-top: 10px; padding: 20px; text-align: center; color: var(--text-muted); border: 1px solid var(--bg-beige);">
                    <i class="fas fa-search" style="font-size: 1.5rem; display: block; margin-bottom: 10px;"></i>
                    لا توجد نتائج مطابقة لبحثك
                </div>
            @endif
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
            <button class="shortcut-btn" onclick="window.location.href='{{ route('books.index') }}'" style="background: var(--bg-off-white); border: 1px solid var(--bg-beige); padding: 8px 15px; border-radius: 50px; cursor: pointer; font-size: 0.9rem;">
                <i class="fas fa-book"></i> كتب المكتبة
            </button>
            <button class="shortcut-btn" onclick="window.location.href='{{ route('projects.index') }}'" style="background: var(--bg-off-white); border: 1px solid var(--bg-beige); padding: 8px 15px; border-radius: 50px; cursor: pointer; font-size: 0.9rem;">
                <i class="fas fa-folder-open"></i> مشاريع التخرج
            </button>
            <button class="shortcut-btn" onclick="window.location.href='{{ route('borrowings.index') }}'" style="background: var(--bg-off-white); border: 1px solid var(--bg-beige); padding: 8px 15px; border-radius: 50px; cursor: pointer; font-size: 0.9rem;">
                <i class="fas fa-hand-holding"></i> الإعارات
            </button>
        </div>
    </div>

    <div class="stats-two-col">
        <div class="card">
            <h3><i class="fas fa-chart-pie"></i> المواد حسب القسم</h3>
            <div class="subtle-form" style="margin-bottom: 20px;">
                <input type="text" id="dashboardDeptFilter" placeholder="🔍 تصفية حسب اسم القسم..." style="padding: 10px 15px; border-radius: 50px;">
            </div>
            <ul class="dept-list">
                @foreach($books_per_dept as $dept)
                @php
                    $dept_name = $dept['department'] ?? 'بدون قسم';
                    $book_count = (int)$dept['book_count'];
                    $books_width = $max_books_count > 0 ? max(5, (int)(($book_count / $max_books_count) * 100)) : 5;
                @endphp
                <li class="dept-item dashboard-dept-item" data-dept="{{ mb_strtolower($dept_name) }}" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span class="dept-name"><i class="fas fa-building" style="margin-left: 8px; color: var(--primary-green);"></i>{{ $dept_name }}</span>
                    <span class="badge staff">{{ $book_count }} مادة</span>
                </li>
                <div class="dept-progress" style="height: 6px; background: var(--bg-beige); border-radius: 10px; margin-bottom: 20px; overflow: hidden;">
                    <div class="dept-progress-bar" style="width: {{ $books_width }}%; height: 100%; background: var(--primary-green); border-radius: 10px;"></div>
                </div>
                @endforeach
            </ul>
        </div>
        
        <div class="card">
            <h3><i class="fas fa-chart-bar"></i> المشاريع حسب القسم</h3>
            <ul class="dept-list" style="margin-top: 45px;">
                @foreach($projects_per_dept as $dept)
                @php
                    $dept_name = $dept['department'] ?? 'بدون قسم';
                    $project_count = (int)$dept['project_count'];
                    $projects_width = $max_projects_count > 0 ? max(5, (int)(($project_count / $max_projects_count) * 100)) : 5;
                @endphp
                <li class="dept-item dashboard-dept-item" data-dept="{{ mb_strtolower($dept_name) }}" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span class="dept-name"><i class="fas fa-building" style="margin-left: 8px; color: var(--primary-blue);"></i>{{ $dept_name }}</span>
                    <span class="badge admin">{{ $project_count }} مشروع</span>
                </li>
                <div class="dept-progress" style="height: 6px; background: var(--bg-beige); border-radius: 10px; margin-bottom: 20px; overflow: hidden;">
                    <div class="dept-progress-bar" style="width: {{ $projects_width }}%; height: 100%; background: var(--primary-blue); border-radius: 10px;"></div>
                </div>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <h3><i class="fas fa-clock-rotate-left"></i> آخر النشاطات</h3>
            <span class="logo-badge"><i class="fas fa-sync"></i> تحديث تلقائي</span>
        </div>
        <ul class="recent-activity-list" style="list-style: none; padding: 0;">
            @forelse ($recent_activity as $activity)
                <li class="recent-activity-item" style="padding: 15px; border-bottom: 1px solid var(--bg-beige); display: flex; align-items: center; gap: 15px;">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--bg-off-white); display: flex; align-items: center; justify-content: center; color: var(--primary-green);">
                        <i class="fas {{ $activity['icon'] }}"></i>
                    </div>
                    <span>{{ $activity['text'] }}</span>
                </li>
            @empty
                <li style="padding: 30px; text-align: center; color: var(--text-muted);">لا يوجد نشاط حديث لعرضه حالياً.</li>
            @endforelse
        </ul>
    </div>

    @push('scripts')
    <script>
        let notifications = @json($dashboard_alerts);
        let overdueLoans = @json($overdue_loans_details);
        
        function showOverdueNotifications() {
            if (!overdueLoans || overdueLoans.length === 0) {
                alert('لا توجد إعارات متأخرة حالياً');
                return;
            }
            
            const customAlerts = overdueLoans.map((loan, index) => ({
                type: 'danger',
                icon: 'fa-triangle-exclamation',
                message: `تأخير ${loan.days_overdue} أيام: "${loan.book_title}" - ${loan.faculty_name}`,
                index: index
            }));
            
            updateNotificationDropdown(customAlerts);
            
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.getElementById('dashboardBell');
            
            if (bell && dropdown) {
                dropdown.style.display = 'block';
                bell.classList.add('active');
                bell.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function updateNotificationDropdown(alerts) {
            const dropdown = document.getElementById('notificationDropdown');
            if (!dropdown) return;
            
            const notificationList = dropdown.querySelector('.notification-list');
            if (!notificationList) return;
            
            notificationList.innerHTML = '';
            
            alerts.forEach(alert => {
                const notificationItem = document.createElement('div');
                notificationItem.className = `notification-item ${alert.type}`;
                notificationItem.innerHTML = `
                    <div class="notification-icon"><i class="fas ${alert.icon}"></i></div>
                    <div class="notification-content">
                        <div class="notification-message">${alert.message}</div>
                        <div class="notification-time">الآن</div>
                    </div>
                `;
                notificationList.appendChild(notificationItem);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('dashboardBell');
            const dropdown = document.getElementById('notificationDropdown');
            
            if (bell && dropdown) {
                bell.onclick = function() {
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                    bell.classList.toggle('active');
                };
            }
            
            const filterInput = document.getElementById('dashboardDeptFilter');
            if (filterInput) {
                filterInput.addEventListener('input', function () {
                    const term = this.value.trim().toLowerCase();
                    document.querySelectorAll('.dashboard-dept-item').forEach(item => {
                        const dept = item.getAttribute('data-dept') || '';
                        const progress = item.nextElementSibling;
                        const visible = dept.includes(term);
                        item.style.display = visible ? 'flex' : 'none';
                        if (progress && progress.classList.contains('dept-progress')) {
                            progress.style.display = visible ? 'block' : 'none';
                        }
                    });
                });
            }
        });

        function clearDashboardFilter() {
            const filterInput = document.getElementById('dashboardDeptFilter');
            if (filterInput) {
                filterInput.value = '';
                filterInput.dispatchEvent(new Event('input'));
            }
        }
    </script>
    @endpush
</div>

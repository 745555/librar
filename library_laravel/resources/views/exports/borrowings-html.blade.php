<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تصدير قائمة الإعارات</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            margin: 20px;
            direction: rtl;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f2b3d;
            padding-bottom: 20px;
        }
        .filters-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #0f2b3d;
            color: white;
            padding: 10px;
            text-align: right;
            border: 1px solid #ddd;
            font-weight: 600;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge.returned { background: #10b981; color: white; }
        .badge.borrowed { background: #3b82f6; color: white; }
        .badge.overdue { background: #ef4444; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            .filters-info { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام المكتبة - قائمة الإعارات</h1>
        <p>تاريخ التصدير: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>إجمالي الإعارات: {{ $borrowings->count() }}</p>
    </div>

    @if($search)
        <div class="filters-info">
            <h3>البحث المطبق:</h3>
            <p><strong>بحث:</strong> {{ $search }}</p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>اسم عضو هيئة التدريس</th>
                <th>القسم</th>
                <th>عنوان الكتاب</th>
                <th>تاريخ الإعارة</th>
                <th>الموعد المتوقع</th>
                <th>تاريخ الإرجاع</th>
                <th>الحالة</th>
                <th>التواصل</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $borrowing)
                <tr>
                    <td>{{ $borrowing->faculty_name }}</td>
                    <td>{{ $borrowing->faculty_department }}</td>
                    <td>{{ $borrowing->book_title }}</td>
                    <td>{{ $borrowing->borrow_date->format('Y-m-d') }}</td>
                    <td>{{ $borrowing->expected_return_date->format('Y-m-d') }}</td>
                    <td>{{ $borrowing->actual_return_date?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        @if($borrowing->actual_return_date)
                            <span class="badge returned">تم الإرجاع</span>
                        @else
                            <span class="badge {{ $borrowing->expected_return_date->isPast() ? 'overdue' : 'borrowed' }}">
                                {{ $borrowing->expected_return_date->isPast() ? 'متأخر' : 'مُعار' }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $borrowing->faculty_contact ?? '—' }}</td>
                    <td>{{ $borrowing->notes ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>نظام المكتبة © {{ date('Y') }}</p>
    </div>
</body>
</html>

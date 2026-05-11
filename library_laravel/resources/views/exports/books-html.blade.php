<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تصدير قائمة الكتب</title>
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
        .badge.available { background: #10b981; color: white; }
        .badge.unavailable { background: #ef4444; color: white; }
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
        <h1>نظام المكتبة - قائمة الكتب</h1>
        <p>تاريخ التصدير: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>إجمالي الكتب: {{ $books->count() }}</p>
    </div>

    @if(!empty($filters))
        <div class="filters-info">
            <h3>الفلاتر المطبقة:</h3>
            @foreach($filters as $key => $value)
                <p><strong>{{ $key }}:</strong> {{ $value }}</p>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>الرقم الأرشيفي (ISBN)</th>
                <th>عنوان الكتاب</th>
                <th>المؤلف</th>
                <th>الناشر</th>
                <th>سنة النشر</th>
                <th>الكمية</th>
                <th>المتاح</th>
                <th>القسم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr>
                    <td>{{ $book->isbn ?? '—' }}</td>
                    <td>{{ $book->book_title }}</td>
                    <td>{{ $book->author ?? '—' }}</td>
                    <td>{{ $book->publisher ?? '—' }}</td>
                    <td>{{ $book->publication_year ?? '—' }}</td>
                    <td>{{ $book->quantity }}</td>
                    <td>{{ $book->available_quantity }}</td>
                    <td>{{ $book->custom_department ?? $book->department->name_ar ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>نظام المكتبة © {{ date('Y') }}</p>
    </div>
</body>
</html>

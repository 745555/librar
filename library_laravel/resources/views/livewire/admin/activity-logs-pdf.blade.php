<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>سجلات الأنشطة</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            margin: 20px;
            direction: rtl;
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
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .log-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .type-book { background: #e3f2fd; color: #1976d2; }
        .type-project { background: #f3e5f5; color: #7b1fa2; }
        .type-facultyborrowing { background: #e8f5e8; color: #388e3c; }
        .type-librarystaff { background: #fff3e0; color: #f57c00; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام المكتبة - سجلات الأنشطة</h1>
        <p>تاريخ التوليد: {{ now()->format('Y-m-d H:i:s') }}</p>
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
                <th>التاريخ</th>
                <th>المستخدم</th>
                <th>الإجراء</th>
                <th>النوع</th>
                <th>التفاصيل</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->causer?->full_name ?? 'N/A' }}</td>
                    <td>{{ $log->description }}</td>
                    <td>
                        <span class="log-type type-{{ strtolower(class_basename($log->subject_type ?? '')) }}">
                            {{ class_basename($log->subject_type ?? 'N/A') }}
                        </span>
                        @if($log->subject_id)
                            <br><small>#{{ $log->subject_id }}</small>
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
                                @foreach($changes as $key => $value)
                                    <small><strong>{{ $key }}:</strong> 
                                    {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}
                                    </small><br>
                                @endforeach
                            @else
                                <small>لا توجد تفاصيل</small>
                            @endif
                        @else
                            <small>لا توجد تفاصيل</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>إجمالي السجلات: {{ $logs->count() }} | نظام المكتبة © {{ date('Y') }}</p>
    </div>
</body>
</html>

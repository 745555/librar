@extends('errors.layout')

@section('title', 'غير مصرح بالدخول')
@section('code', '403')
@section('icon')
    <i class="fas fa-user-shield"></i>
@endsection
@section('message_title', 'عذراً، لا تملك الصلاحية')
@section('message_body', 'ليس لديك الأذونات الكافية للوصول إلى هذه الصفحة. يرجى التواصل مع مدير النظام إذا كنت تعتقد أن هذا خطأ.')

@extends('errors.layout')

@section('title', 'الصفحة غير موجودة')
@section('code', '404')
@section('icon')
    <i class="fas fa-search-minus"></i>
@endsection
@section('message_title', 'عذراً، الصفحة غير موجودة')
@section('message_body', 'يبدو أن الرابط الذي تحاول الوصول إليه غير موجود أو تم نقله لمكان آخر.')

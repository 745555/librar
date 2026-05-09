@extends('errors.layout')

@section('title', 'خطأ في النظام')
@section('code', '500')
@section('icon')
    <i class="fas fa-tools"></i>
@endsection
@section('message_title', 'حدث خطأ تقني غير متوقع')
@section('message_body', 'نحن نعتذر، هناك مشكلة فنية حالياً في النظام. فريقنا يعمل على حلها في أسرع وقت ممكن.')

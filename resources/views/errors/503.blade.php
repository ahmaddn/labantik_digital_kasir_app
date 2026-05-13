@extends('errors.layout')

@section('title', __('Layanan Tidak Tersedia'))
@section('code', '503')
@section('message', $exception->getMessage() ?: __('Server kami sedang dalam pemeliharaan rutin atau mengalami beban berlebih. Silakan coba beberapa saat lagi.'))

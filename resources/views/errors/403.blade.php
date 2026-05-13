@extends('errors.layout')

@section('title', __('Akses Terlarang'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Anda tidak diizinkan untuk melihat halaman ini.'))

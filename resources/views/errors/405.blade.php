@extends('errors.layout')

@section('title', __('Metode Tidak Diizinkan'))
@section('code', '405')
@section('message', __('Permintaan Anda menggunakan metode yang tidak didukung oleh server untuk alamat ini.'))

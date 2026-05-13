@extends('errors.layout')

@section('title', __('Terlalu Banyak Permintaan'))
@section('code', '429')
@section('message', __('Server menerima terlalu banyak permintaan dari perangkat Anda. Silakan tunggu beberapa saat sebelum mencoba kembali.'))

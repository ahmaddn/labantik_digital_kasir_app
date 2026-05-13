@extends('errors.layout')

@section('title', __('Kesalahan Server'))
@section('code', '500')
@section('message', $exception->getMessage() ?: __('Terjadi kesalahan internal pada server kami. Tim teknis sedang berupaya memperbaikinya segera.'))

@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('page-title', 'Tambah Pelanggan')
@section('eyebrow', 'Modul Pelanggan')

@section('content')
    @include('customers._form', [
        'action' => route('pelanggan.store'),
        'method' => 'POST',
    ])
@endsection

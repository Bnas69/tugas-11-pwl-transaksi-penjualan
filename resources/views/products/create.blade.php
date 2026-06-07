@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')
@section('eyebrow', 'Modul Produk')

@section('content')
    @include('products._form', [
        'action' => route('produk.store'),
        'method' => 'POST',
    ])
@endsection

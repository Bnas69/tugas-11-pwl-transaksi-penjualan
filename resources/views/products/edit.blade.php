@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('eyebrow', $product->sku)

@section('content')
    @include('products._form', [
        'action' => route('produk.update', $product),
        'method' => 'PUT',
    ])
@endsection

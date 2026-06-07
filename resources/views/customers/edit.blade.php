@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('page-title', 'Edit Pelanggan')
@section('eyebrow', $customer->name)

@section('content')
    @include('customers._form', [
        'action' => route('pelanggan.update', $customer),
        'method' => 'PUT',
    ])
@endsection

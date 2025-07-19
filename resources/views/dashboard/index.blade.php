@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (Auth::user()->role == 'admin')
        @include('dashboard.admin')
    @endif
    @if (Auth::user()->role == 'juri')
        {{-- @include('dashboard.admin') --}}
    @endif
    @if (Auth::user()->role == 'anggota')
        {{-- @include('dashboard.admin') --}}
    @endif
@endsection

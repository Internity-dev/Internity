@extends('layouts.app')

<div class="content">
    {{-- Sidebar Ditaro disini --}}
    <x-sidebar />
    {{-- Content Ditaro disini --}}

    @if (session('success'))
    <x-alert type="alert-success" message="{{ session('success') }}" />
    @elseif (session('error'))
    <x-alert type="alert-danger" message="{{ session('error') }}" />
    @endif


    {{-- dashboard content --}}
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <x-navbar :user="$user" />
            @yield('dashboard-content')
        </div>
    </main>
    {{-- Dashboard content --}}
</div>
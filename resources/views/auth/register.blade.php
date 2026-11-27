@extends('layouts.guest')

@section('title', 'Sign up')

@section('content')
    <h1 class="mb-6 text-center text-2xl font-bold">Buat akun</h1>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-semibold">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white placeholder:text-text-secondary focus:border-accent focus:outline-none">
            @error('name')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white placeholder:text-text-secondary focus:border-accent focus:outline-none">
            @error('email')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="text-sm font-semibold">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white focus:border-accent focus:outline-none">
            @error('password')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password_confirmation" class="text-sm font-semibold">Ulangi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white focus:border-accent focus:outline-none">
        </div>

        <x-button type="submit" class="mt-2 w-full">Daftar</x-button>
    </form>

    <p class="mt-6 text-center text-sm text-text-secondary">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-white hover:underline">Log in</a>
    </p>
@endsection

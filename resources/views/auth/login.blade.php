@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
    <h1 class="mb-6 text-center text-2xl font-bold">Log in</h1>

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white placeholder:text-text-secondary focus:border-accent focus:outline-none">
            @error('email')
                <p class="text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="text-sm font-semibold">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="h-10 w-full min-w-0 rounded-md border border-border bg-card px-3.5 text-sm text-white focus:border-accent focus:outline-none">
        </div>

        <label class="flex items-center gap-2 text-sm text-text-secondary">
            <input type="checkbox" name="remember" value="1" class="size-4 accent-accent">
            Ingat saya
        </label>

        <x-button type="submit" class="mt-2 w-full">Log in</x-button>
    </form>

    <p class="mt-6 text-center text-sm text-text-secondary">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-white hover:underline">Daftar</a>
    </p>
@endsection

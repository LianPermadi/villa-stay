@extends("layouts.app")

@section("title", "Masuk - VilaStay")

@section("content")
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center">
                <svg class="w-16 h-16 text-primary" viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L4 12V28H10V20H22V28H28V12L16 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="mt-6 font-display text-3xl font-bold text-primary">Masuk ke Akun Anda</h2>
            <p class="mt-2 text-sm text-gray-600">Pengalaman menginap premium menanti Anda</p>
        </div>
        
        @if(session("error"))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session("error") }}
        </div>
        @endif
        
        <form class="mt-8 space-y-6" action="{{ route("login") }}" method="POST">
            @csrf
            
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" required 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Email">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Password">
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                </div>
            </div>
            
            <div>
                <button type="submit" class="btn-primary w-full justify-center">
                    Masuk
                </button>
            </div>
        </form>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">Belum punya akun? 
                <a href="{{ route("register") }}" class="font-medium text-primary hover:text-primary-dark">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection

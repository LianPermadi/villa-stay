@extends("layouts.app")

@section("title", "Daftar Akun - VilaStay")

@section("content")
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center">
                <svg class="w-16 h-16 text-primary" viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L4 12V28H10V20H22V28H28V12L16 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 class="mt-6 font-display text-3xl font-bold text-primary">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-gray-600">Mulai pengalaman menginap premium Anda</p>
        </div>
        
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form class="mt-8 space-y-6" action="{{ route("register") }}" method="POST">
            @csrf
            
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="sr-only">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Nama Lengkap" value="{{ old("name") }}">
                </div>
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" required 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Email" value="{{ old("email") }}">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required minlength="8"
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Password (min 8 karakter)">
                </div>
                <div>
                    <label for="password-confirm" class="sr-only">Konfirmasi Password</label>
                    <input id="password-confirm" name="password_confirmation" type="password" required
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Konfirmasi Password">
                </div>
                <div>
                    <label for="phone" class="sr-only">No. HP</label>
                    <input id="phone" name="phone" type="tel" 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="No. HP (opsional)" value="{{ old("phone") }}">
                </div>
                <div>
                    <label for="address" class="sr-only">Alamat</label>
                    <textarea id="address" name="address" rows="2" 
                        class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Alamat (opsional)">{{ old("address") }}</textarea>
                </div>
            </div>
            
            <div>
                <button type="submit" class="btn-primary w-full justify-center">
                    Daftar
                </button>
            </div>
        </form>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">Sudah punya akun? 
                <a href="{{ route("login") }}" class="font-medium text-primary hover:text-primary-dark">Masuk sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection

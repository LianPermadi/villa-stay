@extends("layouts.app")

@section("title", "Riwayat Pemesanan - Villa-Sina")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="font-display text-4xl font-bold text-primary mb-4">Riwayat Pemesanan</h1>
            <p class="text-gray-600">Kelola pemesanan yang masih proses pembayaran dan riwayat yang sudah selesai</p>
        </div>

        @if(session("success"))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session("success") }}
        </div>
        @endif

        @if($approvedBookings->isEmpty() && $unapprovedBookings->isEmpty() && $cancelledBookings->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada pemesanan</h3>
            <p class="text-gray-500 mb-6">Mulai petualangan Anda dengan memesan villa impian</p>
            <a href="{{ route('villas.index') }}" class="btn-primary inline-flex items-center gap-2">
                Cari Villa
            </a>
        </div>
        @else
        <div class="space-y-8">
            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="font-display text-2xl font-bold text-primary">Proses Pelunasan / Belum Selesai</h2>
                    <p class="text-sm text-gray-500 mt-1">Pemesanan yang belum lunas, termasuk DP yang sudah approve dan masih menunggu pelunasan</p>
                </div>
                @if($unapprovedBookings->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-500">Tidak ada pemesanan yang masih proses pelunasan.</div>
                @else
                    @include('frontend.bookings.partials.booking-table', ['bookings' => $unapprovedBookings, 'showStatus' => true])
                @endif
            </section>

            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h2 class="font-display text-2xl font-bold text-primary">Selesai</h2>
                            <p class="text-sm text-gray-500 mt-1">Riwayat pemesanan yang sudah lunas atau pengembaliannya sudah selesai diproses</p>
                        </div>
                        <form method="GET" action="{{ route('bookings.index') }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                            <div>
                                <label for="approved_from" class="block text-xs font-semibold text-gray-600 mb-1">Dari tanggal</label>
                                <input type="date" id="approved_from" name="approved_from" value="{{ $approvedFrom }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="approved_to" class="block text-xs font-semibold text-gray-600 mb-1">Sampai tanggal</label>
                                <input type="date" id="approved_to" name="approved_to" value="{{ $approvedTo }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <button type="submit" class="btn-primary justify-center self-end px-5 py-2">
                                Filter
                            </button>
                            @if(request('approved_from') || request('approved_to'))
                                <a href="{{ route('bookings.index') }}" class="btn-secondary text-center self-end px-5 py-2">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
                @if($approvedBookings->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-500">Tidak ada riwayat selesai pada rentang tanggal ini.</div>
                @else
                    @include('frontend.bookings.partials.booking-table', ['bookings' => $approvedBookings, 'showStatus' => true])
                @endif
            </section>

            <section class="bg-white rounded-2xl shadow-lg overflow-hidden border border-red-100">
                <div class="px-6 py-5 border-b border-red-100 bg-red-50">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h2 class="font-display text-2xl font-bold text-red-700">Pemesanan Dibatalkan</h2>
                            <p class="text-sm text-red-600 mt-1">Pemesanan yang dicancel tetap tersimpan sebagai riwayat di bagian paling bawah.</p>
                        </div>
                        <form method="GET" action="{{ route('bookings.index') }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                            <input type="hidden" name="approved_from" value="{{ request('approved_from', $approvedFrom) }}">
                            <input type="hidden" name="approved_to" value="{{ request('approved_to', $approvedTo) }}">
                            <div>
                                <label for="cancelled_from" class="block text-xs font-semibold text-red-700 mb-1">Dari tanggal</label>
                                <input type="date" id="cancelled_from" name="cancelled_from" value="{{ $cancelledFrom }}" class="w-full px-3 py-2 rounded-lg border border-red-200 focus:ring-2 focus:ring-red-400 focus:border-transparent">
                            </div>
                            <div>
                                <label for="cancelled_to" class="block text-xs font-semibold text-red-700 mb-1">Sampai tanggal</label>
                                <input type="date" id="cancelled_to" name="cancelled_to" value="{{ $cancelledTo }}" class="w-full px-3 py-2 rounded-lg border border-red-200 focus:ring-2 focus:ring-red-400 focus:border-transparent">
                            </div>
                            <button type="submit" class="self-end px-5 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                                Filter
                            </button>
                            @if(request('cancelled_from') || request('cancelled_to'))
                                <a href="{{ route('bookings.index', request()->except(['cancelled_from', 'cancelled_to'])) }}" class="btn-secondary text-center self-end px-5 py-2">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
                @if($cancelledBookings->isEmpty())
                    <div class="px-6 py-10 text-center text-gray-500">Tidak ada pemesanan batal pada rentang tanggal ini.</div>
                @else
                    @include('frontend.bookings.partials.booking-table', ['bookings' => $cancelledBookings, 'showStatus' => true])
                @endif
            </section>
        </div>
        @endif
    </div>
</div>
@endsection

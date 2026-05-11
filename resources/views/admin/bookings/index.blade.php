@extends("layouts.app")

@section("title", "Daftar Booking - Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-display text-3xl font-bold text-primary">Daftar Booking</h1>
            <p class="text-gray-600">Pantau booking yang masih proses pembayaran dan riwayat booking yang sudah lunas</p>
        </div>

        <div class="space-y-8">
            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="font-display text-2xl font-bold text-primary">Proses Pelunasan / Belum Selesai</h2>
                    <p class="text-sm text-gray-500 mt-1">Booking yang belum lunas, termasuk DP yang sudah approve dan masih menunggu pelunasan</p>
                </div>

                @if($unapprovedBookings->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">Tidak ada booking yang masih proses pelunasan.</div>
                @else
                    @include('admin.bookings.partials.booking-table', ['bookings' => $unapprovedBookings])
                @endif
            </section>

            <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h2 class="font-display text-2xl font-bold text-primary">Selesai Approve / Lunas</h2>
                            <p class="text-sm text-gray-500 mt-1">Riwayat booking yang seluruh pembayarannya sudah diverifikasi</p>
                        </div>

                        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                            <div>
                                <label for="approved_from" class="block text-xs font-semibold text-gray-600 mb-1">Dari tanggal</label>
                                <input type="date" id="approved_from" name="approved_from" value="{{ $approvedFrom }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="approved_to" class="block text-xs font-semibold text-gray-600 mb-1">Sampai tanggal</label>
                                <input type="date" id="approved_to" name="approved_to" value="{{ $approvedTo }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <button type="submit" class="btn-primary justify-center self-end px-5 py-2">Filter</button>
                            @if(request('approved_from') || request('approved_to'))
                                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary text-center self-end px-5 py-2">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>

                @if($approvedBookings->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">Tidak ada riwayat lunas pada rentang tanggal ini.</div>
                @else
                    @include('admin.bookings.partials.booking-table', ['bookings' => $approvedBookings])
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

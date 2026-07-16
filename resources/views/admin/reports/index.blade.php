@extends('layouts.app')

@section('title', 'Laporan Keuangan - Villa-Sina')

@section('content')
<div class="min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Administrasi</p>
                <h1 class="font-display text-4xl font-bold text-primary">Laporan Keuangan</h1>
                <p class="mt-2 text-gray-600">Ringkasan pembayaran terverifikasi dan refund berdasarkan mata uang.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Dashboard</a>
                <a href="{{ route('admin.reports.export', $filters) }}" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M5 20h14"/></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.reports') }}" class="bg-white rounded-2xl shadow-lg p-6 mb-8 grid md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Uang</label>
                <select name="currency" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                    @foreach(\App\Support\Currency::options() as $code => $label)
                    <option value="{{ $code }}" {{ $filters['currency'] === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary justify-center">Terapkan Filter</button>
        </form>

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-green-500"><p class="text-sm font-semibold text-gray-500">Pemasukan Kotor</p><p class="text-2xl font-bold text-primary mt-2">{{ \App\Support\Currency::format($grossIncome, $filters['currency']) }}</p></div>
            <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-red-400"><p class="text-sm font-semibold text-gray-500">Pengeluaran Refund</p><p class="text-2xl font-bold text-red-600 mt-2">{{ \App\Support\Currency::format($refundExpense, $filters['currency']) }}</p></div>
            <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-accent"><p class="text-sm font-semibold text-gray-500">Pendapatan Bersih</p><p class="text-2xl font-bold text-primary mt-2">{{ \App\Support\Currency::format($netIncome, $filters['currency']) }}</p></div>
            <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-blue-400"><p class="text-sm font-semibold text-gray-500">Jumlah Transaksi</p><p class="text-2xl font-bold text-primary mt-2">{{ $transactionCount }}</p></div>
        </div>

        <div class="grid xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b"><h2 class="font-display text-2xl font-bold text-primary">Detail Transaksi</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-primary text-white"><tr><th class="text-left px-5 py-3">Tanggal</th><th class="text-left px-5 py-3">Booking</th><th class="text-left px-5 py-3">Villa / Pelanggan</th><th class="text-left px-5 py-3">Jenis</th><th class="text-right px-5 py-3">Jumlah</th></tr></thead>
                        <tbody class="divide-y">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">{{ $payment->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-4"><a class="font-semibold text-primary hover:underline" href="{{ route('admin.bookings.show', $payment->booking) }}">#{{ $payment->booking_id }}</a></td>
                                <td class="px-5 py-4"><p class="font-semibold">{{ $payment->booking->villa?->name ?? '-' }}</p><p class="text-sm text-gray-500">{{ $payment->booking->user?->name ?? '-' }}</p></td>
                                <td class="px-5 py-4"><span class="badge {{ $payment->payment_type === 'refund' ? 'badge-cancelled' : 'badge-completed' }}">{{ $payment->payment_type === 'refund' ? 'Refund' : str_replace('_', ' ', $payment->payment_type) }}</span></td>
                                <td class="px-5 py-4 text-right font-bold {{ $payment->payment_type === 'refund' ? 'text-red-600' : 'text-primary' }}">{{ $payment->payment_type === 'refund' ? '-' : '' }}{{ $payment->formatted_amount }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-500">Belum ada transaksi terverifikasi pada periode dan mata uang ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-5">{{ $payments->links() }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 h-fit">
                <h2 class="font-display text-2xl font-bold text-primary mb-5">Ringkasan Bulanan</h2>
                <div class="space-y-4">
                    @forelse($monthly as $item)
                    <div class="rounded-xl border p-4">
                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::createFromFormat('Y-m', $item['period'])->translatedFormat('F Y') }}</p>
                        <div class="mt-2 space-y-1 text-sm">
                            <p class="flex justify-between"><span>Pemasukan</span><span class="font-semibold">{{ \App\Support\Currency::format($item['income'], $filters['currency']) }}</span></p>
                            <p class="flex justify-between"><span>Refund</span><span class="font-semibold text-red-600">{{ \App\Support\Currency::format($item['expense'], $filters['currency']) }}</span></p>
                            <p class="flex justify-between border-t pt-1"><span>Bersih</span><span class="font-bold text-primary">{{ \App\Support\Currency::format($item['net'], $filters['currency']) }}</span></p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-8">Belum ada ringkasan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

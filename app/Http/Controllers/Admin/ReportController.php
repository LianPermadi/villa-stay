<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\BookingReportExporter;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$filters, $from, $to] = $this->filters($request);
        $query = $this->paymentQuery($filters, $from, $to);

        $grossIncome = (clone $query)->where('payment_type', '!=', 'refund')->sum('amount');
        $refundExpense = abs((float) (clone $query)->where('payment_type', 'refund')->sum('amount'));
        $transactionCount = (clone $query)->count();
        $payments = (clone $query)->latest()->paginate(20)->withQueryString();

        $monthly = (clone $query)->get()->groupBy(fn (Payment $payment) => $payment->created_at->format('Y-m'))
            ->map(function ($items, $period) {
                $income = $items->where('payment_type', '!=', 'refund')->sum(fn (Payment $payment) => abs((float) $payment->amount));
                $expense = $items->where('payment_type', 'refund')->sum(fn (Payment $payment) => abs((float) $payment->amount));

                return compact('period', 'income', 'expense') + ['net' => $income - $expense];
            })->sortKeys()->values();

        return view('admin.reports.index', [
            'payments' => $payments,
            'monthly' => $monthly,
            'grossIncome' => (float) $grossIncome,
            'refundExpense' => $refundExpense,
            'netIncome' => (float) $grossIncome - $refundExpense,
            'transactionCount' => $transactionCount,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request, BookingReportExporter $exporter)
    {
        [$filters, $from, $to] = $this->filters($request);
        $payments = $this->paymentQuery($filters, $from, $to)->orderBy('created_at')->get();
        $filename = 'laporan-keuangan-'.$filters['currency'].'-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx';
        $path = storage_path('app/reports/'.uniqid('laporan-', true).'.xlsx');

        $exporter->export($payments, $filters['currency'], $from, $to, $path);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'currency' => ['nullable', Rule::in(Currency::codes())],
        ]);

        $from = Carbon::parse($validated['date_from'] ?? now()->startOfYear()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['date_to'] ?? now()->toDateString())->endOfDay();
        $filters = [
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
            'currency' => $validated['currency'] ?? Currency::DEFAULT,
        ];

        return [$filters, $from, $to];
    }

    private function paymentQuery(array $filters, Carbon $from, Carbon $to): Builder
    {
        return Payment::query()
            ->with(['booking.villa', 'booking.user'])
            ->where('status', 'verified')
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('booking', fn (Builder $query) => $query->where('currency', $filters['currency']));
    }
}

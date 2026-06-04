<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Throwable;

class DiagnosticsController extends Controller
{
    public function __invoke()
    {
        $database = [
            'ok' => false,
            'connection' => config('database.default'),
        ];

        try {
            DB::select('select 1');
            $database['ok'] = true;
        } catch (Throwable $exception) {
            $database['error'] = $exception->getMessage();
        }

        return response()->json([
            'app' => config('app.name'),
            'env' => app()->environment(),
            'debug' => config('app.debug'),
            'app_key_set' => filled(config('app.key')),
            'database' => $database,
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Services\PengaturanService;

class ApplyUserSettings
{
    protected PengaturanService $pengaturanService;

    public function __construct(PengaturanService $pengaturanService)
    {
        $this->pengaturanService = $pengaturanService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $settings = $this->pengaturanService->ambilSemua($userId);

            // Share settings to all views
            View::share('userSettings', $settings);

            // Convenience variables for common checks
            View::share('userTheme', $settings['tema'] ?? 'light');
            
            // Generate currency symbol based on user's currency
            $currencyCode = Auth::user()->mata_uang ?? 'IDR';
            $symbols = [
                'IDR' => 'Rp', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 
                'JPY' => '¥', 'MYR' => 'RM', 'SGD' => 'S$', 'AUD' => 'A$', 
                'SAR' => 'ر.س', 'KRW' => '₩'
            ];
            View::share('currencySymbol', $symbols[$currencyCode] ?? 'Rp');

            View::share('compactMode', ($settings['compact_mode'] ?? '0') === '1');
            View::share('animasiAktif', ($settings['animasi_aktif'] ?? '1') === '1');
            View::share('blurSaldo', ($settings['blur_saldo'] ?? '0') === '1');
            View::share('modePrivasi', ($settings['mode_privasi'] ?? '0') === '1');
            View::share('sembunyikanSaldo', ($settings['sembunyikan_saldo'] ?? '0') === '1');

            // Widget visibility
            View::share('widgetVisibility', [
                'heatmap'   => ($settings['tampil_heatmap'] ?? '1') === '1',
                'streak'    => ($settings['tampil_streak'] ?? '1') === '1',
                'target'    => ($settings['tampil_target'] ?? '1') === '1',
                'riwayat'   => ($settings['tampil_riwayat'] ?? '1') === '1',
                'statistik' => ($settings['tampil_statistik'] ?? '1') === '1',
            ]);
            
            // Generate passive notifications (once a day per user)
            // Fire and forget, don't block request
            try {
                app(\App\Services\NotifikasiService::class)->generatePassiveNotifications(Auth::user());
            } catch (\Exception $e) {
                // Ignore errors
            }

            // Fetch unread notifications
            $unreadNotifs = \App\Models\Notifikasi::where('pengguna_id', $userId)
                ->belumDibaca()
                ->latest()
                ->take(5)
                ->get();
            $unreadCount = \App\Models\Notifikasi::where('pengguna_id', $userId)
                ->belumDibaca()
                ->count();

            View::share('unreadNotifs', $unreadNotifs);
            View::share('unreadCount', $unreadCount);
        } else {
            // Defaults for guests
            View::share('userSettings', []);
            View::share('userTheme', 'light');
            View::share('currencySymbol', 'Rp');
            View::share('compactMode', false);
            View::share('animasiAktif', true);
            View::share('blurSaldo', false);
            View::share('modePrivasi', false);
            View::share('sembunyikanSaldo', false);
            View::share('widgetVisibility', [
                'heatmap' => true, 'streak' => true, 'target' => true,
                'riwayat' => true, 'statistik' => true,
            ]);
        }

        return $next($request);
    }
}

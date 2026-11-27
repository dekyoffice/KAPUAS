<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard.
     */
    public function index(Request $request): View
    {
        $selectedYear = (int) $request->input('tahun', date('Y'));

        // Ambil daftar tahun unik yang ada di data arsip untuk dropdown filter
        $availableYears = Arsip::selectRaw('YEAR(tgl_upload) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (! in_array((int) date('Y'), $availableYears)) {
            array_unshift($availableYears, (int) date('Y'));
            rsort($availableYears);
        }

        // Summary Statistics
        $totalArsip = Arsip::count();
        $totalKategori = Kategori::count();
        $totalUser = User::count();
        $arsipBulanIni = Arsip::whereMonth('tgl_upload', date('m'))
            ->whereYear('tgl_upload', date('Y'))
            ->count();

        // Proporsi Status Kelengkapan Berkas Fisik vs Digital
        $countFisikDigital = Arsip::whereNotNull('file_path')->where('file_path', '!=', '')
            ->whereNotNull('google_drive_url')->where('google_drive_url', '!=', '')
            ->count();
        $countFisik = Arsip::whereNotNull('file_path')->where('file_path', '!=', '')
            ->where(function ($q) {
                $q->whereNull('google_drive_url')->orWhere('google_drive_url', '');
            })->count();
        $countDigital = Arsip::whereNotNull('google_drive_url')->where('google_drive_url', '!=', '')
            ->where(function ($q) {
                $q->whereNull('file_path')->orWhere('file_path', '');
            })->count();
        $countTercatat = Arsip::where(function ($q) {
            $q->whereNull('file_path')->orWhere('file_path', '');
        })->where(function ($q) {
            $q->whereNull('google_drive_url')->orWhere('google_drive_url', '');
        })->count();

        $statusStats = [
            'fisik_digital' => $countFisikDigital,
            'fisik' => $countFisik,
            'digital' => $countDigital,
            'tercatat' => $countTercatat,
            'total' => $totalArsip,
        ];

        // 1. Data Grafik Bulanan (12 Bulan untuk tahun terpilih)
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $monthlyCounts = Arsip::selectRaw('MONTH(tgl_upload) as month, COUNT(*) as total')
            ->whereYear('tgl_upload', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartMonthlyLabels = [];
        $chartMonthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartMonthlyLabels[] = $monthNames[$m];
            $chartMonthlyData[] = $monthlyCounts[$m] ?? 0;
        }

        // 2. Data Grafik Kategori (Donut)
        $kategoriStats = Kategori::withCount('arsip')
            ->orderBy('arsip_count', 'desc')
            ->get();

        $chartCategoryLabels = $kategoriStats->pluck('nama_kategori')->toArray();
        $chartCategoryData = $kategoriStats->pluck('arsip_count')->toArray();

        // 3. Tabel 5 Arsip Terbaru
        $recentArsips = Arsip::with(['user', 'kategori'])
            ->accessibleBy()
            ->latest('tgl_upload')
            ->latest('id_arsip')
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'totalArsip' => $totalArsip,
            'totalKategori' => $totalKategori,
            'totalUser' => $totalUser,
            'arsipBulanIni' => $arsipBulanIni,
            'statusStats' => $statusStats,
            'chartMonthlyLabels' => $chartMonthlyLabels,
            'chartMonthlyData' => $chartMonthlyData,
            'chartCategoryLabels' => $chartCategoryLabels,
            'chartCategoryData' => $chartCategoryData,
            'recentArsips' => $recentArsips,
        ]);
    }
}

@extends('layouts.app')

@section('title', 'Dashboard Statistik')
@section('header-title', 'Dashboard Arsip')
@section('header-subtitle', 'Ringkasan data, analitik statistik, dan dokumen terbaru')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner with Year Filter -->
    <div class="rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative overflow-hidden" style="background: linear-gradient(135deg, #003CAA 0%, #0050C8 55%, #3296FA 100%)">
        <div class="absolute -right-10 -top-10 w-56 h-56 bg-white/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 bottom-0 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 text-white/90 border border-white/20 mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-light animate-pulse"></span>
                Sistem Aktif &amp; Terintegrasi
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->nama_user }}!
            </h2>
            <p class="text-white/70 text-xs sm:text-sm mt-1 max-w-xl">
                Anda masuk dengan peran <strong class="text-white uppercase">{{ auth()->user()->role }}</strong>. Kelola dokumen arsip digital instansi dengan cepat, akurat, dan terstruktur.
            </p>
        </div>

        <!-- Year Filter Selector -->
        <div class="relative z-10 bg-white/12 backdrop-blur-md p-3 rounded-xl border border-white/20 flex items-center gap-3 shrink-0">
            <div class="text-xs font-semibold text-white/80 whitespace-nowrap flex items-center gap-1.5">
                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Filter Tahun:</span>
            </div>
            <form action="{{ route('dashboard') }}" method="GET">
                <select name="tahun" onchange="this.form.submit()" class="bg-navy/70 text-white text-xs font-bold rounded-lg border border-white/25 px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-accent-light cursor-pointer">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Arsip -->
        <div class="bg-white rounded-2xl p-5 border border-line shadow-sm flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Total Arsip</p>
                <h3 class="text-2xl font-extrabold text-ink mt-1">{{ number_format($totalArsip) }}</h3>
                <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Dokumen tersimpan</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(0,60,170,0.08); color: #003CAA">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
            </div>
        </div>

        <!-- Arsip Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-line shadow-sm flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Bulan Ini</p>
                <h3 class="text-2xl font-extrabold mt-1" style="color: #0050C8">{{ number_format($arsipBulanIni) }}</h3>
                <p class="text-[11px] text-ink-muted font-medium mt-1">
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(0,80,200,0.08); color: #0050C8">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Total Kategori -->
        <div class="bg-white rounded-2xl p-5 border border-line shadow-sm flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Kategori Dokumen</p>
                <h3 class="text-2xl font-extrabold text-ink mt-1">{{ number_format($totalKategori) }}</h3>
                <p class="text-[11px] text-ink-muted font-medium mt-1">
                    Klasifikasi arsip aktif
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(50,150,250,0.1); color: #3296FA">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
        </div>

        <!-- Total User -->
        <div class="bg-white rounded-2xl p-5 border border-line shadow-sm flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <p class="text-xs font-bold text-ink-muted uppercase tracking-wider">Pengguna Sistem</p>
                <h3 class="text-2xl font-extrabold text-ink mt-1">{{ number_format($totalUser) }}</h3>
                <p class="text-[11px] text-ink-muted font-medium mt-1">
                    Admin, Operator &amp; Viewer
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(210,230,250,0.6); color: #0046B4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Status Kelengkapan Berkas Progress Bar & Summary Card -->
    @php
        $tot = max(1, $statusStats['total'] ?? 0);
        $pctFisikDigital = round((($statusStats['fisik_digital'] ?? 0) / $tot) * 100, 1);
        $pctFisik = round((($statusStats['fisik'] ?? 0) / $tot) * 100, 1);
        $pctDigital = round((($statusStats['digital'] ?? 0) / $tot) * 100, 1);
        $pctTercatat = round((($statusStats['tercatat'] ?? 0) / $tot) * 100, 1);
    @endphp
    <div class="bg-white rounded-2xl p-5 border border-line shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Status Kelengkapan Berkas Arsip</span>
                </h3>
                <p class="text-xs text-slate-500">Distribusi proporsi kepemilikan dokumen fisik dan tautan digital di seluruh sistem</p>
            </div>
            <a href="{{ route('arsip.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                <span>Buka Data Arsip</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <!-- Stacked Progress Bar -->
        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
            <div style="width: {{ $pctFisikDigital }}%" title="Fisik & Digital: {{ $statusStats['fisik_digital'] }} ({{ $pctFisikDigital }}%)" class="bg-emerald-500 transition-all duration-500"></div>
            <div style="width: {{ $pctFisik }}%" title="Fisik Saja: {{ $statusStats['fisik'] }} ({{ $pctFisik }}%)" class="bg-blue-500 transition-all duration-500"></div>
            <div style="width: {{ $pctDigital }}%" title="Digital Saja: {{ $statusStats['digital'] }} ({{ $pctDigital }}%)" class="bg-purple-500 transition-all duration-500"></div>
            <div style="width: {{ $pctTercatat }}%" title="Tercatat Saja: {{ $statusStats['tercatat'] }} ({{ $pctTercatat }}%)" class="bg-amber-400 transition-all duration-500"></div>
        </div>

        <!-- Status Pill Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
            <a href="{{ route('arsip.index', ['status' => 'fisik_digital']) }}" class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/60 hover:bg-emerald-100/70 transition-all group">
                <div class="flex items-center gap-1.5 text-emerald-800 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Fisik & Digital</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-lg font-black text-emerald-900">{{ number_format($statusStats['fisik_digital']) }}</span>
                    <span class="text-[11px] font-semibold text-emerald-700">{{ $pctFisikDigital }}%</span>
                </div>
            </a>

            <a href="{{ route('arsip.index', ['status' => 'fisik']) }}" class="p-3 rounded-xl bg-blue-50/70 border border-blue-200/60 hover:bg-blue-100/70 transition-all group">
                <div class="flex items-center gap-1.5 text-blue-800 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Fisik Saja</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-lg font-black text-blue-900">{{ number_format($statusStats['fisik']) }}</span>
                    <span class="text-[11px] font-semibold text-blue-700">{{ $pctFisik }}%</span>
                </div>
            </a>

            <a href="{{ route('arsip.index', ['status' => 'digital']) }}" class="p-3 rounded-xl bg-purple-50/70 border border-purple-200/60 hover:bg-purple-100/70 transition-all group">
                <div class="flex items-center gap-1.5 text-purple-800 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <span>Digital Saja</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-lg font-black text-purple-900">{{ number_format($statusStats['digital']) }}</span>
                    <span class="text-[11px] font-semibold text-purple-700">{{ $pctDigital }}%</span>
                </div>
            </a>

            <a href="{{ route('arsip.index', ['status' => 'tercatat']) }}" class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 hover:bg-amber-100/70 transition-all group">
                <div class="flex items-center gap-1.5 text-amber-800 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <span>Tercatat Saja</span>
                </div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-lg font-black text-amber-900">{{ number_format($statusStats['tercatat']) }}</span>
                    <span class="text-[11px] font-semibold text-amber-700">{{ $pctTercatat }}%</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    @if($totalArsip == 0)
    <!-- Empty State if no archives exist -->
    <div class="bg-white rounded-2xl p-12 border border-line text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: rgba(0,80,200,0.08); color: #0050C8">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-ink">Data Arsip Belum Tersedia</h3>
        <p class="text-sm text-ink-muted mt-1 max-w-md mx-auto">
            Sistem belum memiliki dokumen arsip yang diunggah. Mulai tambahkan arsip baru untuk melihat grafik statistik.
        </p>
        @if(auth()->user()->canManageArsip())
        <div class="mt-6">
            <a href="{{ route('arsip.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-md transition-all hover:-translate-y-0.5" style="background: linear-gradient(135deg, #003CAA, #0050C8)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Unggah Arsip Pertama</span>
            </a>
        </div>
        @endif
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Trend Bar Chart (2 cols) -->
        <div class="bg-white rounded-2xl p-6 border border-line shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-ink">Aktivitas Pengarsipan Bulanan</h3>
                    <p class="text-xs text-ink-muted">Distribusi jumlah dokumen yang diunggah sepanjang tahun {{ $selectedYear }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border" style="background: rgba(0,80,200,0.07); color: #0050C8; border-color: rgba(0,80,200,0.2)">
                    Tahun {{ $selectedYear }}
                </span>
            </div>
            <div class="h-72">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Category Donut Chart (1 col) -->
        <div class="bg-white rounded-2xl p-6 border border-line shadow-sm">
            <div class="mb-4">
                <h3 class="text-base font-bold text-ink">Proporsi Kategori</h3>
                <p class="text-xs text-ink-muted">Persentase dokumen berdasarkan klasifikasi</p>
            </div>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent 5 Archives Table -->
    <div class="bg-white rounded-2xl border border-line shadow-sm overflow-hidden">
        <div class="p-6 border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-ink">Arsip Terkini</h3>
                <p class="text-xs text-ink-muted">5 dokumen terakhir yang masuk ke dalam sistem</p>
            </div>
            <a href="{{ route('arsip.index') }}" class="inline-flex items-center gap-1 text-xs font-bold hover:underline transition-colors" style="color: #0050C8">
                <span>Lihat Seluruh Arsip</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        @if($recentArsips->isEmpty())
        <div class="p-8 text-center text-xs text-ink-muted font-medium">
            Belum ada arsip yang diunggah.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-ink-muted">
                <thead class="text-[11px] uppercase tracking-wider font-bold border-b border-line" style="background: rgba(210,230,250,0.25); color: #1E293B">
                    <tr>
                        <th class="px-6 py-3.5">Kode Arsip</th>
                        <th class="px-6 py-3.5">Judul Dokumen</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pengunggah</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($recentArsips as $arsip)
                    <tr class="hover:bg-ice/20 transition-colors duration-100">
                        <td class="px-6 py-4 font-mono text-xs font-bold" style="color: #0050C8">
                            <div>{{ $arsip->no_item_arsip ?: $arsip->kode_arsip }}</div>
                            @if($arsip->no_item_arsip)
                                <div class="text-[10px] text-ink-muted font-normal">{{ $arsip->kode_arsip }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="font-semibold text-ink line-clamp-1">{{ $arsip->hal ?: $arsip->judul_arsip }}</div>
                            @if($arsip->hal && $arsip->judul_arsip && $arsip->hal !== $arsip->judul_arsip)
                                <div class="text-[11px] text-ink-muted truncate">{{ $arsip->judul_arsip }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" style="background: rgba(210,230,250,0.5); color: #0050C8; border-color: rgba(0,80,200,0.2)">
                                {{ $arsip->kategori->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-ink-muted">
                            {{ $arsip->tgl_upload ? $arsip->tgl_upload->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-ink-muted">
                            {{ $arsip->user->nama_user ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('arsip.showFile', $arsip) }}" target="_blank" title="Lihat Dokumen"
                                class="inline-flex items-center justify-center p-2 rounded-xl text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/60 transition-all hover:scale-105 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@push('scripts')
@if($totalArsip > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Monthly Bar Chart — warna KAPUAS primary
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyLabels = @json($chartMonthlyLabels);
        const monthlyData = @json($chartMonthlyData);

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Jumlah Arsip',
                    data: monthlyData,
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return 'rgba(0,80,200,0.75)';
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(0, 60, 170, 0.6)');
                        gradient.addColorStop(1, 'rgba(50, 150, 250, 0.95)');
                        return gradient;
                    },
                    hoverBackgroundColor: '#003CAA',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 36,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#003CAA',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12, family: 'Plus Jakarta Sans', weight: 'bold' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#64748B'
                        },
                        grid: { color: '#E2E8F0' }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#64748B'
                        },
                        grid: { display: false }
                    }
                }
            }
        });

        // Category Doughnut Chart — palet multi-warna agar mudah dibedakan
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryLabels = @json($chartCategoryLabels);
        const categoryData = @json($chartCategoryData);

        // Palet warna yang jelas berbeda satu sama lain (bukan gradasi biru)
        const distinctColors = [
            '#003CAA', // Biru Navy (brand utama)
            '#10B981', // Hijau Emerald
            '#F59E0B', // Amber / Kuning Emas
            '#EF4444', // Merah Coral
            '#af8dffff', // Ungu Violet
            '#06B6D4', // Cyan / Teal
            '#F97316', // Oranye
            '#EC4899', // Pink Magenta
            '#14B8A6', // Teal Gelap
            '#6366F1', // Indigo
        ];

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: distinctColors.slice(0, categoryData.length),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            boxWidth: 12,
                            padding: 12,
                            color: '#1E293B'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#003CAA',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12, family: 'Plus Jakarta Sans', weight: 'bold' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    }
                },
                cutout: '68%'
            }
        });
    });
</script>
@endif
@endpush
@endsection

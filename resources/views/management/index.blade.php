@extends('layouts.app')

@section('title', 'Manajemen Keuangan')

@push('styles')
<style>
    :root {
        --budget-aman: #10B981;
        --budget-waspada: #F59E0B;
        --budget-bahaya: #EF4444;
        --budget-aman-bg: rgba(16, 185, 129, 0.12);
        --budget-waspada-bg: rgba(245, 158, 11, 0.12);
        --budget-bahaya-bg: rgba(239, 68, 68, 0.12);
    }

    .ringkasan-stat {
        transition: all 0.2s ease;
        min-width: 0;
    }
    .ringkasan-stat:hover {
        transform: translateY(-2px);
    }

    .budget-progress {
        height: 12px;
        border-radius: 10px;
        overflow: hidden;
        background: var(--bg-main);
    }
    .budget-progress .progress-bar {
        border-radius: 10px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .budget-progress.aman .progress-bar { background: var(--budget-aman); }
    .budget-progress.waspada .progress-bar { background: var(--budget-waspada); }
    .budget-progress.bahaya .progress-bar { background: var(--budget-bahaya); }

    .insight-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1rem;
        transition: all 0.2s ease;
    }
    .insight-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }
    .insight-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .chart-compact {
        height: 160px;
    }

    .budget-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .budget-indicator.aman { background: var(--budget-aman-bg); color: var(--budget-aman); }
    .budget-indicator.waspada { background: var(--budget-waspada-bg); color: var(--budget-waspada); }
    .budget-indicator.bahaya { background: var(--budget-bahaya-bg); color: var(--budget-bahaya); }

    .budget-modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        background: linear-gradient(135deg, var(--primary) 0%, #047857 100%);
        color: white;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.25);
    }

    .automation-badge-count {
        font-size: 0.6rem;
        padding: 0.15rem 0.45rem;
        border-radius: 20px;
        background: var(--bg-main);
        color: var(--text-muted);
        font-weight: 600;
    }

    .ringkasan-stat {
        background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(14,165,233,0.12)) !important;
        border: 1px solid rgba(5,150,105,0.16) !important;
        box-shadow: 0 18px 40px rgba(5,150,105,0.12) !important;
    }

    .budget-progress {
        height: 14px !important;
        background: rgba(15,23,42,0.06) !important;
    }
    .budget-progress .progress-bar {
        background: linear-gradient(135deg, var(--budget-aman), var(--budget-waspada)) !important;
        box-shadow: 0 10px 26px rgba(5,150,105,0.14) !important;
    }

    .insight-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(229,246,255,0.95)) !important;
        border-color: rgba(5,150,105,0.12) !important;
        box-shadow: 0 22px 56px rgba(5,150,105,0.12) !important;
    }

    .budget-indicator.aman,
    .budget-indicator.waspada,
    .budget-indicator.bahaya {
        background: rgba(255,255,255,0.95) !important;
        border: 1px solid rgba(5,150,105,0.12) !important;
    }

    .budget-modal-icon {
        background: linear-gradient(135deg, var(--primary), #047857) !important;
        box-shadow: 0 16px 40px rgba(5,150,105,0.18) !important;
    }

    @media (max-width: 768px) {
        .ringkasan-hero-saldo {
            font-size: 1.6rem !important;
        }
        .budget-progress {
            height: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-xl-4 py-2">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold font-poppins text-dark d-flex align-items-center gap-2">
                <i class="ph-fill ph-wallet" style="color: var(--primary);"></i> Manajemen Keuangan
            </h4>
            <p class="text-muted small mb-0">Pantau pemasukan, kendalikan pengeluaran, dan atur budget bulanan.</p>
        </div>
        <a href="{{ route('tabung.index') }}" class="btn btn-sm btn-outline-modern rounded-pill px-3 d-flex align-items-center gap-1">
            <i class="ph ph-arrow-left"></i> Dashboard
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert bg-light-success text-success border border-success border-opacity-25 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="ph-fill ph-check-circle fs-4"></i>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert bg-light-danger text-danger border border-danger border-opacity-25 rounded-4 alert-dismissible fade show shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="ph-fill ph-warning-circle fs-4"></i>
                <div class="fw-bold">Terjadi kesalahan!</div>
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $privacyClass = '';
        if (isset($sembunyikanSaldo) && $sembunyikanSaldo) {
            $privacyClass = 'saldo-hidden';
        } elseif (isset($blurSaldo) && $blurSaldo) {
            $privacyClass = 'saldo-blur';
        }
        $privasiSensitif = isset($modePrivasi) && $modePrivasi ? 'privasi-sensitif' : '';

        $budgetColor = $budgetStatus === 'bahaya' ? 'var(--budget-bahaya)' : ($budgetStatus === 'waspada' ? 'var(--budget-waspada)' : 'var(--budget-aman)');
        $budgetLabel = $budgetStatus === 'bahaya' ? 'Bahaya' : ($budgetStatus === 'waspada' ? 'Waspada' : ($budgetStatus === 'aman' ? 'Aman' : 'Belum Diatur'));
        $budgetIcon = $budgetStatus === 'bahaya' ? 'ph-warning-circle' : ($budgetStatus === 'waspada' ? 'ph-warning' : ($budgetStatus === 'aman' ? 'ph-shield-check' : 'ph-coins'));
    @endphp

    {{-- ======================================================================== --}}
    {{-- FULL-WIDTH RINGKASAN HERO --}}
    {{-- ======================================================================== --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="fintech-card card-hero p-4 p-xl-5 rounded-4 shadow-lg position-relative overflow-hidden" style="border: none;">
                <div class="position-absolute" style="top: -40px; right: -40px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -60px; left: -60px; width: 160px; height: 160px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <i class="ph-fill ph-wallet position-absolute" style="right: 30px; bottom: 10px; font-size: 8rem; color: rgba(255,255,255,0.04); transform: rotate(-10deg);"></i>

                <div class="position-relative" style="z-index: 1;">
                    <div class="row g-4 align-items-end">
                        <div class="col-lg-5">
                            <p class="mb-1 text-white-50 fw-medium small text-uppercase tracking-wide d-flex align-items-center gap-1">
                                <i class="ph ph-coins"></i> Ringkasan Bulan {{ strtoupper(now()->format('F Y')) }}
                            </p>
                            <h2 class="font-poppins fw-bold ringkasan-hero-saldo mb-0 {{ $privacyClass }} {{ $privasiSensitif }}" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                                {{ format_currency($saldoTersedia) }}
                            </h2>
                            <small class="text-white-50" style="font-size: 0.75rem;">Saldo tersedia saat ini</small>
                        </div>
                        <div class="col-lg-7">
                            <div class="row g-2">
                                <div class="col-3 col-md-3">
                                    <div class="bg-white bg-opacity-10 rounded-3 p-2 p-xl-3 text-center ringkasan-stat">
                                        <div class="small text-white-50 d-flex align-items-center justify-content-center gap-1 mb-1" style="font-size: 0.6rem;">
                                            <i class="ph ph-arrow-down-left" style="color: #6ee7b7;"></i> Pemasukan
                                        </div>
                                        <div class="fw-bold text-white {{ $privasiSensitif }}" style="font-size: 0.8rem;">{{ format_currency($totalIncome) }}</div>
                                    </div>
                                </div>
                                <div class="col-3 col-md-3">
                                    <div class="bg-white bg-opacity-10 rounded-3 p-2 p-xl-3 text-center ringkasan-stat">
                                        <div class="small text-white-50 d-flex align-items-center justify-content-center gap-1 mb-1" style="font-size: 0.6rem;">
                                            <i class="ph ph-arrow-up-right" style="color: #fca5a5;"></i> Pengeluaran
                                        </div>
                                        <div class="fw-bold text-white {{ $privasiSensitif }}" style="font-size: 0.8rem;">{{ format_currency($totalExpense) }}</div>
                                    </div>
                                </div>
                                <div class="col-3 col-md-3">
                                    <div class="bg-white bg-opacity-10 rounded-3 p-2 p-xl-3 text-center ringkasan-stat">
                                        <div class="small text-white-50 d-flex align-items-center justify-content-center gap-1 mb-1" style="font-size: 0.6rem;">
                                            <i class="ph ph-piggy-bank" style="color: #fcd34d;"></i> Tabungan
                                        </div>
                                        <div class="fw-bold text-white {{ $privasiSensitif }}" style="font-size: 0.8rem;">{{ format_currency($totalTabungan) }}</div>
                                    </div>
                                </div>
                                <div class="col-3 col-md-3">
                                    <div class="bg-white bg-opacity-10 rounded-3 p-2 p-xl-3 text-center ringkasan-stat">
                                        <div class="small text-white-50 d-flex align-items-center justify-content-center gap-1 mb-1" style="font-size: 0.6rem;">
                                            <i class="ph ph-chart-line-up" style="color: #93c5fd;"></i> Aset
                                        </div>
                                        <div class="fw-bold text-white {{ $privasiSensitif }}" style="font-size: 0.8rem;">{{ format_currency($totalAset) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- 2-COLUMN LAYOUT --}}
    {{-- ======================================================================== --}}
    <div class="row g-4">

        {{-- LEFT: FORMS & AUTOMATION --}}
        <div class="col-lg-7 d-flex flex-column gap-4">

            {{-- Tabs --}}
            <ul class="nav nav-pills gap-2" id="managementTab" role="tablist" style="margin-bottom: 0 !important;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4 d-flex align-items-center gap-1" id="transaksi-tab" data-bs-toggle="pill" data-bs-target="#transaksi" type="button" role="tab">
                        <i class="ph ph-pen-nib"></i> Catat Transaksi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 d-flex align-items-center gap-1" id="automasi-tab" data-bs-toggle="pill" data-bs-target="#automasi" type="button" role="tab">
                        <i class="ph ph-robot"></i> Automasi
                        @if($automations->count() > 0)
                            <span class="automation-badge-count">{{ $automations->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="managementTabContent" style="margin-top: -0.5rem;">

                {{-- === TAB: Catat Transaksi === --}}
                <div class="tab-pane fade show active" id="transaksi" role="tabpanel">
                    <div class="row g-3">
                        {{-- INCOME --}}
                        <div class="col-md-6">
                            <div class="fintech-card p-4 h-100 rounded-4 border-color" style="border-top: 3px solid var(--success);">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <div class="insight-icon bg-light-success text-success rounded-circle">
                                        <i class="ph-fill ph-trend-up fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-poppins fw-semibold mb-0" style="font-size: 0.9rem;">Pemasukan</h6>
                                        <small class="text-muted" style="font-size: 0.65rem;">Catat pemasukan baru</small>
                                    </div>
                                </div>
                                <form action="{{ route('management.income.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Nama</label>
                                        <input type="text" name="nama" class="form-control form-control-modern" placeholder="Gaji, Bonus, dll" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Jumlah ({{ $currencySymbol }})</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-muted px-3">{{ $currencySymbol }}</span>
                                            <input type="text" name="jumlah" class="form-control form-control-modern js-currency-format border-start-0" placeholder="0" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-modern btn-primary-modern w-100">
                                        <i class="ph-fill ph-check me-1"></i> Simpan
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- EXPENSE --}}
                        <div class="col-md-6">
                            <div class="fintech-card p-4 h-100 rounded-4 border-color" style="border-top: 3px solid var(--danger);">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <div class="insight-icon bg-light-danger text-danger rounded-circle">
                                        <i class="ph-fill ph-trend-down fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-poppins fw-semibold mb-0" style="font-size: 0.9rem;">Pengeluaran</h6>
                                        <small class="text-muted" style="font-size: 0.65rem;">Catat pengeluaran baru</small>
                                    </div>
                                </div>
                                <form action="{{ route('management.expense.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Nama</label>
                                        <input type="text" name="nama" class="form-control form-control-modern" placeholder="Belanja, Tagihan, dll" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Jumlah ({{ $currencySymbol }})</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-muted px-3">{{ $currencySymbol }}</span>
                                            <input type="text" name="jumlah" class="form-control form-control-modern js-currency-format border-start-0" placeholder="0" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Kategori</label>
                                        <select name="kategori" class="form-control form-control-modern" required>
                                            <option value="" disabled selected>Pilih</option>
                                            <option value="Kebutuhan Pokok">Kebutuhan Pokok</option>
                                            <option value="Mendesak">Mendesak</option>
                                            <option value="Kebutuhan Lain">Kebutuhan Lain</option>
                                            <option value="Cicilan">Cicilan</option>
                                            <option value="Hiburan">Hiburan</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-medium">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-modern w-100 text-white fw-semibold" style="background: var(--danger); border: none;">
                                        <i class="ph-fill ph-check me-1"></i> Catat
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === TAB: Automasi === --}}
                <div class="tab-pane fade" id="automasi" role="tabpanel">
                    <div class="fintech-card p-4 mb-4 rounded-4 border-color">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="insight-icon rounded-circle" style="background: rgba(5, 150, 105, 0.1); color: var(--primary);">
                                <i class="ph-fill ph-robot fs-5"></i>
                            </div>
                            <div>
                                <h6 class="font-poppins fw-semibold mb-0" style="font-size: 0.9rem;">Tambah Automasi</h6>
                                <small class="text-muted" style="font-size: 0.65rem;">Transaksi berulang setiap bulan</small>
                            </div>
                        </div>
                        <form action="{{ route('management.automation.store') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Tipe</label>
                                    <select name="tipe" class="form-control form-control-modern" required>
                                        <option value="pemasukan">Pemasukan</option>
                                        <option value="pengeluaran">Pengeluaran</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label text-muted small fw-medium">Nama</label>
                                    <input type="text" name="nama" class="form-control form-control-modern" placeholder="Gaji PT ABC" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Jumlah ({{ $currencySymbol }})</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted px-2">{{ $currencySymbol }}</span>
                                        <input type="text" name="jumlah" class="form-control form-control-modern js-currency-format border-start-0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Kategori</label>
                                    <select name="kategori" class="form-control form-control-modern">
                                        <option value="">—</option>
                                        <option value="Kebutuhan Pokok">Kebutuhan Pokok</option>
                                        <option value="Mendesak">Mendesak</option>
                                        <option value="Kebutuhan Lain">Kebutuhan Lain</option>
                                        <option value="Cicilan">Cicilan</option>
                                        <option value="Hiburan">Hiburan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Tgl. Rutin</label>
                                    <input type="number" name="tanggal_rutin" class="form-control form-control-modern" min="1" max="31" placeholder="1-31" required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-modern btn-primary-modern w-100">
                                        <i class="ph-fill ph-rocket-launch me-1"></i> Aktifkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <h6 class="font-poppins fw-semibold mb-3 d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                        <i class="ph-fill ph-list-checks" style="color: var(--primary);"></i> Automasi Aktif
                    </h6>

                    @forelse($automations as $auto)
                        <div class="fintech-card p-3 mb-2 d-flex align-items-center justify-content-between rounded-4 border-color">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="insight-icon rounded-circle flex-shrink-0 {{ $auto->tipe == 'pemasukan' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">
                                    <i class="ph-fill {{ $auto->tipe == 'pemasukan' ? 'ph-trend-up' : 'ph-trend-down' }} fs-5"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark text-truncate">{{ $auto->nama }}</div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                        <span class="small text-muted d-flex align-items-center gap-1"><i class="ph ph-calendar-blank"></i> Tgl {{ $auto->tanggal_rutin }}</span>
                                        <span class="small text-muted">•</span>
                                        <span class="fw-semibold small {{ $auto->tipe == 'pemasukan' ? 'text-success' : 'text-danger' }}">{{ $auto->tipe == 'pemasukan' ? '+' : '-' }}{{ format_currency($auto->jumlah) }}</span>
                                        @if($auto->kategori)
                                            <span class="small text-muted">•</span>
                                            <span class="small fw-medium text-muted bg-light rounded-pill px-2 py-0">{{ $auto->kategori }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('management.automation.destroy', $auto->id) }}" method="POST" onsubmit="return confirm('Hapus automasi ini?');" class="m-0 ms-2 flex-shrink-0">
                                @csrf
                                <button class="btn btn-sm btn-outline-modern rounded-circle p-1 border-0 text-danger" title="Hapus" style="width: 32px; height: 32px;">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="fintech-card p-4 text-center rounded-4 border-color">
                            <div class="insight-icon bg-light text-muted rounded-circle mx-auto mb-2" style="width: 48px; height: 48px;">
                                <i class="ph ph-robot fs-4"></i>
                            </div>
                            <p class="text-muted small mb-0">Belum ada automasi aktif.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: BUDGET + INSIGHTS + CHART --}}
        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- === BUDGET CARD === --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="insight-icon rounded-circle" style="background: {{ $budgetStatus === 'bahaya' ? 'var(--budget-bahaya-bg)' : ($budgetStatus === 'waspada' ? 'var(--budget-waspada-bg)' : ($budgetStatus === 'aman' ? 'var(--budget-aman-bg)' : 'var(--bg-main)')) }}; color: {{ $budgetColor }};">
                            <i class="ph-fill {{ $budgetIcon }} fs-5"></i>
                        </div>
                        <div>
                            <h6 class="font-poppins fw-semibold mb-0" style="font-size: 0.9rem;">Budget Bulanan</h6>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ now()->format('F Y') }}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-modern rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#budgetModal" style="font-size: 0.75rem;">
                        <i class="ph ph-pencil-line me-1"></i> Atur
                    </button>
                </div>

                @if($budget > 0)
                    {{-- Large progress bar --}}
                    <div class="mb-3">
                        <div class="budget-progress {{ $budgetStatus }}">
                            <div class="progress-bar" style="width: {{ $budgetPct }}%;"></div>
                        </div>
                    </div>

                    {{-- Metrics row --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background: var(--bg-main);">
                                <div class="small text-muted" style="font-size: 0.6rem;">TERPAKAI</div>
                                <div class="fw-bold text-dark {{ $privasiSensitif }}" style="font-size: 0.85rem;">{{ format_currency($totalExpense) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background: var(--bg-main);">
                                <div class="small text-muted" style="font-size: 0.6rem;">TERSISA</div>
                                <div class="fw-bold {{ $isOverBudget ? 'text-danger' : 'text-success' }} {{ $privasiSensitif }}" style="font-size: 0.85rem;">{{ format_currency($sisaBudget) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded-3" style="background: var(--bg-main);">
                                <div class="small text-muted" style="font-size: 0.6rem;">ANGGARAN</div>
                                <div class="fw-bold text-dark {{ $privasiSensitif }}" style="font-size: 0.85rem;">{{ format_currency($budget) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Status + Percentage --}}
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-color">
                        <span class="budget-indicator {{ $budgetStatus }}">
                            <i class="ph-fill {{ $budgetIcon }}"></i> {{ $budgetLabel }}
                        </span>
                        <span class="fw-bold" style="color: {{ $budgetColor }}; font-size: 1.1rem;">{{ $budgetPct }}%</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="insight-icon bg-light text-muted rounded-circle mx-auto mb-2" style="width: 48px; height: 48px;">
                            <i class="ph ph-coins fs-4"></i>
                        </div>
                        <p class="text-muted small mb-2">Belum mengatur budget bulanan.</p>
                        <button class="btn btn-modern btn-primary-modern btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#budgetModal" style="font-size: 0.8rem;">
                            <i class="ph ph-plus me-1"></i> Atur Budget
                        </button>
                    </div>
                @endif
            </div>

            {{-- === INSIGHT GRID === --}}
            <div class="row g-3">
                {{-- Kategori Terbesar --}}
                <div class="col-6">
                    <div class="insight-card h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="insight-icon bg-light-danger text-danger" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                <i class="ph-fill ph-trend-down"></i>
                            </div>
                            <small class="text-muted fw-medium" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.3px;">Terbesar</small>
                        </div>
                        @if($kategoriTerbesar)
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $kategoriTerbesar }}</div>
                            <div class="fw-semibold {{ $privasiSensitif }}" style="color: var(--danger); font-size: 0.8rem;">{{ format_currency($nominalTerbesar) }}</div>
                        @else
                            <div class="text-muted" style="font-size: 0.75rem;">Belum ada data</div>
                        @endif
                    </div>
                </div>

                {{-- Rata-rata Harian --}}
                <div class="col-6">
                    <div class="insight-card h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="insight-icon bg-light-primary text-primary" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                <i class="ph-fill ph-calendar"></i>
                            </div>
                            <small class="text-muted fw-medium" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.3px;">Rata-rata</small>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Per Hari</div>
                        <div class="fw-semibold text-dark {{ $privasiSensitif }}" style="font-size: 0.8rem;">{{ format_currency($rataHarian) }}</div>
                    </div>
                </div>

                {{-- Prediksi Sisa --}}
                <div class="col-12">
                    <div class="insight-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="insight-icon" style="width: 32px; height: 32px; font-size: 0.9rem; background: rgba(5, 150, 105, 0.1); color: var(--primary);">
                                    <i class="ph-fill ph-chart-line-up"></i>
                                </div>
                                <div>
                                    <small class="text-muted fw-medium" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.3px;">Prediksi Akhir Bulan</small>
                                    <div class="fw-semibold text-dark" style="font-size: 0.8rem;">Sisa Budget</div>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($prediksiSisaBudget >= 0)
                                    <span class="fw-bold text-success {{ $privasiSensitif }}" style="font-size: 1rem;">{{ format_currency($prediksiSisaBudget) }}</span>
                                    <div class="small text-success" style="font-size: 0.6rem;"><i class="ph-fill ph-check-circle"></i> On track</div>
                                @else
                                    <span class="fw-bold text-danger {{ $privasiSensitif }}" style="font-size: 1rem;">{{ format_currency($prediksiSisaBudget) }}</span>
                                    <div class="small text-danger" style="font-size: 0.6rem;"><i class="ph-fill ph-warning-circle"></i> Melebihi budget</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === CHART CARD === --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="insight-icon rounded-circle" style="width: 32px; height: 32px; background: rgba(5, 150, 105, 0.1); color: var(--primary);">
                            <i class="ph-fill ph-chart-pie-slice fs-6"></i>
                        </div>
                        <div>
                            <h6 class="font-poppins fw-semibold mb-0" style="font-size: 0.85rem;">Kategori Pengeluaran</h6>
                        </div>
                    </div>
                    <small class="text-muted" style="font-size: 0.6rem;">{{ now()->format('M Y') }}</small>
                </div>

                @if(count($chartKeys) > 0)
                    <div class="row g-3 align-items-center">
                        <div class="col-5">
                            <div class="chart-compact">
                                <canvas id="expenseChart"></canvas>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="d-flex flex-column gap-1" style="max-height: 160px; overflow-y: auto;">
                                @foreach($chartKeys as $i => $key)
                                    @php
                                        $colors = ['#10B981','#F59E0B','#3B82F6','#8B5CF6','#EF4444','#06B6D4','#F43F5E','#84CC16'];
                                        $pct = $totalExpense > 0 ? round(($chartValues[$i] / $totalExpense) * 100, 1) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2 py-1">
                                        <span class="rounded-circle flex-shrink-0" style="width: 8px; height: 8px; background: {{ $colors[$i % 8] }};"></span>
                                        <span class="small text-muted flex-grow-1 text-truncate" style="font-size: 0.7rem;">{{ $key }}</span>
                                        <span class="small fw-semibold text-dark flex-shrink-0 {{ $privasiSensitif }}" style="font-size: 0.7rem;">{{ $pct }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="insight-icon bg-light text-muted rounded-circle mx-auto mb-2" style="width: 44px; height: 44px;">
                            <i class="ph ph-chart-pie-slice fs-4"></i>
                        </div>
                        <p class="text-muted small mb-0">Belum ada pengeluaran bulan ini.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- MODAL ATUR BUDGET (Large, Modern) --}}
{{-- ======================================================================== --}}
<div class="modal fade" id="budgetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="position-relative" style="background: linear-gradient(135deg, var(--primary) 0%, #047857 100%); padding: 2rem 2rem 3rem;">
                <div class="position-absolute" style="top: -30px; right: -30px; width: 140px; height: 140px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -50px; left: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
                <div class="position-relative text-center" style="z-index: 1;">
                    <div class="budget-modal-icon">
                        <i class="ph-fill ph-coins"></i>
                    </div>
                    <h4 class="text-white font-poppins fw-bold mb-1">Atur Budget Bulanan</h4>
                    <p class="text-white-50 small mb-0">Tentukan batas pengeluaran bulanan untuk membantu mengelola keuangan lebih disiplin.</p>
                </div>
            </div>

            <form action="{{ route('management.budget.set') }}" method="POST" class="p-4">
                @csrf
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label text-muted small fw-medium">Nominal Budget ({{ $currencySymbol }})</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0 text-muted fw-bold px-3" style="font-size: 1.1rem;">{{ $currencySymbol }}</span>
                            <input type="text" name="anggaran_bulanan" class="form-control form-control-modern js-currency-format border-start-0 fw-bold" style="font-size: 1.1rem;" value="{{ number_format($budget,0,'','') }}" placeholder="0" required>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-modern btn-primary-modern w-100 py-3">
                            <i class="ph-fill ph-floppy-disk me-2"></i> Simpan
                        </button>
                    </div>
                </div>

                @if($budget > 0)
                    <div class="mt-4 pt-3 border-top border-color">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="small text-muted" style="font-size: 0.65rem;">BUDGET SAAT INI</div>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ format_currency($budget) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted" style="font-size: 0.65rem;">TERPAKAI</div>
                                <div class="fw-bold {{ $isOverBudget ? 'text-danger' : 'text-success' }}" style="font-size: 0.9rem;">{{ format_currency($totalExpense) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted" style="font-size: 0.65rem;">TERSISA</div>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ format_currency($sisaBudget) }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <button type="button" class="btn btn-sm btn-outline-modern rounded-pill position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" style="width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="ph ph-x"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.chartData = {
        labels: {!! json_encode($chartKeys) !!},
        data: {!! json_encode($chartValues) !!}
    };
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/management.js') }}?v={{ time() }}"></script>
@endpush

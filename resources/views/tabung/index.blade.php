@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Modern Fintech Aesthetics */
        :root {
            --primary-modern: #059669; /* Soft Green */
            --success-modern: #10B981;
            --warning-modern: #F59E0B;
            --bg-light-success: #ecfdf5;
            --bg-light-warning: #fffbeb;
            --border-radius-lg: 1.25rem;
            --border-radius-xl: 1.5rem;
        }

        .fintech-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .fintech-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        .card-hero {
            background: linear-gradient(135deg, var(--primary-modern) 0%, #047857 100%);
            color: white;
            border: none;
            box-shadow: 0 10px 30px rgba(5, 150, 105, 0.2);
        }

        .text-hero-balance {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Standardized Buttons */
        .btn-modern {
            border-radius: 1rem;
            font-weight: 600;
            padding: 0.8rem 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-light-modern {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn-light-modern:hover {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .btn-success-modern { background-color: var(--success-modern); color: white; border: none; }
        .btn-success-modern:hover { background-color: #059669; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(16, 185, 129, 0.2); }
        
        .btn-outline-modern { background-color: white; border: 1px solid #e5e7eb; color: #4b5563; }
        .btn-outline-modern:hover { background-color: #f9fafb; border-color: #d1d5db; transform: translateY(-2px); }

        /* Progress Bar */
        .progress-modern {
            height: 8px;
            background-color: #f3f4f6;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-modern {
            background: linear-gradient(90deg, var(--success-modern) 0%, #34d399 100%);
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Sidebar Widgets */
        .icon-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .icon-sm { width: 36px; height: 36px; font-size: 1.2rem; }
        
        .icon-bg-hero {
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-size: 10rem;
            color: rgba(255, 255, 255, 0.05);
            transform: rotate(-15deg);
            pointer-events: none;
        }

        /* Heatmap Activity */
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            max-width: 320px;
            margin: 0 auto;
        }
        .heatmap-box {
            aspect-ratio: 1;
            border-radius: 6px;
            transition: transform 0.1s ease;
        }
        .heatmap-box:hover:not(.heatmap-empty) {
            transform: scale(1.15);
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            z-index: 2;
            position: relative;
        }
        .heatmap-empty { background-color: transparent; border: 1px dashed rgba(0,0,0,0.05); }
        .heatmap-level-0 { background-color: #f3f4f6; }
        .heatmap-level-1 { background-color: #d1fae5; }
        .heatmap-level-2 { background-color: #6ee7b7; }
        .heatmap-level-3 { background-color: #10b981; }
        .heatmap-level-4 { background-color: #047857; }
        .heatmap-box.is-today { border: 2px solid var(--warning-modern); }

        /* Responsive Tweaks */
        @media (max-width: 768px) {
            .text-hero-balance { font-size: 1.8rem; }
            .btn-modern { width: 100%; margin-bottom: 0.5rem; }
        }
        
        /* Empty State */
        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            background-color: #f9fafb;
            border-radius: var(--border-radius-lg);
            border: 2px dashed #e5e7eb;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush

@section('content')
<!-- MENGGUNAKAN CONTAINER-FLUID PENUH TANPA MAX-WIDTH UNTUK MEMANFAATKAN LAYAR DESKTOP -->
<div class="container-fluid px-3 px-xl-4 py-2">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold font-poppins text-dark">
                Halo, <span style="color: var(--primary-modern);">{{ auth()->user()->nama }}</span> 👋
            </h4>
            <p class="text-muted small mb-0 mt-1">Mari wujudkan mimpimu, satu langkah setiap hari.</p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('incomes.create') }}" class="btn btn-sm btn-outline-modern rounded-pill px-3">
                <i class="ph ph-plus me-1"></i> Pemasukan Baru
            </a>
            <a href="{{ route('tabung.create') }}" class="btn btn-sm btn-outline-modern rounded-pill px-3">
                <i class="ph ph-target me-1"></i> Target Baru
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert bg-light-success text-success border border-success border-opacity-25 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="ph-fill ph-check-circle fs-4"></i>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert bg-light-danger text-danger border border-danger border-opacity-25 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
            <i class="ph-fill ph-warning-circle fs-4"></i>
            <div class="fw-medium">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert bg-light-danger text-danger border border-danger border-opacity-25 rounded-4 alert-dismissible fade show shadow-sm" role="alert">
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

    {{-- 3-COLUMN LAYOUT UNTUK LAYAR LEBAR --}}
    <div class="row g-4">
        
        {{-- KOLOM 1 KIRI: SALDO & MOTIVASI (col-xl-3 col-lg-4) --}}
        <div class="col-xl-3 col-lg-4 d-flex flex-column gap-4">
            
            {{-- Privacy helper --}}
            @php
                $dots = '••••••••';
                // Priority: sembunyikanSaldo (permanent) > blurSaldo (hover reveal)
                $privacyClass = '';
                if (isset($sembunyikanSaldo) && $sembunyikanSaldo) {
                    $privacyClass = 'saldo-hidden';
                } elseif (isset($blurSaldo) && $blurSaldo) {
                    $privacyClass = 'saldo-blur';
                }
                // modePrivasi adds .privasi-sensitif to all monetary amounts
                $privasiSensitif = isset($modePrivasi) && $modePrivasi ? 'privasi-sensitif' : '';
            @endphp

            {{-- HERO BALANCE --}}
            <div class="fintech-card card-hero p-4 rounded-4 flex-grow-0" data-widget-id="saldo">
                <i class="ph-fill ph-wallet icon-bg-hero"></i>
                <div class="position-relative" style="z-index: 1;">
                    <p class="mb-1 text-white-50 fw-medium small text-uppercase tracking-wide">Saldo Tersedia</p>
                    @if($privacyClass === 'saldo-hidden')
                        <div class="text-hero-balance font-poppins mb-4 saldo-hidden" style="font-size: 2rem;">{{ $currencySymbol }} {{ $dots }}</div>
                    @else
                        <div class="text-hero-balance font-poppins js-count-up mb-4 {{ $privacyClass }} {{ $privasiSensitif }}" data-value="{{ convert_currency_value($saldoTersedia ?? 0) }}" data-currency="true" data-symbol="{{ get_currency_symbol() }}" style="font-size: 2rem;">
                            {{ format_currency($saldoTersedia ?? 0) }}
                        </div>
                    @endif
                    
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 mt-auto">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-white-50 pb-2 mb-2">
                            <span class="small text-white-50 d-flex align-items-center gap-1"><i class="ph-fill ph-piggy-bank text-light"></i> Total Tabungan</span>
                            @if($privacyClass === 'saldo-hidden')
                                <span class="fw-bold fs-6 saldo-hidden">{{ $currencySymbol }} {{ $dots }}</span>
                            @else
                                <span class="fw-bold fs-6 {{ $privacyClass }} {{ $privasiSensitif }}">{{ format_currency($usedForSaving ?? 0) }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-white-50 d-flex align-items-center gap-1"><i class="ph-fill ph-chart-line-up text-light"></i> Total Aset</span>
                            @if($privacyClass === 'saldo-hidden')
                                <span class="fw-bold fs-6 saldo-hidden">{{ $currencySymbol }} {{ $dots }}</span>
                            @else
                                <span class="fw-bold fs-6 {{ $privacyClass }} {{ $privasiSensitif }}">{{ format_currency($totalAset ?? 0) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- MOTIVATIONAL STREAK --}}
            @if(!isset($widgetVisibility) || $widgetVisibility['streak'])
            <div class="fintech-card p-4 rounded-4 text-center border-0 flex-grow-1 d-flex flex-column justify-content-center" data-widget-id="streak" style="background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%);">
                <div class="icon-container bg-white {{ $hasSavedToday ? 'text-danger' : 'text-secondary' }} mx-auto mb-3 shadow-sm" style="width: 56px; height: 56px; border-radius: 50%;">
                    <i class="ph-fill ph-fire" style="font-size: 2rem;"></i>
                </div>
                <h3 class="font-poppins fw-bold text-dark mb-1 js-count-up" data-value="{{ $streak ?? 0 }}" style="font-size: 2.5rem; letter-spacing: -1px;">
                    {{ $streak ?? 0 }} Hari
                </h3>
                <h6 class="fw-bold text-dark mb-2">Konsistensi Menabung!</h6>
                <p class="small text-muted mb-4 px-2">Pertahankan streakmu agar target impian semakin cepat tercapai.</p>
                
                <div class="d-flex justify-content-around bg-white p-3 rounded-4 shadow-sm mx-1 mt-auto">
                    <div>
                        <div class="small text-muted mb-1" style="font-size: 0.7rem;">Bulan Ini</div>
                        @if($privacyClass === 'saldo-hidden')
                            <div class="fw-bold text-success saldo-hidden" style="font-size: 0.9rem;">{{ get_currency_symbol() }} {{ $dots }}</div>
                        @else
                            <div class="fw-bold text-success js-count-up {{ $privasiSensitif }}" data-value="{{ convert_currency_value($totalBulanIni ?? 0) }}" data-currency="true" data-symbol="{{ get_currency_symbol() }}" style="font-size: 0.9rem;">
                                {{ format_currency($totalBulanIni ?? 0) }}
                            </div>
                        @endif
                    </div>
                    <div class="border-start"></div>
                    <div>
                        <div class="small text-muted mb-1" style="font-size: 0.7rem;">Rata-rata</div>
                        @if($privacyClass === 'saldo-hidden')
                            <div class="fw-bold text-dark saldo-hidden" style="font-size: 0.9rem;">{{ get_currency_symbol() }} {{ $dots }}</div>
                        @else
                            <div class="fw-bold text-dark js-count-up {{ $privasiSensitif }}" data-value="{{ convert_currency_value($rata2PerCheckin ?? 0) }}" data-currency="true" data-symbol="{{ get_currency_symbol() }}" style="font-size: 0.9rem;">
                                {{ format_currency($rata2PerCheckin ?? 0) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- KOLOM 2 TENGAH: AKSI UTAMA & VISUALISASI (col-xl-6 col-lg-8) --}}
        <div class="col-xl-6 col-lg-8 d-flex flex-column gap-4" style="order: -1;">
            
            {{-- COMBINED CARD: TARGET AKTIF & FORM NABUNG --}}
            @if(!isset($widgetVisibility) || $widgetVisibility['target'])
            <div class="fintech-card p-0 border-success shadow-sm" data-widget-id="target_aktif" style="border-width: 2px;">
                <div class="row g-0 h-100">
                    
                    {{-- Target Progress --}}
                    <div class="col-md-6 p-4 border-end-md" style="background-color: var(--bg-light-success);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ph-fill ph-target text-success fs-4"></i>
                                <h6 class="mb-0 font-poppins fw-bold text-dark text-uppercase tracking-wide" style="font-size: 0.8rem;">Target Aktif</h6>
                            </div>
                            @if($activeTarget)
                            <button type="button" class="btn btn-sm text-danger p-0 shadow-none" onclick="confirmDeleteTarget('{{ $activeTarget->id }}', '{{ addslashes($activeTarget->nama) }}', {{ $activeTarget->total_terkumpul }})" title="Hapus Target">
                                <i class="ph ph-trash fs-5"></i>
                            </button>
                            <form id="delete-form-{{ $activeTarget->id }}" action="{{ route('tabung.destroy', $activeTarget->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </div>
                        
                        @if($activeTarget)
                            @if($activeTarget->gambar)
                                <img src="{{ asset('storage/' . $activeTarget->gambar) }}" class="img-fluid rounded-4 mb-3 shadow-sm w-100" style="height: 140px; object-fit: cover;" alt="{{ $activeTarget->nama }}">
                            @endif
                            <h4 class="fw-bold text-dark mb-3">{{ $activeTarget->nama }}</h4>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    @if($privacyClass === 'saldo-hidden')
                                        <span class="fw-bold text-success fs-5 saldo-hidden">{{ $currencySymbol }} {{ $dots }}</span>
                                    @else
                                        <span class="fw-bold text-success fs-5 {{ $privasiSensitif }}">{{ format_currency($activeTarget->total_terkumpul) }}</span>
                                    @endif
                                    <span class="small fw-bold text-muted bg-white px-2 py-1 rounded-pill shadow-sm" style="font-size: 0.75rem;">{{ number_format($activeTarget->persentase_progres, 1) }}%</span>
                                </div>
                                <div class="progress-modern w-100 shadow-sm" style="height: 10px;">
                                    <div class="progress-bar-modern js-progress-bar-modern" style="width: {{ $activeTarget->persentase_progres }}%" data-progress="{{ number_format($activeTarget->persentase_progres, 2, '.', '') }}"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted" style="font-size: 0.7rem;">0</small>
                                    @if($privacyClass === 'saldo-hidden')
                                        <small class="text-muted fw-bold saldo-hidden" style="font-size: 0.7rem;">{{ $currencySymbol }} {{ $dots }}</small>
                                    @else
                                        <small class="text-muted fw-bold {{ $privasiSensitif }}" style="font-size: 0.7rem;">{{ format_currency($activeTarget->jumlah_target) }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="empty-state bg-transparent border-0 p-2 text-start align-items-start">
                                <span class="fw-bold d-block mb-1">Belum Ada Target</span>
                                <p class="small text-muted mb-3">Buat target pertamamu untuk mulai menabung terarah.</p>
                                <a href="{{ route('tabung.create') }}" class="btn btn-sm btn-success-modern rounded-pill px-3">Buat Target Baru</a>
                            </div>
                        @endif
                    </div>

                    {{-- Form Nabung Compact --}}
                    <div class="col-md-6 p-4 bg-white d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ph-fill ph-coins text-warning fs-4"></i>
                            <h6 class="mb-0 font-poppins fw-bold text-dark text-uppercase tracking-wide" style="font-size: 0.8rem;">Catat Tabungan</h6>
                        </div>

                        @if($activeTarget)
                            <form action="{{ route('checkins.store') }}" method="POST" class="d-flex flex-column gap-3">
                                @csrf
                                <input type="hidden" name="target_tabungan_id" value="{{ $activeTarget->id }}">
                                
                                <div class="input-group input-group-lg shadow-sm rounded-3">
                                    <span class="input-group-text bg-light border-0 fw-bold text-success">{{ $currencySymbol }}</span>
                                    <input type="text" name="jumlah" class="form-control border-0 bg-light js-currency-format fw-bold" placeholder="Nominal" required>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light border-0 px-2"><i class="ph ph-calendar-blank text-muted"></i></span>
                                            <input type="date" name="tanggal_transaksi" class="form-control border-0 bg-light small" style="font-size: 0.85rem;" value="{{ now()->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" name="catatan" class="form-control border-0 bg-light shadow-sm small h-100" style="font-size: 0.85rem;" placeholder="Catatan (opsional)">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success-modern w-100 py-2 mt-1 rounded-3 fw-bold shadow-sm">
                                    <i class="ph-fill ph-paper-plane-right me-1"></i> Simpan
                                </button>
                                <div class="text-center mt-2">
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <i class="ph ph-info"></i> Sisa Saldo Tersedia: 
                                        <span class="fw-bold text-dark {{ $privasiSensitif }}">{{ format_currency($saldoTersedia ?? 0) }}</span>
                                    </small>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <i class="ph ph-lock-key fs-1 text-muted opacity-50 mb-2"></i>
                                <p class="small text-muted mb-0">Form terkunci. Buat target terlebih dahulu.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- HEATMAP ACTIVITY --}}
            @if(!isset($widgetVisibility) || $widgetVisibility['heatmap'])
            <div class="fintech-card p-4 flex-grow-1" data-widget-id="heatmap">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-poppins fw-bold mb-0">Aktivitas Menabung</h6>
                        <small class="text-muted">Progres tabungan harian Anda</small>
                    </div>
                    <div class="d-flex align-items-center gap-1 bg-light rounded-pill p-1 shadow-sm">
                        <a href="{{ route('tabung.index', ['month' => $heatmapPrevMonth]) }}" class="btn btn-sm rounded-circle text-muted" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;"><i class="ph ph-caret-left fw-bold"></i></a>
                        <span class="small fw-bold text-dark px-3">{{ $heatmapCurrentMonthName }}</span>
                        @if(!$isCurrentMonth)
                        <a href="{{ route('tabung.index', ['month' => $heatmapNextMonth]) }}" class="btn btn-sm rounded-circle text-muted" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center;"><i class="ph ph-caret-right fw-bold"></i></a>
                        @else
                        <button class="btn btn-sm rounded-circle text-muted" style="width:32px; height:32px; padding:0; display:flex; align-items:center; justify-content:center; opacity: 0.5; cursor: not-allowed;"><i class="ph ph-caret-right fw-bold"></i></button>
                        @endif
                    </div>
                </div>

                <div class="heatmap-container mb-4 px-2">
                    <div class="d-flex justify-content-between mb-2 px-1" style="max-width: 320px; margin: 0 auto;">
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Min</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Sen</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Sel</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Rab</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Kam</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Jum</small>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem; width: 14.28%; text-align: center;">Sab</small>
                    </div>
                    <div class="heatmap-grid">
                        @foreach($heatmapData as $box)
                            @if(is_null($box))
                                <div class="heatmap-box heatmap-empty"></div>
                            @else
                                <div class="heatmap-box heatmap-level-{{ $box['level'] }} {{ $box['isToday'] ? 'is-today shadow' : '' }}" 
                                     data-bs-toggle="tooltip" 
                                     data-bs-placement="top" 
                                     title="{{ \Carbon\Carbon::parse($box['date'])->format('d M') }}: {{ format_currency($box['total']) }}">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <div class="d-flex justify-content-center align-items-center mt-auto pt-3 border-top border-color">
                    <small class="text-muted me-3" style="font-size: 0.75rem;">Intensitas Nominal:</small>
                    <div class="d-flex gap-2 align-items-center">
                        <small class="text-muted" style="font-size: 0.65rem;">Rendah</small>
                        <div class="heatmap-box heatmap-level-0" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                        <div class="heatmap-box heatmap-level-1" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                        <div class="heatmap-box heatmap-level-2" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                        <div class="heatmap-box heatmap-level-3" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                        <div class="heatmap-box heatmap-level-4" style="width: 14px; height: 14px; border-radius: 3px;"></div>
                        <small class="text-muted" style="font-size: 0.65rem;">Tinggi</small>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- KOLOM 3 KANAN: RIWAYAT & TARGET TAMBAHAN (col-xl-3 col-lg-12) --}}
        <div class="col-xl-3 col-lg-12 d-flex flex-column gap-4">
            
            {{-- SUMMARY STATS (Pindah dari Heatmap ke panel terpisah) --}}
            @if(!isset($widgetVisibility) || $widgetVisibility['statistik'])
            <div class="fintech-card p-3 rounded-4 border-0" data-widget-id="statistik" style="background-color: #f8fafc;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-medium text-dark d-flex align-items-center gap-2"><i class="ph-fill ph-trophy text-warning"></i> Rekor Streak</span>
                    <span class="small fw-bold">{{ $longestStreak }} Hari</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-medium text-dark d-flex align-items-center gap-2"><i class="ph-fill ph-calendar-check text-success"></i> Frekuensi Bulanan</span>
                    <span class="small fw-bold">{{ $jumlahCheckinBulanIni }}x Nabung</span>
                </div>
            </div>
            @endif

            {{-- DAFTAR TARGET LAINNYA --}}
            @if((!isset($widgetVisibility) || $widgetVisibility['target']) && $targets->count() > 1)
            <div class="fintech-card" data-widget-id="target_lainnya">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h6 class="font-poppins fw-bold mb-0" style="font-size: 0.9rem;">Target Lainnya</h6>
                    <a href="{{ route('tabung.create') }}" class="small text-primary text-decoration-none"><i class="ph ph-plus"></i> Baru</a>
                </div>
                <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                    @foreach($targets as $tg)
                        @if(!$activeTarget || $activeTarget->id !== $tg->id)
                            <div class="list-group-item px-3 py-3 border-0 border-bottom">
                                <div class="d-flex gap-3">
                                    @if($tg->gambar)
                                        <img src="{{ asset('storage/' . $tg->gambar) }}" class="rounded-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $tg->nama }}">
                                    @else
                                        <div class="rounded-3 d-flex justify-content-center align-items-center text-success" style="width: 50px; height: 50px; background-color: var(--bg-light-success); flex-shrink: 0;">
                                            <i class="ph-fill ph-target fs-3"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1 min-w-0 d-flex flex-column justify-content-center">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark text-truncate pe-2" style="font-size: 0.85rem;">{{ $tg->nama }}</div>
                                            <span class="small fw-bold text-muted" style="font-size: 0.7rem;">{{ number_format($tg->persentase_progres, 0) }}%</span>
                                        </div>
                                        <div class="progress-modern w-100 mb-2" style="height: 5px; background-color: #f1f5f9;">
                                            <div class="progress-bar-modern bg-success" style="width: {{ $tg->persentase_progres }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-sm text-danger p-0 shadow-none" onclick="confirmDeleteTarget('{{ $tg->id }}', '{{ addslashes($tg->nama) }}', {{ $tg->total_terkumpul }})" title="Hapus Target">
                                                <i class="ph ph-trash fs-6"></i>
                                            </button>
                                            <form id="delete-form-{{ $tg->id }}" action="{{ route('tabung.destroy', $tg->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            
                                            <form action="{{ route('targets.active', $tg->id) }}" method="POST" class="text-end">
                                                @csrf
                                                <button type="submit" class="btn btn-sm text-primary p-0 shadow-none text-decoration-none" style="font-size: 0.75rem; font-weight: 600;">Jadikan Aktif <i class="ph-bold ph-arrow-right"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- TRANSACTIONS HISTORY --}}
            @if(!isset($widgetVisibility) || $widgetVisibility['riwayat'])
            <div class="fintech-card flex-grow-1" data-widget-id="riwayat">
                <div class="p-3 border-bottom border-color d-flex justify-content-between align-items-center bg-light">
                    <h6 class="font-poppins fw-bold mb-0" style="font-size: 0.9rem;">Riwayat Terakhir</h6>
                    <ul class="nav nav-pills small gap-1" id="historyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-0 px-2 rounded-pill fw-medium" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#tab-tabungan">Nabung</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-0 px-2 rounded-pill fw-medium" style="font-size: 0.75rem;" data-bs-toggle="tab" data-bs-target="#tab-income">Masuk</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content h-100" id="historyTabContent">
                    <!-- Tab Tabungan -->
                    <div class="tab-pane fade show active h-100" id="tab-tabungan" role="tabpanel">
                        @if(!empty($recentCheckins) && count($recentCheckins))
                            <div class="list-group list-group-flush">
                                @foreach($recentCheckins as $rc)
                                    <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center border-0 border-bottom" style="transition: background 0.2s;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-container bg-light-success text-success rounded-circle" style="width: 28px; height: 28px;">
                                                <i class="ph-fill ph-piggy-bank" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem; max-width: 100px;">{{ $rc->targetTabungan->nama ?? 'Tabungan' }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($rc->tanggal_transaksi)->format('d M') }}</div>
                                            </div>
                                        </div>
                                        <div class="fw-bold text-success" style="font-size: 0.8rem;">+{{ format_currency($rc->jumlah) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-muted h-100 d-flex flex-column justify-content-center">
                                <i class="ph ph-receipt fs-3 mb-2 opacity-50"></i>
                                <p class="small mb-0" style="font-size: 0.75rem;">Belum ada riwayat.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Tab Income -->
                    <div class="tab-pane fade h-100" id="tab-income" role="tabpanel">
                        @if(!empty($recentIncomes) && count($recentIncomes))
                            <div class="list-group list-group-flush">
                                @foreach($recentIncomes as $inc)
                                    <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-container" style="background-color: #e0e7ff; color: #4f46e5; border-radius: 50%; width: 28px; height: 28px;">
                                                <i class="ph-fill ph-wallet" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem; max-width: 100px;">{{ ucfirst($inc->nama) }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($inc->tanggal)->format('d M') }}</div>
                                            </div>
                                        </div>
                                        <div class="fw-bold" style="color: #4f46e5; font-size: 0.8rem;">+{{ format_currency($inc->jumlah) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-muted h-100 d-flex flex-column justify-content-center">
                                <i class="ph ph-wallet fs-3 mb-2 opacity-50"></i>
                                <p class="small mb-0" style="font-size: 0.75rem;">Belum ada pemasukan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function confirmDeleteTarget(id, name, totalSavings) {
        let text = "Tindakan ini tidak dapat dibatalkan.";
        if (totalSavings > 0) {
            // Format currency in JS
            let formattedSavings = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalSavings);
            text = `Target ini sudah memiliki saldo terkumpul sebesar ${formattedSavings}. Jika dihapus, saldo tersebut akan dikembalikan ke Saldo Tersedia Anda.`;
        }

        Swal.fire({
            title: `Hapus Target "${name}"?`,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    // Initialize tooltips for heatmap
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Mobile Widget Reordering Logic
        const isMobile = window.innerWidth < 992; // Bootstrap lg breakpoint
        if (isMobile) {
            @php
                $urutan = json_decode($userSettings['urutan_widget'] ?? '[]', true);
            @endphp
            const urutanWidget = {!! json_encode($urutan ?: []) !!};
            
            if (urutanWidget && urutanWidget.length > 0) {
                // Find all widgets by their data attribute
                const widgets = {};
                document.querySelectorAll('[data-widget-id]').forEach(el => {
                    widgets[el.getAttribute('data-widget-id')] = el;
                });

                // Find the main row container
                const rowContainer = document.querySelector('.row.g-4');
                
                if (rowContainer) {
                    // Create a single column wrapper for mobile to stack them exactly in order
                    const mobileWrapper = document.createElement('div');
                    mobileWrapper.className = 'col-12 d-flex flex-column gap-4';
                    
                    // Append widgets in the saved order
                    urutanWidget.forEach(id => {
                        if (widgets[id]) {
                            // If widget is inside a col wrapper, we pull the card out
                            mobileWrapper.appendChild(widgets[id]);
                            delete widgets[id]; // mark as sorted
                        }
                    });

                    // Append any remaining widgets that weren't in the saved order (fallback)
                    Object.values(widgets).forEach(widget => {
                        mobileWrapper.appendChild(widget);
                    });

                    // Hide original columns (col-xl-3, col-xl-6) since they are now empty
                    rowContainer.querySelectorAll('.col-xl-3, .col-xl-6').forEach(col => {
                        col.style.display = 'none';
                    });

                    // Append the new mobile wrapper
                    rowContainer.appendChild(mobileWrapper);
                }
            }
        }
    });
</script>
@endpush
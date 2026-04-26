@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid p-0" style="max-width: 1200px; margin: 0 auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold font-poppins d-flex align-items-center gap-2">
                Halo, <span class="text-primary">{{ auth()->user()->name }}</span> 👋
            </h4>
            <small class="text-muted">Pantau keuangan dan progres tabunganmu hari ini.</small>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert bg-light-success text-success border-0 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="ph-fill ph-check-circle fs-4"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HERO SECTION : BALANCE --}}
    <div class="fintech-card card-hero p-4 mb-3 rounded-4 shadow-lg">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4 position-relative" style="z-index: 1;">
            <div>
                <p class="mb-1 text-white-50 fw-medium">Total Saldo Tersedia</p>
                <div class="text-hero-balance font-poppins js-count-up" data-value="{{ $saldo ?? 0 }}" data-currency="true">
                    Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}
                </div>
            </div>
            
            <div class="d-flex gap-4">
                <div>
                    <div class="small text-white-50 mb-1"><i class="ph ph-arrow-down-left text-success"></i> Total Pemasukan</div>
                    <div class="fw-semibold fs-5">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="small text-white-50 mb-1"><i class="ph ph-arrow-up-right text-warning"></i> Total Keluar</div>
                    <div class="fw-semibold fs-5">Rp {{ number_format(($usedForSaving ?? 0) + ($usedForExpense ?? 0), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <a href="{{ route('incomes.create') }}" class="btn btn-primary-modern w-100 py-3 text-decoration-none">
                <i class="ph ph-plus-circle fs-4"></i>
                Pemasukan Baru
            </a>
        </div>
        <div class="col-md-4">
            <button id="btn-toggle-nabung" class="btn btn-success-modern w-100 py-3">
                <i class="ph ph-piggy-bank fs-4"></i>
                Catat Tabungan
            </button>
        </div>
        <div class="col-md-4">
            <a href="{{ route('tabung.create') }}" class="btn btn-outline-modern w-100 py-3 bg-card text-decoration-none border">
                <i class="ph ph-target fs-4"></i>
                Target Baru
            </a>
        </div>
    </div>

    {{-- HIDDEN FORM: NABUNG / CHECK-IN --}}
    <div id="section-nabung" class="toggle-section mb-3">
        <div class="fintech-card p-4 border-success" style="border-width: 2px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 font-poppins text-success d-flex align-items-center gap-2">
                    <i class="ph-fill ph-coins"></i> Nabung Hari Ini
                </h5>
            </div>

            @if($activeTarget)
                <form action="{{ route('checkins.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="target_id" value="{{ $activeTarget->id }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-medium">Tanggal</label>
                            <input type="date" id="input-tanggal" name="tanggal" class="form-control form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-medium">Nominal (Rp)</label>
                            <input type="text" name="nominal" class="form-control form-control-modern js-currency-format" placeholder="Contoh: 20.000" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-medium">Catatan (Opsional)</label>
                            <div class="d-flex gap-2">
                                <input type="text" name="catatan" class="form-control form-control-modern" placeholder="Misal: Sisa uang jajan">
                                <button type="submit" class="btn btn-success-modern px-4">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert bg-light-warning text-warning border-0 d-flex align-items-center gap-2 mb-0 rounded-3">
                    <i class="ph-fill ph-warning-circle fs-4"></i>
                    <span>Kamu belum punya target aktif. Silakan buat target terlebih dahulu.</span>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT COLUMN: TARGETS & HISTORY --}}
        <div class="col-lg-8">
            
            {{-- DAFTAR TARGET --}}
            <div class="d-flex justify-content-between align-items-end mb-3">
                <div>
                    <h5 class="font-poppins fw-semibold mb-1">Target Tabungan</h5>
                    <small class="text-muted">Pantau progres mimpimu</small>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @forelse($targets as $tg)
                    @php
                        $totalTarget = $tg->checkins->sum('nominal');
                        $progress = $tg->harga_target > 0 ? ($totalTarget / $tg->harga_target) * 100 : 0;
                        $progress = min(100, max(0, $progress));
                    @endphp

                    <div class="col-md-6">
                        <div class="fintech-card h-100 p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="font-poppins fw-semibold mb-1 d-flex align-items-center gap-2">
                                        {{ $tg->nama }}
                                        @if($activeTarget && $activeTarget->id === $tg->id)
                                            <span class="badge bg-light-success text-success border border-success rounded-pill small">Aktif</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">Target: Rp {{ number_format($tg->harga_target, 0, ',', '.') }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-modern border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="ph ph-dots-three fs-4"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                        <li><a class="dropdown-item" href="{{ route('tabung.edit', $tg->id) }}"><i class="ph ph-pencil-simple me-2"></i>Edit</a></li>
                                        <li>
                                            <form action="{{ route('tabung.destroy', $tg->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus target ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="ph ph-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="fw-bold text-success">Rp {{ number_format($totalTarget, 0, ',', '.') }}</span>
                                    <span class="small fw-semibold">{{ number_format($progress, 1) }}%</span>
                                </div>

                                <div class="progress-modern w-100 mb-2">
                                    <div class="progress-bar-modern js-progress-bar-modern" data-progress="{{ number_format($progress, 2, '.', '') }}"></div>
                                </div>

                                <div class="small text-muted text-end">
                                    Sisa: Rp {{ number_format(max(0, $tg->harga_target - $totalTarget), 0, ',', '.') }}
                                </div>
                                
                                @if($activeTarget && $activeTarget->id !== $tg->id)
                                    <form action="{{ route('targets.active', $tg->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-modern w-100 btn-sm py-2">
                                            Jadikan Target Aktif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="fintech-card p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center">
                            <div class="icon-container bg-light-primary text-primary mb-3 mx-auto" style="width: 64px; height: 64px;">
                                <i class="ph ph-target fs-1"></i>
                            </div>
                            <h6>Belum ada target tabungan</h6>
                            <p class="small mb-0">Klik tombol Target Baru untuk mulai.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- TRANSACTIONS HISTORY --}}
            <div class="fintech-card mb-3">
                <div class="p-4 border-bottom border-color d-flex justify-content-between align-items-center">
                    <h6 class="font-poppins fw-semibold mb-0">Riwayat Terakhir</h6>
                    <ul class="nav nav-pills small" id="historyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-1 px-3 rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-tabungan">Tabungan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3 rounded-pill" data-bs-toggle="tab" data-bs-target="#tab-income">Pemasukan</button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content" id="historyTabContent">
                    <!-- Tab Tabungan -->
                    <div class="tab-pane fade show active" id="tab-tabungan" role="tabpanel">
                        @if(!empty($recentCheckins) && count($recentCheckins))
                            <div class="list-group list-group-flush rounded-bottom">
                                @foreach($recentCheckins as $rc)
                                    <div class="list-group-item list-item-modern d-flex justify-content-between align-items-center bg-transparent">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-container bg-light-success text-success icon-sm">
                                                <i class="ph-fill ph-piggy-bank"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $rc->target->nama ?? 'Tanpa Target' }}</div>
                                                <div class="small text-muted">{{ \Carbon\Carbon::parse($rc->tanggal)->format('d M Y') }} • {{ $rc->catatan ?? 'Menabung' }}</div>
                                            </div>
                                        </div>
                                        <div class="fw-bold text-success">+ Rp {{ number_format($rc->nominal, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 text-center text-muted small">Belum ada riwayat tabungan.</div>
                        @endif
                    </div>
                    
                    <!-- Tab Income -->
                    <div class="tab-pane fade" id="tab-income" role="tabpanel">
                        @if(!empty($recentIncomes) && count($recentIncomes))
                            <div class="list-group list-group-flush rounded-bottom">
                                @foreach($recentIncomes as $inc)
                                    <div class="list-group-item list-item-modern d-flex justify-content-between align-items-center bg-transparent">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-container bg-light-primary text-primary icon-sm">
                                                <i class="ph-fill ph-wallet"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ ucfirst($inc->nama) }}</div>
                                                <div class="small text-muted">{{ \Carbon\Carbon::parse($inc->tanggal)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="fw-bold text-primary">+ Rp {{ number_format($inc->nominal, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 text-center text-muted small">Belum ada riwayat pemasukan.</div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: STREAK & CALENDAR --}}
        <div class="col-lg-4">
            
            {{-- STREAK CARD --}}
            @php
                $activeTotal = $activeTarget ? $activeTarget->checkins->sum('nominal') : 0;
                $activeGoal  = $activeTarget ? (int) $activeTarget->harga_target : 0;
                $ringProgress = $activeGoal > 0 ? ($activeTotal / $activeGoal) * 100 : 0;
                $ringProgress = max(0, min(100, $ringProgress));
            @endphp

            <div class="fintech-card p-4 mb-3 text-center">
                <div class="icon-container bg-light-warning text-warning mx-auto mb-3" style="width: 56px; height: 56px;">
                    <i class="ph-fill ph-fire fs-2"></i>
                </div>
                <h2 class="font-poppins fw-bold text-warning mb-0 js-count-up" data-value="{{ $streak ?? 0 }}">
                    {{ $streak ?? 0 }}
                </h2>
                <div class="fw-medium mb-1">Hari Streak 🔥</div>
                <p class="small text-muted mb-4">Terus konsisten menabung untuk mencapai targetmu!</p>
                
                <div class="p-3 bg-main rounded-3 text-start">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-muted">Bulan Ini</span>
                        <span class="small fw-bold text-success">Rp {{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Frekuensi</span>
                        <span class="small fw-medium">{{ $jumlahCheckinBulanIni ?? 0 }}x Nabung</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Rata-rata</span>
                        <span class="small fw-medium">Rp {{ number_format($rata2PerCheckin ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                @if($estimasiTanggal)
                    <div class="mt-3 pt-3 border-top border-color">
                        <div class="small text-muted mb-1">Estimasi Target "<b>{{ $activeTarget->nama ?? '-' }}</b>" Tercapai</div>
                        <div class="fw-semibold text-primary d-flex align-items-center justify-content-center gap-2">
                            <i class="ph-fill ph-flag-checkered"></i>
                            {{ $estimasiTanggal->format('d M Y') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- CALENDAR WIDGET --}}
            <div class="fintech-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-poppins fw-semibold mb-0">Kalender Aktivitas</h6>
                    <span class="badge bg-light-primary text-primary border border-primary-subtle rounded-pill" id="pickedDateBadge"></span>
                </div>
                <div class="small text-muted mb-3">Klik tanggal untuk mencatat tabungan di hari tersebut.</div>
                
                <!-- FullCalendar Element -->
                <div id="savingCalendar" class="font-inter" style="font-size: 0.85rem;"></div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script id="calendarEventsData" type="application/json">@json($calendarEvents ?? [])</script>
@endpush
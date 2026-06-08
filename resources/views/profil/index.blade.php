@extends('layouts.app')

@section('title', 'Profil Saya - Sakuin')

@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 font-poppins fw-bold text-dark">Profil Saya</h4>
            <small class="text-muted">Kelola informasi akun dan lihat aktivitas menabungmu</small>
        </div>
    </div>

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

    <div class="row g-4">

        {{-- ======================== --}}
        {{-- KOLOM KIRI                --}}
        {{-- ======================== --}}
        <div class="col-xl-4 col-lg-5 d-flex flex-column gap-4">

            {{-- HERO PROFILE CARD --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex gap-3">
                    <div class="position-relative flex-shrink-0">
                        @if($user->foto_url)
                            <img src="{{ $user->foto_url }}" alt="{{ $user->inisial }}" class="rounded-circle" style="width: 88px; height: 88px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-poppins fw-bold" style="width: 88px; height: 88px; font-size: 2.25rem;">
                                {{ $user->inisial }}
                            </div>
                        @endif
                        <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 18px; height: 18px; border-width: 3px !important;" title="Aktif"></div>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        @php
                            $jam = now()->hour;
                            if ($jam < 12) $sapaan = 'Selamat Pagi';
                            elseif ($jam < 16) $sapaan = 'Selamat Siang';
                            elseif ($jam < 19) $sapaan = 'Selamat Sore';
                            else $sapaan = 'Selamat Malam';
                        @endphp
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ $sapaan }} <span class="ms-1">👋</span></p>
                        <h5 class="font-poppins fw-bold text-dark mb-0 text-truncate">{{ $user->nama }}</h5>
                        <p class="text-muted mb-2" style="font-size: 0.8rem;">{{ $user->email }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-light-primary text-primary rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                <i class="ph-fill ph-calendar-blank" style="font-size: 0.7rem;"></i> {{ $tanggalBergabung->translatedFormat('M Y') }}
                            </span>
                            <span class="badge bg-light-success text-success rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                <i class="ph-fill ph-star" style="font-size: 0.7rem;"></i> {{ $hariBergabung }} hari
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="border-color my-3">

                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small d-flex align-items-center gap-1">
                        <i class="ph-fill ph-wallet text-primary"></i> Saldo Tersedia
                    </span>
                    <span class="fw-bold text-dark fs-5 {{ ($hideBalance ?? false) ? 'saldo-hidden' : '' }}">{{ $hideBalance ? '••••••••' : format_currency($saldoSaatIni) }}</span>
                </div>

                @php
                    $quotes = [
                        '"Menabung bukan tentang seberapa banyak, tapi seberapa konsisten."',
                        '"Kebebasan finansial dimulai dari satu langkah kecil hari ini."',
                        '"Uang yang kamu tabung hari ini adalah kebebasan untuk masa depanmu."',
                        '"Bukan soal gaji besar, tapi soal kebiasaan yang benar."',
                        '"Konsisten menabung lebih berharga dari jumlah besar sekali waktu."',
                        '"Setiap rupiah yang kamu sisihkan adalah investasi untuk mimpi."',
                        '"Kebiasaan kecil hari ini, hasil besar di masa depan."',
                        '"Jangan remehkan tabungan kecil. Konsistensi mengubahnya menjadi besar."',
                    ];
                @endphp
                <p class="text-muted fst-italic text-center mt-3 mb-0" style="font-size: 0.78rem;">
                    <i class="ph-fill ph-quotes text-primary opacity-50 me-1"></i>
                    {{ $quotes[array_rand($quotes)] }}
                    <i class="ph-fill ph-quotes text-primary opacity-50 ms-1" style="transform: scaleX(-1); display: inline-block;"></i>
                </p>
            </div>

            {{-- TARGET TERDEKAT --}}
            @if($targetTerdekat)
                <div class="fintech-card p-4 rounded-4 border-color">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="icon-container bg-light-primary text-primary rounded-circle" style="width: 34px; height: 34px; font-size: 1rem;">
                            <i class="ph-fill ph-target"></i>
                        </div>
                        <div>
                            <h6 class="font-poppins fw-bold text-dark mb-0" style="font-size: 0.9rem;">Target Terdekat</h6>
                            <small class="text-muted">Prioritas utama</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="fs-3">{{ $targetTerdekat->ikon ?? '🎯' }}</span>
                        <div>
                            <p class="fw-bold text-dark mb-0">{{ $targetTerdekat->nama }}</p>
                            <small class="text-muted">{{ $hideBalance ? '••••••••' : format_currency($targetTerdekat->total_terkumpul) }} dari {{ $hideBalance ? '••••••••' : format_currency($targetTerdekat->jumlah_target) }}</small>
                        </div>
                    </div>
                    <div class="progress-modern mb-2">
                        <div class="progress-bar-modern js-progress-bar" data-width="{{ number_format($targetTerdekat->persentase_progres, 1) }}"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary" style="font-size: 0.85rem;">{{ $targetTerdekat->persentase_progres }}%</span>
                        @php $sisaHari = $targetTerdekat->tanggal_target ? now()->startOfDay()->diffInDays($targetTerdekat->tanggal_target->startOfDay(), false) : null; @endphp
                        @if($sisaHari !== null && $sisaHari > 0)
                            <span class="text-muted small d-flex align-items-center gap-1">
                                <i class="ph ph-clock"></i>{{ $sisaHari }} hari lagi
                            </span>
                        @elseif($sisaHari !== null && $sisaHari <= 0)
                            <span class="text-danger small d-flex align-items-center gap-1">
                                <i class="ph ph-warning"></i>Lewat deadline
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <div class="fintech-card p-4 rounded-4 border-color">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="icon-container bg-light-primary text-primary rounded-circle" style="width: 34px; height: 34px; font-size: 1rem;">
                            <i class="ph-fill ph-target"></i>
                        </div>
                        <h6 class="font-poppins fw-bold text-dark mb-0" style="font-size: 0.9rem;">Target Tabungan</h6>
                    </div>
                    <p class="text-muted small mb-0">
                        Belum ada target aktif.
                        <a href="{{ route('tabung.create') }}" class="text-primary text-decoration-none fw-medium">Buat target sekarang</a>
                    </p>
                </div>
            @endif

        </div>

        {{-- ======================== --}}
        {{-- KOLOM KANAN               --}}
        {{-- ======================== --}}
        <div class="col-xl-8 col-lg-7 d-flex flex-column gap-4">

            {{-- FINANCIAL SUMMARY --}}
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="fintech-card p-3 rounded-4 border-color h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="ph-fill ph-wallet"></i>
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">Saldo Saat Ini</span>
                        </div>
                        <p class="fw-bold text-dark mb-0 {{ ($hideBalance ?? false) ? 'saldo-hidden' : '' }}" style="font-size: 0.95rem;">{{ $hideBalance ? '••••••••' : format_currency($saldoSaatIni) }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fintech-card p-3 rounded-4 border-color h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="ph-fill ph-piggy-bank"></i>
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">Total Menabung</span>
                        </div>
                        <p class="fw-bold text-dark mb-0 {{ ($hideBalance ?? false) ? 'saldo-hidden' : '' }}" style="font-size: 0.95rem;">{{ $hideBalance ? '••••••••' : format_currency($totalMenabung) }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fintech-card p-3 rounded-4 border-color h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-light-warning text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="ph-fill ph-target"></i>
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">Target Aktif</span>
                        </div>
                        <p class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $targetAktif }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="fintech-card p-3 rounded-4 border-color h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="ph-fill ph-trophy"></i>
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">Target Tercapai</span>
                        </div>
                        <p class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $targetTercapai }}</p>
                    </div>
                </div>
            </div>

            {{-- ACTIVITY HEATMAP --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="font-poppins fw-bold text-dark mb-0">Aktivitas Menabung</h6>
                        <small class="text-muted">Konsistensi 1 tahun terakhir</small>
                    </div>
                    <div class="icon-container bg-light-success text-success rounded-circle" style="width: 36px; height: 36px;">
                        <i class="ph-fill ph-git-commit fs-5"></i>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-3">
                        <div class="bg-light rounded-3 p-2 text-center d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="ph-fill ph-calendar-check text-primary mb-1" style="font-size: 1.2rem;"></i>
                            <p class="fw-bold text-dark mb-0 lh-1" style="font-size: 1rem;">{{ $hariAktif }}</p>
                            <span class="text-muted" style="font-size: 0.6rem;">Hari Aktif</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-3 p-2 text-center d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="ph-fill ph-fire text-danger mb-1" style="font-size: 1.2rem;"></i>
                            <p class="fw-bold text-dark mb-0 lh-1" style="font-size: 1rem;">{{ $streakSaatIni }}</p>
                            <span class="text-muted" style="font-size: 0.6rem;">Streak Saat Ini</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-3 p-2 text-center d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="ph-fill ph-trophy text-warning mb-1" style="font-size: 1.2rem;"></i>
                            <p class="fw-bold text-dark mb-0 lh-1" style="font-size: 1rem;">{{ $bestStreak }}</p>
                            <span class="text-muted" style="font-size: 0.6rem;">Streak Terbaik</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-light rounded-3 p-2 text-center d-flex flex-column align-items-center justify-content-center h-100">
                            <i class="ph-fill ph-receipt text-success mb-1" style="font-size: 1.2rem;"></i>
                            <p class="fw-bold text-dark mb-0 lh-1" style="font-size: 1rem;">{{ $totalTransaksi }}</p>
                            <span class="text-muted" style="font-size: 0.6rem;">Total Transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="heatmap-wrapper position-relative w-100 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                    <div class="d-inline-flex flex-column" style="min-width: 800px;">
                        <div class="d-flex" style="margin-left: 28px; margin-bottom: 4px; height: 16px;">
                            @php
                                $renderedMonths = [];
                            @endphp
                            @foreach(array_chunk($heatmapData, 7) as $weekData)
                                @php
                                    $showMonth = false;
                                    $monthName = '';
                                    foreach($weekData as $day) {
                                        if(!$day['is_padding'] && $day['isFirstOfMonth'] && !in_array($day['monthName'], $renderedMonths)) {
                                            $showMonth = true;
                                            $monthName = $day['monthName'];
                                            $renderedMonths[] = $monthName;
                                            break;
                                        }
                                    }
                                @endphp
                                <div style="width: 16px; position: relative;">
                                    @if($showMonth)
                                        <span class="text-muted" style="font-size: 0.65rem; position: absolute; bottom: 0; left: 0; white-space: nowrap; line-height: 1;">{{ $monthName }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex">
                            <div class="d-flex flex-column justify-content-between text-muted me-2" style="font-size: 0.65rem; height: 105px; padding-top: 5px; width: 20px;">
                                <div style="visibility: hidden;">Sun</div>
                                <div>Mon</div>
                                <div style="visibility: hidden;">Tue</div>
                                <div>Wed</div>
                                <div style="visibility: hidden;">Thu</div>
                                <div>Fri</div>
                                <div style="visibility: hidden;">Sat</div>
                            </div>
                            <div class="d-flex gap-1" style="height: 105px;">
                                @foreach(array_chunk($heatmapData, 7) as $weekData)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($weekData as $day)
                                            @if($day['is_padding'])
                                                <div style="width: 12px; height: 12px; background: transparent;"></div>
                                            @else
                                                <div style="width: 12px; height: 12px; border-radius: 2px;"
                                                     class="heatmap-level-{{ $day['level'] }} border {{ $day['level'] == 0 ? 'border-color' : 'border-0' }}"
                                                     data-bs-toggle="tooltip"
                                                     data-bs-placement="top"
                                                     title="{{ $day['total'] > 0 ? format_currency($day['total']) . ' pada ' : 'Tidak menabung pada ' }}{{ \Carbon\Carbon::parse($day['date'])->translatedFormat('d M Y') }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-3 gap-2 text-muted" style="font-size: 0.7rem;">
                    <span>Sedikit</span>
                    <div style="width: 12px; height: 12px; border-radius: 2px;" class="heatmap-level-0 border border-color"></div>
                    <div style="width: 12px; height: 12px; border-radius: 2px;" class="heatmap-level-1"></div>
                    <div style="width: 12px; height: 12px; border-radius: 2px;" class="heatmap-level-2"></div>
                    <div style="width: 12px; height: 12px; border-radius: 2px;" class="heatmap-level-3"></div>
                    <div style="width: 12px; height: 12px; border-radius: 2px;" class="heatmap-level-4"></div>
                    <span>Banyak</span>
                </div>
            </div>

            {{-- ACHIEVEMENTS --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="font-poppins fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="ph-fill ph-trophy text-warning"></i> Prestasi
                        </h6>
                        <small class="text-muted">{{ $achievements['total_tercapai'] }} dari {{ $achievements['total_semua'] }} terkunci</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $achievements['persentase'] }}%</span>
                        <div style="width: 60px; height: 6px; background: #f3f4f6; border-radius: 10px; overflow: hidden;">
                            <div class="js-progress-bar" data-width="{{ number_format($achievements['persentase'], 1) }}" style="height: 100%; background: linear-gradient(90deg, #10B981, #059669); border-radius: 10px; transition: width 0.6s ease;"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($achievements['daftar'] as $ach)
                        @php $isUnlocked = $ach['status'] === 'tercapai'; @endphp
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 border {{ $isUnlocked ? 'border-success bg-light-success' : 'border-color bg-light' }}" style="transition: all 0.15s ease;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 js-achievement-badge" 
                                     data-bg="{{ $isUnlocked ? $ach['warna'] : '#e5e7eb' }}"
                                     data-text-color="{{ $isUnlocked ? '#ffffff' : '#9ca3af' }}"
                                     style="width: 44px; height: 44px;">
                                    <i class="{{ $ach['ikon'] }}" style="font-size: 1.3rem; color: white;"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $ach['judul'] }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $ach['deskripsi'] }}</div>
                                    @if($isUnlocked && $ach['tercapai_pada'])
                                        <div class="text-success d-flex align-items-center gap-1 mt-1" style="font-size: 0.65rem;">
                                            <i class="ph-fill ph-check-circle"></i> Tercapai
                                        </div>
                                    @elseif(!$isUnlocked)
                                        <div class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size: 0.65rem;">
                                            <i class="ph ph-lock"></i> Belum Tercapai
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PENGATURAN PROFIL --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <h6 class="font-poppins fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <i class="ph-fill ph-gear-six text-primary"></i> Pengaturan Profil
                </h6>

                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf

                    {{-- INFORMASI AKUN --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-color">
                            <i class="ph-fill ph-user-circle text-primary"></i>
                            <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Informasi Akun</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-dark">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-color text-muted"><i class="ph ph-user"></i></span>
                                    <input type="text" name="nama" class="form-control form-control-modern" value="{{ old('nama', $user->nama) }}" required minlength="3" id="fieldNama">
                                </div>
                                <div class="form-text validation-hint d-none" id="namaHint"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-dark">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-color text-muted"><i class="ph ph-envelope"></i></span>
                                    <input type="email" name="email" class="form-control form-control-modern" value="{{ old('email', $user->email) }}" required id="fieldEmail">
                                </div>
                                <div class="form-text validation-hint d-none" id="emailHint"></div>
                            </div>
                        </div>
                    </div>

                    {{-- FOTO PROFIL --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-color">
                            <i class="ph-fill ph-camera text-primary"></i>
                            <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Foto Profil</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div id="avatarPreviewContainer" class="position-relative flex-shrink-0" style="width: 72px; height: 72px;">
                                @if($user->foto_url)
                                    <img id="avatarPreviewImg" src="{{ $user->foto_url }}" class="rounded-circle w-100 h-100 object-fit-cover border border-color" style="width: 72px; height: 72px;">
                                @else
                                    <div id="avatarPreviewInit" class="rounded-circle w-100 h-100 bg-primary text-white d-flex align-items-center justify-content-center fw-bold font-poppins" style="font-size: 1.75rem;">{{ $user->inisial }}</div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input class="form-control form-control-modern" type="file" name="avatar" accept="image/*" id="avatarInput">
                                <div class="form-text">Format: JPG, PNG, GIF. Maks 2MB. Preview otomatis setelah pilih file.</div>
                            </div>
                        </div>
                    </div>

                    {{-- KEAMANAN AKUN --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-color">
                            <i class="ph-fill ph-shield-check text-primary"></i>
                            <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Keamanan Akun</span>
                            <span class="badge bg-light-warning text-warning rounded-pill ms-auto" style="font-size: 0.6rem; font-weight: 500;">Opsional</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-dark">Password Lama</label>
                                <div class="input-group">
                                    <input type="password" name="password_lama" class="form-control form-control-modern password-field" placeholder="Masukkan password lama" autocomplete="off">
                                    <button class="btn btn-outline-modern border-color toggle-password" type="button" style="background: var(--bg-main);"><i class="ph ph-eye-slash"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-dark">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" name="password_baru" class="form-control form-control-modern password-field" placeholder="Min. 6 karakter" minlength="6" autocomplete="off">
                                    <button class="btn btn-outline-modern border-color toggle-password" type="button" style="background: var(--bg-main);"><i class="ph ph-eye-slash"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-dark">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_baru_confirmation" class="form-control form-control-modern password-field" placeholder="Ulangi password baru" autocomplete="off">
                                    <button class="btn btn-outline-modern border-color toggle-password" type="button" style="background: var(--bg-main);"><i class="ph ph-eye-slash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 border-top border-color mt-4">
                        <button type="submit" class="btn btn-modern rounded-pill px-4 px-md-5 shadow-sm d-flex align-items-center gap-2" id="submitBtn" style="background: var(--primary); color: white; border: none; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
                            <i class="ph-fill ph-floppy-disk"></i>
                            <span id="submitText">Simpan Perubahan</span>
                            <div class="spinner-border spinner-border-sm d-none" role="status" id="submitSpinner">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
    .heatmap-level-0 { background-color: var(--bg-card); }
    [data-theme="dark"] .heatmap-level-0 { background-color: #374151; }
    .heatmap-level-1 { background-color: #a7f3d0; }
    .heatmap-level-2 { background-color: #34d399; }
    .heatmap-level-3 { background-color: #059669; }
    .heatmap-level-4 { background-color: #064e3b; }
    [data-theme="green"] .heatmap-level-1 { background-color: #a7f3d0; }
    [data-theme="green"] .heatmap-level-2 { background-color: #34d399; }
    [data-theme="green"] .heatmap-level-3 { background-color: #059669; }
    [data-theme="green"] .heatmap-level-4 { background-color: #064e3b; }

    .heatmap-wrapper::-webkit-scrollbar { height: 6px; }
    .heatmap-wrapper::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
    .heatmap-wrapper::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
    .heatmap-wrapper::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.3); }
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }

    .validation-hint { font-size: 0.75rem; margin-top: 0.25rem; }
    .validation-hint.text-danger { color: #dc3545; }
    .validation-hint.text-success { color: var(--success); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

        var wrapper = document.querySelector('.heatmap-wrapper');
        if (wrapper) { wrapper.scrollLeft = wrapper.scrollWidth; }

        // Avatar preview
        var avatarInput = document.getElementById('avatarInput');
        if (avatarInput) {
            avatarInput.addEventListener('change', function (e) {
                var file = e.target.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (event) {
                    var container = document.getElementById('avatarPreviewContainer');
                    container.innerHTML = '<img id="avatarPreviewImg" src="' + event.target.result + '" class="rounded-circle w-100 h-100 object-fit-cover border border-color" style="width: 72px; height: 72px;">';
                };
                reader.readAsDataURL(file);
            });
        }

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = this.closest('.input-group').querySelector('.password-field');
                if (!input) return;
                var icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'ph ph-eye';
                } else {
                    input.type = 'password';
                    icon.className = 'ph ph-eye-slash';
                }
            });
        });

        // Apply dynamic progress bar widths
        document.querySelectorAll('.js-progress-bar').forEach(function(el) {
            el.style.width = el.getAttribute('data-width') + '%';
        });

        // Apply dynamic achievement badge styles
        document.querySelectorAll('.js-achievement-badge').forEach(function(el) {
            el.style.background = el.getAttribute('data-bg');
            el.style.color = el.getAttribute('data-text-color');
        });

        // Loading state on form submit
        var form = document.getElementById('profileForm');
        if (form) {
            form.addEventListener('submit', function () {
                var btn = document.getElementById('submitBtn');
                var text = document.getElementById('submitText');
                var spinner = document.getElementById('submitSpinner');
                if (btn) btn.disabled = true;
                if (text) text.textContent = 'Menyimpan...';
                if (spinner) spinner.classList.remove('d-none');
            });
        }
    });
</script>
@endsection
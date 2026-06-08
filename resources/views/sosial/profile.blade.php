@extends('layouts.app')

@section('title', 'Profil ' . $user->nama . ' - Sakuin')

@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 font-poppins fw-bold text-dark">
                <i class="ph-fill ph-user-circle text-success me-2"></i> Profil Pengguna
            </h4>
            <small class="text-muted">{{ $isOwnProfile ? 'Ini adalah profil kamu' : 'Profil ' . $user->nama }}</small>
        </div>
        @if($isOwnProfile)
            <a href="{{ route('profil.index') }}" class="btn btn-modern rounded-pill px-4 shadow-sm d-flex align-items-center gap-2" style="background: var(--primary); color: white; border: none;">
                <i class="ph-fill ph-pencil-simple"></i> Edit Profil
            </a>
        @endif
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

    <div class="row g-4">
        {{-- LEFT COLUMN --}}
        <div class="col-xl-4 col-lg-5 d-flex flex-column gap-4">
            {{-- HERO CARD --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex flex-column align-items-center text-center gap-3">
                    @if($user->foto_url)
                        <img src="{{ $user->foto_url }}" alt="{{ $user->inisial }}" class="rounded-circle border border-color" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-poppins fw-bold" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ $user->inisial }}
                        </div>
                    @endif
                    <div>
                        <h5 class="font-poppins fw-bold text-dark mb-1">{{ $user->nama }}</h5>
                        <p class="text-muted mb-1" style="font-size: 0.85rem;">{{ '@' . $user->username }}</p>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1 px-3 py-2"
                                  style="font-size: 0.75rem; background: {{ $badge['warna'] }}20; color: {{ $badge['warna'] }}; border: 1px solid {{ $badge['warna'] }}40;">
                                <span>{{ $badge['ikon'] }}</span>
                                <span class="fw-bold">{{ $badge['nama'] }}</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <span class="badge bg-light-primary text-primary rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                <i class="ph-fill ph-calendar-blank" style="font-size: 0.7rem;"></i> Bergabung {{ $tanggalBergabung->translatedFormat('M Y') }}
                            </span>
                            <span class="badge bg-light-success text-success rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                <i class="ph-fill ph-star" style="font-size: 0.7rem;"></i> {{ $hariBergabung }} hari
                            </span>
                        </div>
                    </div>
                </div>

                @if(!$isOwnProfile)
                    <hr class="border-color my-3">
                    <div class="d-flex justify-content-center">
                        @if($friendStatus === 'friends')
                            <form action="{{ route('friends.remove', $user->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus pertemanan dengan {{ $user->nama }}?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-4 d-flex align-items-center gap-2">
                                    <i class="ph ph-user-minus"></i> Hapus Teman
                                </button>
                            </form>
                        @elseif($friendStatus === 'pending')
                            <button class="btn btn-outline-modern rounded-pill px-4 d-flex align-items-center gap-2" disabled>
                                <i class="ph ph-clock"></i> Permintaan Terkirim
                            </button>
                        @elseif($friendStatus === 'awaiting')
                            <div class="d-flex gap-2">
                                <form action="{{ route('friends.accept', $pendingFriendRequest->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-modern rounded-pill px-4 d-flex align-items-center gap-2" style="background: var(--primary); color: white; border: none;">
                                        <i class="ph ph-user-check"></i> Terima
                                    </button>
                                </form>
                                <form action="{{ route('friends.reject', $pendingFriendRequest->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 d-flex align-items-center gap-2">
                                        <i class="ph ph-x"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('friends.send', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-modern rounded-pill px-4 d-flex align-items-center gap-2" style="background: var(--primary); color: white; border: none;">
                                    <i class="ph ph-user-plus"></i> Tambah Teman
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if($isOwnProfile)
                    <hr class="border-color my-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small d-flex align-items-center gap-1">
                            <i class="ph-fill ph-wallet text-primary"></i> Saldo Tersedia
                        </span>
                        <span class="fw-bold text-dark fs-5 {{ ($hideBalance ?? false) ? 'saldo-hidden' : '' }}">{{ $hideBalance ? '••••••••' : format_currency($saldoSaatIni) }}</span>
                    </div>
                @endif
            </div>

            {{-- STATS --}}
            <div class="row g-2">
                <div class="col-6">
                    <div class="fintech-card p-3 rounded-4 border-color text-center h-100">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <i class="ph-fill ph-fire text-danger" style="font-size: 1.2rem;"></i>
                            <span class="fw-bold text-dark" style="font-size: 1.5rem;">{{ $user->streak_saat_ini }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.65rem;">Streak Saat Ini</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="fintech-card p-3 rounded-4 border-color text-center h-100">
                        <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                            <i class="ph-fill ph-trophy text-warning" style="font-size: 1.2rem;"></i>
                            <span class="fw-bold text-dark" style="font-size: 1.5rem;">{{ $user->streak_terbaik }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.65rem;">Rekor Streak</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="fintech-card p-3 rounded-4 border-color text-center h-100">
                        <span class="fw-bold text-dark d-block" style="font-size: 1.2rem;">{{ $totalTarget }}</span>
                        <span class="text-muted" style="font-size: 0.6rem;">Total Target</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="fintech-card p-3 rounded-4 border-color text-center h-100">
                        <span class="fw-bold text-success d-block" style="font-size: 1.2rem;">{{ $targetTercapai }}</span>
                        <span class="text-muted" style="font-size: 0.6rem;">Tercapai</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="fintech-card p-3 rounded-4 border-color text-center h-100">
                        <span class="fw-bold text-primary d-block {{ ($hideBalance ?? false) ? 'saldo-hidden' : '' }}" style="font-size: 1.2rem;">{{ $hideBalance ? '••••••••' : ($totalMenabung > 0 ? format_currency($totalMenabung) : 'Rp0') }}</span>
                        <span class="text-muted" style="font-size: 0.6rem;">Total Nabung</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-xl-8 col-lg-7 d-flex flex-column gap-4">
            {{-- HEATMAP --}}
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

                <div class="d-flex align-items-center justify-content-center mb-3">
                    <span class="fw-bold text-dark px-4 py-2 bg-light rounded-3" style="font-size: 1rem;">
                        <i class="ph-fill ph-fire text-danger me-1"></i> {{ $user->streak_saat_ini }} Hari Streak
                    </span>
                </div>

                <div class="heatmap-wrapper position-relative w-100 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                    <div class="d-inline-flex flex-column" style="min-width: 800px;">
                        <div class="d-flex mb-1" style="margin-left: 25px;">
                            @php
                                $renderedMonths = [];
                                $colCount = 0;
                            @endphp
                            @foreach(array_chunk($heatmapData, 7) as $weekData)
                                @php
                                    $colCount++;
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
                                <div style="width: 14px; margin-right: 2px;">
                                    @if($showMonth)
                                        <span class="text-muted" style="font-size: 0.65rem; position: absolute;">{{ $monthName }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex">
                            <div class="d-flex flex-column justify-content-between text-muted me-2" style="font-size: 0.65rem; height: 105px; padding-top: 5px;">
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
                        <small class="text-muted">{{ $achievements['total_tercapai'] }} dari {{ $achievements['total_semua'] }}</small>
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
                            <div class="d-flex align-items-center gap-3 p-3 rounded-4 border {{ $isUnlocked ? 'border-success bg-light-success' : 'border-color bg-light' }}">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 44px; height: 44px; background: {{ $isUnlocked ? $ach['warna'] : '#e5e7eb' }}; color: white;">
                                    <i class="{{ $ach['ikon'] }}" style="font-size: 1.3rem;"></i>
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

    .heatmap-wrapper::-webkit-scrollbar { height: 6px; }
    .heatmap-wrapper::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
    .heatmap-wrapper::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
    .heatmap-wrapper::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.3); }
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

        var wrapper = document.querySelector('.heatmap-wrapper');
        if (wrapper) { wrapper.scrollLeft = wrapper.scrollWidth; }

        document.querySelectorAll('.js-progress-bar').forEach(function(el) {
            el.style.width = el.getAttribute('data-width') + '%';
        });
    });
</script>
@endsection

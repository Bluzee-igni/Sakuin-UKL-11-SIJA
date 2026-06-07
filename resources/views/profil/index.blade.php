@extends('layouts.app')

@section('title', 'Profil Pengguna - Sakuin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 font-poppins fw-bold text-dark">Profil Pengguna</h4>
    </div>

    {{-- FLASH MESSAGES --}}
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
        {{-- KOLOM KIRI: INFO PROFIL SINGKAT --}}
        <div class="col-xl-4 col-lg-5">
            <div class="fintech-card p-4 rounded-4 text-center border-color h-100 d-flex flex-column">
                <div class="position-relative mx-auto mb-3" style="width: 120px; height: 120px;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm border border-4 border-white">
                    @else
                        <div class="rounded-circle w-100 h-100 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm border border-4 border-white font-poppins fw-bold" style="font-size: 3rem;">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                        </div>
                    @endif
                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 20px; height: 20px; border-width: 3px !important;" title="Active"></div>
                </div>
                
                <h4 class="font-poppins fw-bold text-dark mb-1">{{ $user->nama }}</h4>
                <p class="text-muted mb-4">{{ $user->email }}</p>
                
                <div class="bg-light rounded-4 p-3 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Saldo:</span>
                        <span class="fw-bold text-dark {{ isset($modePrivasi) && $modePrivasi ? 'privasi-sensitif' : '' }}">{{ format_currency($saldo) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Bergabung sejak:</span>
                        <span class="fw-bold text-dark">{{ $tanggalBergabung->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-3 border-top border-color">
                    <p class="small text-muted mb-0">
                        <i class="ph-fill ph-calendar-star text-warning me-1"></i> Telah menjadi member selama <strong>{{ $hariBergabung }} hari</strong>.
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: GITHUB HEATMAP & FORM EDIT --}}
        <div class="col-xl-8 col-lg-7 d-flex flex-column gap-4">
            
            {{-- GITHUB-STYLE HEATMAP (365 HARI) --}}
            <div class="fintech-card p-4 rounded-4 border-color">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="font-poppins fw-bold text-dark mb-0">Aktivitas Menabung (1 Tahun Terakhir)</h6>
                        <small class="text-muted">Konsistensi harianmu terekam di sini</small>
                    </div>
                    <div class="icon-container bg-light-primary text-primary rounded-circle" style="width: 36px; height: 36px;">
                        <i class="ph-fill ph-git-commit fs-5"></i>
                    </div>
                </div>
                
                <div class="heatmap-wrapper position-relative w-100 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                    <div class="d-inline-flex flex-column" style="min-width: 800px;">
                        {{-- Label Bulan --}}
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
                            {{-- Label Hari (Kiri) --}}
                            <div class="d-flex flex-column justify-content-between text-muted me-2" style="font-size: 0.65rem; height: 105px; padding-top: 5px;">
                                <div style="visibility: hidden;">Sun</div>
                                <div>Mon</div>
                                <div style="visibility: hidden;">Tue</div>
                                <div>Wed</div>
                                <div style="visibility: hidden;">Thu</div>
                                <div>Fri</div>
                                <div style="visibility: hidden;">Sat</div>
                            </div>

                            {{-- Grid Kotak --}}
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

            {{-- FORM EDIT PROFIL --}}
            <div class="fintech-card p-4 rounded-4 border-color flex-grow-1">
                <h6 class="font-poppins fw-bold text-dark mb-4">Pengaturan Profil</h6>
                
                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-dark">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-color text-muted"><i class="ph ph-user"></i></span>
                                <input type="text" name="nama" class="form-control form-control-modern" value="{{ old('nama', $user->nama) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-dark">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-color text-muted"><i class="ph ph-envelope"></i></span>
                                <input type="email" name="email" class="form-control form-control-modern" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-medium text-dark">Ubah Avatar</label>
                        <input class="form-control form-control-modern" type="file" name="avatar" accept="image/*">
                        <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</div>
                    </div>

                    <hr class="border-color my-4">

                    <h6 class="font-poppins fw-bold text-dark mb-3">Ubah Password (Opsional)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-dark">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control form-control-modern" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-dark">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control form-control-modern" placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium text-dark">Konfirmasi Password</label>
                            <input type="password" name="password_baru_confirmation" class="form-control form-control-modern" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-modern rounded-pill px-4">
                            <i class="ph ph-floppy-disk me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
    /* GitHub Heatmap Styles specifically for profile */
    .heatmap-level-0 { background-color: var(--bg-card); }
    [data-theme="dark"] .heatmap-level-0 { background-color: #374151; }
    
    .heatmap-level-1 { background-color: #a7f3d0; } /* emerald-200 */
    .heatmap-level-2 { background-color: #34d399; } /* emerald-400 */
    .heatmap-level-3 { background-color: #059669; } /* emerald-600 */
    .heatmap-level-4 { background-color: #064e3b; } /* emerald-900 */
    
    [data-theme="green"] .heatmap-level-1 { background-color: #a7f3d0; }
    [data-theme="green"] .heatmap-level-2 { background-color: #34d399; }
    [data-theme="green"] .heatmap-level-3 { background-color: #059669; }
    [data-theme="green"] .heatmap-level-4 { background-color: #064e3b; }
    
    .heatmap-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .heatmap-wrapper::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }
    .heatmap-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.15);
        border-radius: 10px;
    }
    .heatmap-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.3);
    }
    
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    [data-theme="dark"] .heatmap-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips for heatmap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Auto scroll heatmap to the right (most recent)
        const wrapper = document.querySelector('.heatmap-wrapper');
        if (wrapper) {
            wrapper.scrollLeft = wrapper.scrollWidth;
        }
    });
</script>
@endsection

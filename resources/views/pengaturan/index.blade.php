@extends('layouts.app')

@section('title', 'Pengaturan')

@push('styles')
<style>
    .settings-container {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }
    
    .settings-sidebar {
        flex: 0 0 250px;
        position: sticky;
        top: 90px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1rem 0;
        box-shadow: var(--shadow-sm);
    }
    
    .settings-content {
        flex: 1;
        min-width: 0;
    }

    .settings-nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: var(--text-muted);
        font-weight: 500;
        text-decoration: none;
        transition: all var(--transition-fast);
        border-left: 3px solid transparent;
    }

    .settings-nav-link:hover {
        background-color: var(--bg-main);
        color: var(--text-main);
    }

    .settings-nav-link.active {
        color: var(--primary);
        background-color: var(--primary-light);
        border-left-color: var(--primary);
        font-weight: 600;
    }

    .settings-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        scroll-margin-top: 100px; /* Offset for sticky header */
    }

    .settings-section-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .settings-section-desc {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 2rem;
    }

    /* Theme Cards */
    .theme-card-wrapper {
        position: relative;
        cursor: pointer;
    }

    .theme-card-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .theme-card {
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        background: var(--bg-card);
    }

    .theme-card-input:checked + .theme-card {
        border-color: var(--primary);
        background-color: var(--primary-light);
        box-shadow: 0 0 0 1px var(--primary);
    }

    .theme-card-preview {
        height: 80px;
        border-radius: 6px;
        margin-bottom: 1rem;
        border: 1px solid rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .theme-card-preview-header { height: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); }
    .theme-card-preview-body { flex: 1; padding: 0.5rem; display: flex; gap: 0.5rem; }
    .theme-card-preview-sidebar { width: 30%; height: 100%; border-radius: 4px; }
    .theme-card-preview-main { flex: 1; height: 100%; border-radius: 4px; }

    /* Theme Previews */
    .preview-light { background: #f3f4f6; }
    .preview-light .theme-card-preview-header { background: #ffffff; }
    .preview-light .theme-card-preview-sidebar { background: #e5e7eb; }
    .preview-light .theme-card-preview-main { background: #ffffff; }

    .preview-dark { background: #111827; }
    .preview-dark .theme-card-preview-header { background: #1f2937; border-color: rgba(255,255,255,0.05); }
    .preview-dark .theme-card-preview-sidebar { background: #374151; }
    .preview-dark .theme-card-preview-main { background: #1f2937; }

    .preview-green { background: #ecfdf5; }
    .preview-green .theme-card-preview-header { background: #059669; border-color: rgba(0,0,0,0.1); }
    .preview-green .theme-card-preview-sidebar { background: #a7f3d0; }
    .preview-green .theme-card-preview-main { background: #ffffff; border: 1px solid #d1fae5; }

    /* Custom Switches */
    .switch-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        transition: border-color 0.2s ease;
    }
    
    .switch-modern:hover {
        border-color: var(--primary-light);
        background-color: rgba(5, 150, 105, 0.02);
    }

    /* Sortable List */
    .sortable-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sortable-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        margin-bottom: 0.5rem;
        cursor: grab;
        transition: box-shadow 0.2s ease;
    }
    
    .sortable-item:active {
        cursor: grabbing;
    }
    
    .sortable-item.sortable-ghost {
        opacity: 0.4;
        background: var(--bg-main);
    }

    .drag-handle {
        color: var(--text-muted);
        cursor: grab;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .settings-container {
            flex-direction: column;
        }
        .settings-sidebar {
            flex: none;
            width: 100%;
            position: static;
            padding: 0.5rem;
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
        }
        .settings-nav-link {
            padding: 0.75rem 1rem;
            border-left: none;
            border-bottom: 3px solid transparent;
            border-radius: var(--radius-md);
        }
        .settings-nav-link.active {
            border-bottom-color: var(--primary);
        }
    }
</style>
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endpush

@section('content')
<div class="container-fluid p-0">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold font-poppins text-dark">Pengaturan</h4>
            <p class="text-muted small mb-0 mt-1">Sesuaikan pengalaman Sakuin sesuai keinginanmu.</p>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert bg-light-success text-success border border-success border-opacity-25 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="ph-fill ph-check-circle fs-4"></i>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="settings-container">
        
        {{-- SIDEBAR NAV --}}
        <aside class="settings-sidebar">
            <nav class="nav flex-column" id="settings-nav">
                <a class="settings-nav-link active" href="#tampilan" data-target="tampilan">
                    <i class="ph ph-palette fs-5"></i> Tampilan
                </a>
                <a class="settings-nav-link" href="#dashboard" data-target="dashboard">
                    <i class="ph ph-squares-four fs-5"></i> Dashboard
                </a>
                <a class="settings-nav-link" href="#notifikasi" data-target="notifikasi">
                    <i class="ph ph-bell fs-5"></i> Notifikasi
                </a>
                <a class="settings-nav-link" href="#keuangan" data-target="keuangan">
                    <i class="ph ph-wallet fs-5"></i> Keuangan
                </a>
                <a class="settings-nav-link" href="#privasi" data-target="privasi">
                    <i class="ph ph-shield-check fs-5"></i> Privasi
                </a>
                <a class="settings-nav-link" href="#tentang" data-target="tentang">
                    <i class="ph ph-info fs-5"></i> Tentang
                </a>
            </nav>
        </aside>

        {{-- CONTENT SECTIONS --}}
        <main class="settings-content">
            
            {{-- 1. TAMPILAN --}}
            <section id="tampilan" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-palette text-primary"></i> Tampilan</h5>
                <p class="settings-section-desc">Pilih tema dan preferensi visual aplikasi.</p>

                <form action="{{ route('pengaturan.tampilan') }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Tema Aplikasi</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="theme-card-wrapper w-100">
                                <input type="radio" name="tema" value="light" class="theme-card-input" {{ ($settings['tema'] ?? 'light') == 'light' ? 'checked' : '' }}>
                                <div class="theme-card">
                                    <div class="theme-card-preview preview-light">
                                        <div class="theme-card-preview-header"></div>
                                        <div class="theme-card-preview-body">
                                            <div class="theme-card-preview-sidebar"></div>
                                            <div class="theme-card-preview-main"></div>
                                        </div>
                                    </div>
                                    <span class="fw-medium text-dark">Light Mode</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="theme-card-wrapper w-100">
                                <input type="radio" name="tema" value="dark" class="theme-card-input" {{ ($settings['tema'] ?? 'light') == 'dark' ? 'checked' : '' }}>
                                <div class="theme-card">
                                    <div class="theme-card-preview preview-dark">
                                        <div class="theme-card-preview-header"></div>
                                        <div class="theme-card-preview-body">
                                            <div class="theme-card-preview-sidebar"></div>
                                            <div class="theme-card-preview-main"></div>
                                        </div>
                                    </div>
                                    <span class="fw-medium text-dark">Dark Mode</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="theme-card-wrapper w-100">
                                <input type="radio" name="tema" value="green" class="theme-card-input" {{ ($settings['tema'] ?? 'light') == 'green' ? 'checked' : '' }}>
                                <div class="theme-card">
                                    <div class="theme-card-preview preview-green">
                                        <div class="theme-card-preview-header"></div>
                                        <div class="theme-card-preview-body">
                                            <div class="theme-card-preview-sidebar"></div>
                                            <div class="theme-card-preview-main"></div>
                                        </div>
                                    </div>
                                    <span class="fw-medium text-dark">Sakuin Green</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Preferensi Visual</h6>
                    
                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Compact Mode</div>
                            <div class="small text-muted">Kurangi jarak (padding/margin) agar layar memuat lebih banyak informasi.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="compact_mode" value="1" {{ ($settings['compact_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Animasi Dashboard</div>
                            <div class="small text-muted">Aktifkan efek transisi, fade, dan hover. Nonaktifkan pada perangkat lambat.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="animasi_aktif" value="1" {{ ($settings['animasi_aktif'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success-modern px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- 2. DASHBOARD --}}
            <section id="dashboard" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-squares-four text-primary"></i> Dashboard</h5>
                <p class="settings-section-desc">Atur tata letak dan widget apa saja yang muncul di dashboard utama.</p>

                <form action="{{ route('pengaturan.dashboard') }}" method="POST" id="formDashboard">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Visibilitas Widget</h6>
                            
                            <div class="switch-modern py-2">
                                <span class="fw-medium text-dark">Tampilkan Heatmap Aktivitas</span>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tampil_heatmap" value="1" {{ ($settings['tampil_heatmap'] ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="switch-modern py-2">
                                <span class="fw-medium text-dark">Tampilkan Streak Konsistensi</span>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tampil_streak" value="1" {{ ($settings['tampil_streak'] ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="switch-modern py-2">
                                <span class="fw-medium text-dark">Tampilkan Target Tambahan</span>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tampil_target" value="1" {{ ($settings['tampil_target'] ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="switch-modern py-2">
                                <span class="fw-medium text-dark">Tampilkan Riwayat Transaksi</span>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tampil_riwayat" value="1" {{ ($settings['tampil_riwayat'] ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="switch-modern py-2">
                                <span class="fw-medium text-dark">Tampilkan Ringkasan Statistik</span>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="tampil_statistik" value="1" {{ ($settings['tampil_statistik'] ?? '1') == '1' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Urutan Widget (Mobile View)</h6>
                            <p class="small text-muted mb-2">Tarik & lepaskan (Drag & Drop) untuk mengatur urutan widget pada layar kecil.</p>
                            
                            <input type="hidden" name="urutan_widget" id="urutanWidgetInput" value='{{ $settings['urutan_widget'] ?? '[]' }}'>
                            
                            @php
                                $urutanDefault = [
                                    'saldo' => 'Total Saldo',
                                    'streak' => 'Motivasi Streak',
                                    'target_form' => 'Target & Form Nabung',
                                    'heatmap' => 'Heatmap Aktivitas',
                                    'stats' => 'Ringkasan Statistik',
                                    'target_list' => 'Daftar Target Lainnya',
                                    'riwayat' => 'Riwayat Terakhir'
                                ];
                                $urutanTersimpan = json_decode($settings['urutan_widget'] ?? '[]', true);
                                if(empty($urutanTersimpan)) $urutanTersimpan = array_keys($urutanDefault);
                            @endphp

                            <ul class="sortable-list" id="widgetSortable">
                                @foreach($urutanTersimpan as $key)
                                    @if(isset($urutanDefault[$key]))
                                    <li class="sortable-item" data-id="{{ $key }}">
                                        <i class="ph ph-dots-six-vertical fs-5 drag-handle"></i>
                                        <span class="fw-medium text-dark">{{ $urutanDefault[$key] }}</span>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success-modern px-4" onclick="updateWidgetOrder()">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- 3. NOTIFIKASI --}}
            <section id="notifikasi" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-bell text-primary"></i> Notifikasi</h5>
                <p class="settings-section-desc">Atur pemberitahuan dan pengingat (reminder) cerdas dari Sakuin.</p>

                <form action="{{ route('pengaturan.notifikasi') }}" method="POST">
                    @csrf
                    
                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Pengingat Menabung Harian</div>
                            <div class="small text-muted">Tampilkan pesan motivasi jika Anda belum menabung hari ini untuk menjaga Streak.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_menabung" value="1" {{ ($settings['notif_menabung'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>
                    
                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Notifikasi Progres Target</div>
                            <div class="small text-muted">Pemberitahuan saat target tabungan sudah mendekati pencapaian (misal tinggal 10% lagi).</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_target" value="1" {{ ($settings['notif_target'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Pengingat Hari Gajian</div>
                            <div class="small text-muted">Ucapan selamat saat hari gajian otomatis tiba.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_gajian" value="1" {{ ($settings['notif_gajian'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="switch-modern">
                        <div>
                            <div class="fw-bold text-dark">Peringatan Budget Bulanan</div>
                            <div class="small text-muted">Muncul saat total pengeluaran bulan ini hampir mendekati/melebihi budget.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="notif_budget" value="1" {{ ($settings['notif_budget'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success-modern px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- 4. KEUANGAN --}}
            <section id="keuangan" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-wallet text-primary"></i> Keuangan</h5>
                <p class="settings-section-desc">Atur mata uang, gaji otomatis, dan sistem alokasi tabungan pintar.</p>

                <form action="{{ route('pengaturan.keuangan') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Preferensi Dasar</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-dark small">Mata Uang Utama</label>
                                <select class="form-select form-control-modern" name="mata_uang">
                                    <option value="IDR" {{ $user->mata_uang == 'IDR' ? 'selected' : '' }}>Indonesian Rupiah (IDR)</option>
                                    <option value="USD" {{ $user->mata_uang == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="EUR" {{ $user->mata_uang == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    <option value="GBP" {{ $user->mata_uang == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                    <option value="JPY" {{ $user->mata_uang == 'JPY' ? 'selected' : '' }}>Japanese Yen (JPY)</option>
                                    <option value="MYR" {{ $user->mata_uang == 'MYR' ? 'selected' : '' }}>Malaysian Ringgit (MYR)</option>
                                    <option value="SGD" {{ $user->mata_uang == 'SGD' ? 'selected' : '' }}>Singapore Dollar (SGD)</option>
                                    <option value="AUD" {{ $user->mata_uang == 'AUD' ? 'selected' : '' }}>Australian Dollar (AUD)</option>
                                    <option value="SAR" {{ $user->mata_uang == 'SAR' ? 'selected' : '' }}>Saudi Riyal (SAR)</option>
                                    <option value="KRW" {{ $user->mata_uang == 'KRW' ? 'selected' : '' }}>South Korean Won (KRW)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium text-dark small">Target Tabungan Default</label>
                                <select class="form-select form-control-modern" name="target_default_id">
                                    <option value="">Pilih Target Default...</option>
                                    @foreach($targets as $tg)
                                        <option value="{{ $tg->id }}" {{ ($settings['target_default_id'] ?? '') == $tg->id ? 'selected' : '' }}>{{ $tg->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text small">Target yang otomatis menerima dana dari potongan gaji otomatis.</div>
                            </div>
                        </div>

                        <div class="col-md-6 border-start-md ps-md-4">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted d-flex align-items-center gap-2">
                                Gaji Otomatis <span class="badge bg-light-primary text-primary rounded-pill">Automation</span>
                            </h6>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <label class="form-label fw-medium text-dark small">Tgl (1-31)</label>
                                    <input type="number" class="form-control form-control-modern" name="gaji_tanggal" min="1" max="31" value="{{ $gajiOtomatis->tanggal_rutin ?? '' }}" placeholder="25">
                                </div>
                                <div class="col-8">
                                    <label class="form-label fw-medium text-dark small">Nominal Gaji</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-color border-end-0 fw-bold">{{ $currencySymbol }}</span>
                                        <input type="number" class="form-control form-control-modern border-start-0 ps-0" name="gaji_nominal" value="{{ $gajiOtomatis->jumlah ?? '' }}" placeholder="5000000">
                                    </div>
                                </div>
                            </div>

                            <div class="switch-modern mb-2 p-2">
                                <div>
                                    <div class="fw-bold text-dark small">Alokasi Tabungan Otomatis</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Potong gaji langsung ke target default.</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="alokasi_aktif" value="1" {{ ($settings['alokasi_aktif'] ?? '0') == '1' ? 'checked' : '' }} id="alokasiToggle">
                                </div>
                            </div>
                            
                            <div class="mb-3" id="alokasiSliderWrapper" style="display: {{ ($settings['alokasi_aktif'] ?? '0') == '1' ? 'block' : 'none' }};">
                                <label class="form-label fw-medium text-dark small d-flex justify-content-between">
                                    <span>Persentase Potongan</span>
                                    <span class="text-primary fw-bold" id="alokasiValue">{{ $settings['alokasi_persen'] ?? '20' }}%</span>
                                </label>
                                <input type="range" class="form-range" name="alokasi_persen" id="alokasiSlider" min="1" max="100" value="{{ $settings['alokasi_persen'] ?? '20' }}">
                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.65rem;">
                                    <span>Gaji Bebas: <span id="sisaGajiValue">{{ 100 - ($settings['alokasi_persen'] ?? 20) }}</span>%</span>
                                    <span>Masuk Tabungan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success-modern px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- 5. PRIVASI --}}
            <section id="privasi" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-shield-check text-primary"></i> Privasi & Keamanan</h5>
                <p class="settings-section-desc">Lindungi data sensitif Anda dari pandangan orang lain.</p>

                <form action="{{ route('pengaturan.privasi') }}" method="POST">
                    @csrf
                    
                    <div class="switch-modern border-danger-subtle bg-danger-subtle bg-opacity-10">
                        <div>
                            <div class="fw-bold text-danger">🔒 Sembunyikan Semua Nominal</div>
                            <div class="small text-muted">Sembunyikan seluruh nominal uang (saldo, target tabungan, ringkasan keuangan, statistik) menjadi •••••••• di semua halaman. Cocok saat membuka aplikasi di tempat umum.</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input border-danger" type="checkbox" role="switch" name="hide_balance" value="1" {{ ($settings['hide_balance'] ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success-modern px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- 6. TENTANG --}}
            <section id="tentang" class="settings-section">
                <h5 class="settings-section-title"><i class="ph-fill ph-info text-primary"></i> Tentang Sakuin</h5>
                <p class="settings-section-desc mb-4">Informasi aplikasi dan versi rilis.</p>

                <div class="text-center mb-4 pb-4 border-bottom">
                    <img src="{{ asset('images/logo-sakuin.jpg') }}" alt="SakuinAja" class="mx-auto mb-3" style="height: 80px; width: auto; object-fit: contain;">
                    <h4 class="font-poppins fw-bold text-dark mb-1">Sakuin Aja</h4>
                    <span class="badge bg-light text-muted border border-color rounded-pill">Versi 1.0.0</span>
                </div>

                <h6 class="fw-bold text-dark small text-uppercase">Deskripsi</h6>
                <p class="small text-muted mb-4">
                    Sakuin adalah aplikasi manajemen keuangan berbasis web yang didesain khusus untuk membantu pengguna melacak pemasukan, pengeluaran, serta mewujudkan target tabungan impian dengan sentuhan gamifikasi yang memotivasi.
                </p>

                <h6 class="fw-bold text-dark small text-uppercase">Changelog (v1.0.0)</h6>
                <ul class="small text-muted">
                    <li>Rilis awal aplikasi Sakuin.</li>
                    <li>Sistem manajemen keuangan komprehensif (Income, Expense, Budget).</li>
                    <li>Sistem pelacakan Target Tabungan dengan progres.</li>
                    <li>Fitur Gamifikasi: Rekor Streak & Heatmap Aktivitas Menabung bergaya GitHub.</li>
                    <li>Halaman Pengaturan lengkap (Tampilan, Dashboard, Notifikasi, Keuangan, Privasi).</li>
                    <li>Sistem otomasi Gaji Bulanan dan Alokasi Tabungan Otomatis.</li>
                </ul>

                <div class="text-center mt-5 pt-3">
                    <p class="small text-muted mb-0">&copy; {{ date('Y') }} Sakuin App. Dibangun dengan ❤️</p>
                </div>
            </section>

        </main>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (existing code for tabs if any) ...

        const tampilanForm = document.querySelector('form[action="{{ route('pengaturan.tampilan') }}"]');
        const themeRadios = document.querySelectorAll('input[name="tema"]');
        const htmlTag = document.documentElement;
        const compactSwitch = document.querySelector('input[name="compact_mode"]');
        const animasiSwitch = document.querySelector('input[name="animasi_aktif"]');
        const bodyTag = document.body;

        // Function to auto-save settings via AJAX
        function autoSaveTampilan() {
            if (!tampilanForm) return;
            const formData = new FormData(tampilanForm);
            
            fetch(tampilanForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if(!response.ok) throw new Error('Auto-save failed');
                console.log('Settings auto-saved successfully');
            })
            .catch(error => {
                console.error('Error saving settings:', error);
            });
        }
        
        // Listeners for Theme
        themeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if(this.checked) {
                    htmlTag.setAttribute('data-theme', this.value);
                    autoSaveTampilan();
                }
            });
        });

        // Listeners for Compact Mode
        if(compactSwitch) {
            compactSwitch.addEventListener('change', function() {
                if(this.checked) {
                    bodyTag.classList.add('compact-mode');
                } else {
                    bodyTag.classList.remove('compact-mode');
                }
                autoSaveTampilan();
            });
        }

        // Listeners for Animasi
        if(animasiSwitch) {
            animasiSwitch.addEventListener('change', function() {
                if(!this.checked) {
                    bodyTag.classList.add('no-animations');
                } else {
                    bodyTag.classList.remove('no-animations');
                }
                autoSaveTampilan();
            });
        }
    });
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Smooth Scrolling & Active Link Highlighting for Sidebar ---
        const sections = document.querySelectorAll('.settings-section');
        const navLinks = document.querySelectorAll('.settings-nav-link');
        
        // Handle click on nav links
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // If it's a hash link on the same page
                if(this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    
                    if(targetSection) {
                        targetSection.scrollIntoView({ behavior: 'smooth' });
                    }
                    
                    // Update active state manually (IntersectionObserver will also do this, but this is faster for clicks)
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Use IntersectionObserver to update active link based on scroll position
        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px', // Adjust these values to change trigger point
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    
                    // Update nav links
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${id}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });

        // --- Sortable JS Init ---
        const sortableEl = document.getElementById('widgetSortable');
        if (sortableEl && typeof Sortable !== 'undefined') {
            new Sortable(sortableEl, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.drag-handle',
                onEnd: function (evt) {
                    // Update hidden input when sorting ends
                    updateWidgetOrder();
                },
            });
        }

        // --- Alokasi Slider Logic ---
        const alokasiToggle = document.getElementById('alokasiToggle');
        const alokasiSliderWrapper = document.getElementById('alokasiSliderWrapper');
        const alokasiSlider = document.getElementById('alokasiSlider');
        const alokasiValue = document.getElementById('alokasiValue');
        const sisaGajiValue = document.getElementById('sisaGajiValue');

        if (alokasiToggle && alokasiSliderWrapper) {
            alokasiToggle.addEventListener('change', function() {
                if (this.checked) {
                    alokasiSliderWrapper.style.display = 'block';
                } else {
                    alokasiSliderWrapper.style.display = 'none';
                }
            });
        }

        if (alokasiSlider) {
            alokasiSlider.addEventListener('input', function() {
                alokasiValue.textContent = this.value + '%';
                sisaGajiValue.textContent = (100 - parseInt(this.value));
            });
        }
    });

    // Function called on form submit or sort end
    function updateWidgetOrder() {
        const sortableItems = document.querySelectorAll('.sortable-item');
        const order = [];
        sortableItems.forEach(item => {
            order.push(item.getAttribute('data-id'));
        });
        
        const input = document.getElementById('urutanWidgetInput');
        if (input) {
            input.value = JSON.stringify(order);
        }
    }
</script>
@endpush

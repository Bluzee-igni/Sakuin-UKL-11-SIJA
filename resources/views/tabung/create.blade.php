@extends('layouts.app')

@section('title', 'Tambah Target Tabungan')

@push('styles')
<style>
    .upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .upload-box:hover {
        border-color: var(--primary-modern);
        background-color: #f1f5f9;
    }
    .preview-image {
        max-height: 200px;
        object-fit: cover;
        border-radius: 0.5rem;
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold font-poppins text-primary d-flex align-items-center gap-2">
                <i class="ph-fill ph-target text-success"></i> Target Impian Baru
            </h4>
            <p class="text-muted small mb-0 mt-1">Langkah pertama menuju mimpimu dimulai dari sini.</p>
        </div>
        <a href="{{ route('tabung.index') }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert bg-light-danger text-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="ph-fill ph-warning-circle fs-5"></i>
                <div class="fw-bold">Periksa kembali isian Anda:</div>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('tabung.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="fintech-card p-4 p-md-5 h-100 rounded-4 border-color">
                    <h6 class="font-poppins fw-bold mb-4 border-bottom pb-2">Informasi Target</h6>

                    <div class="mb-4">
                        <label class="form-label text-dark small fw-bold">Nama Target Impian</label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control form-control-modern bg-light border-0"
                            placeholder="Contoh: Beli Laptop Baru, Liburan ke Bali..."
                            value="{{ old('nama') }}"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-dark small fw-bold">Harga / Jumlah Target ({{ $currencySymbol }})</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-success"><i class="ph-fill ph-coins"></i></span>
                            <input type="text" name="jumlah_target" class="form-control form-control-modern border-start-0 ps-0 js-currency-format fw-bold" placeholder="Contoh: 5000000" value="{{ old('jumlah_target') }}" required>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark small fw-bold">Rencana Nabung/Hari ({{ $currencySymbol }})</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="ph ph-wallet"></i></span>
                                <input
                                    type="text"
                                    name="rencana_harian"
                                    class="form-control form-control-modern border-start-0 ps-0 js-currency-format"
                                    placeholder="Contoh: 50.000 (Opsional)"
                                    value="{{ old('rencana_harian') }}"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark small fw-bold">Tanggal Mulai</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="ph ph-calendar-blank"></i></span>
                                <input
                                    type="date"
                                    name="tanggal_mulai"
                                    class="form-control form-control-modern border-start-0 ps-0"
                                    value="{{ old('tanggal_mulai', now()->format('Y-m-d')) }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="fintech-card p-4 h-100 rounded-4 border-color d-flex flex-column">
                    <h6 class="font-poppins fw-bold mb-4 border-bottom pb-2">Visualisasi Target</h6>
                    
                    <label class="form-label text-dark small fw-bold mb-2">Unggah Gambar (Opsional)</label>
                    <p class="small text-muted mb-3" style="font-size: 0.75rem;">Tambahkan gambar untuk memotivasi kamu mencapai target ini!</p>

                    <label for="gambar" class="upload-box d-flex flex-column align-items-center justify-content-center p-4 text-center flex-grow-1 position-relative">
                        <img id="imagePreview" src="" alt="Preview" class="preview-image w-100 shadow-sm mb-2">
                        <div id="uploadPlaceholder" class="d-flex flex-column align-items-center">
                            <div class="icon-container bg-light-success text-success rounded-circle mb-2" style="width: 48px; height: 48px;">
                                <i class="ph-fill ph-image fs-4"></i>
                            </div>
                            <span class="text-primary fw-medium small">Klik untuk memilih gambar</span>
                            <span class="text-muted" style="font-size: 0.7rem;">Maks 2MB (JPG, PNG)</span>
                        </div>
                        <input type="file" name="gambar" id="gambar" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">
                    </label>

                    <button type="submit" class="btn btn-success-modern w-100 py-3 mt-4 d-flex justify-content-center align-items-center gap-2 fw-bold shadow-sm rounded-4">
                        <i class="ph-fill ph-paper-plane-tilt fs-5"></i> Mulai Menabung
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('gambar').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
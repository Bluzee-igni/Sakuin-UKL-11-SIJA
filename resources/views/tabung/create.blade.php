@extends('layouts.app')

@section('title', 'Tambah Target Tabungan')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold font-poppins text-primary d-flex align-items-center gap-2">
            <i class="ph-fill ph-target"></i> Target Baru
        </h4>
        <a href="{{ route('tabung.index') }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert bg-light-danger text-danger border-0 rounded-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <div class="fintech-card p-4 p-md-5">
        <form action="{{ route('tabung.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label text-muted small fw-medium">Nama Target</label>
                <input
                    type="text"
                    name="nama"
                    class="form-control form-control-modern"
                    placeholder="Contoh: Beli Laptop Baru"
                    value="{{ old('nama') }}"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-medium">Harga Target (Rp)</label>
                <input
                    type="text"
                    name="harga_target"
                    class="form-control form-control-modern js-currency-format"
                    placeholder="Contoh: 10.000.000"
                    value="{{ old('harga_target') }}"
                    required
                >
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-medium">Rencana Nabung/Hari (Rp)</label>
                    <input
                        type="text"
                        name="rencana_per_hari"
                        class="form-control form-control-modern js-currency-format"
                        placeholder="Contoh: 50.000 (Opsional)"
                        value="{{ old('rencana_per_hari') }}"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted small fw-medium">Tanggal Mulai</label>
                    <input
                        type="date"
                        name="mulai"
                        class="form-control form-control-modern"
                        value="{{ old('mulai', now()->format('Y-m-d')) }}"
                    >
                </div>
            </div>

            <button type="submit" class="btn btn-primary-modern w-100 py-3 d-flex justify-content-center align-items-center gap-2">
                <i class="ph ph-plus-circle fs-4"></i> Simpan Target
            </button>
        </form>
    </div>
</div>
@endsection
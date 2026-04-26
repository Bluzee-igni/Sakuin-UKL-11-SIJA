@extends('layouts.app')

@section('title', 'Tambah Pemasukan')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold font-poppins text-primary d-flex align-items-center gap-2">
            <i class="ph-fill ph-wallet"></i> Tambah Pemasukan
        </h4>
        <a href="{{ route('tabung.index') }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert bg-light-success text-success border-0 rounded-4 alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="ph-fill ph-check-circle fs-4"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert bg-light-danger text-danger border-0 rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <div class="fintech-card p-4 p-md-5">
        <form action="{{ route('incomes.store') }}" method="POST">
            @csrf

            {{-- NAMA PEMASUKAN --}}
            <div class="mb-4">
                <label class="form-label text-muted small fw-medium">Nama Pemasukan</label>
                <input type="text" name="nama" class="form-control form-control-modern"
                       placeholder="Contoh: Gaji, Bonus, Freelance" required>
            </div>

            {{-- NOMINAL --}}
            <div class="mb-4">
                <label class="form-label text-muted small fw-medium">Nominal (Rp)</label>
                <input type="text" name="nominal" class="form-control form-control-modern js-currency-format"
                       placeholder="Contoh: 3.000.000" required>
            </div>

            <div class="row g-4 mb-5">
                {{-- TANGGAL --}}
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-medium">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-modern"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>

                {{-- CATATAN --}}
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-medium">Catatan</label>
                    <input type="text" name="catatan" class="form-control form-control-modern"
                           placeholder="Opsional">
                </div>
            </div>

            {{-- BUTTON --}}
            <button type="submit" class="btn btn-primary-modern w-100 py-3 d-flex justify-content-center align-items-center gap-2">
                <i class="ph ph-plus-circle fs-4"></i> Simpan Pemasukan
            </button>
        </form>
    </div>
</div>
@endsection
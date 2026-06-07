@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Sakuin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1 font-poppins fw-bold text-dark">Riwayat Transaksi</h4>
            <p class="text-muted mb-0 small">Catatan seluruh aktivitas keuangan Anda.</p>
        </div>
    </div>

    <div class="fintech-card p-0 rounded-4 border-color overflow-hidden">
        {{-- TAB FILTER --}}
        <div class="p-3 border-bottom border-color bg-light">
            <ul class="nav nav-pills gap-2" id="history-filter-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('riwayat.index', ['filter' => 'semua']) }}" class="nav-link rounded-pill px-4 {{ $filter === 'semua' ? 'active bg-primary text-white shadow-sm' : 'bg-white border border-color text-dark' }}">Semua</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('riwayat.index', ['filter' => 'masuk']) }}" class="nav-link rounded-pill px-4 {{ $filter === 'masuk' ? 'active bg-primary text-white shadow-sm' : 'bg-white border border-color text-dark' }}">Pemasukan</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('riwayat.index', ['filter' => 'keluar']) }}" class="nav-link rounded-pill px-4 {{ $filter === 'keluar' ? 'active bg-primary text-white shadow-sm' : 'bg-white border border-color text-dark' }}">Pengeluaran</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('riwayat.index', ['filter' => 'nabung']) }}" class="nav-link rounded-pill px-4 {{ $filter === 'nabung' ? 'active bg-primary text-white shadow-sm' : 'bg-white border border-color text-dark' }}">Tabungan</a>
                </li>
            </ul>
        </div>

        {{-- DAFTAR TRANSAKSI --}}
        <div class="list-group list-group-flush">
            @forelse($transactions as $trx)
                <div class="list-group-item p-4 border-bottom border-color list-item-modern" style="cursor: default;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-container rounded-circle flex-shrink-0 bg-light-{{ $trx->tipe_badge }} text-{{ $trx->tipe_badge }}" style="width: 48px; height: 48px;">
                                <i class="ph-fill {{ $trx->ikon }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 font-poppins fw-bold text-dark">{{ $trx->judul }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-muted border border-color fw-medium" style="font-size: 0.65rem;">{{ $trx->kategori }}</span>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><i class="ph ph-calendar-blank me-1"></i>{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-md-end ms-5 ms-md-0">
                            @if($trx->kategori === 'Pengeluaran' || $trx->kategori === 'Penarikan Tabungan')
                                <h5 class="mb-0 fw-bold text-danger {{ isset($modePrivasi) && $modePrivasi ? 'privasi-sensitif' : '' }}">-{{ format_currency($trx->jumlah) }}</h5>
                            @else
                                <h5 class="mb-0 fw-bold text-success {{ isset($modePrivasi) && $modePrivasi ? 'privasi-sensitif' : '' }}">+{{ format_currency($trx->jumlah) }}</h5>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center">
                    <div class="icon-container bg-light-secondary text-muted mx-auto rounded-circle mb-3" style="width: 64px; height: 64px;">
                        <i class="ph ph-receipt fs-2"></i>
                    </div>
                    <h6 class="font-poppins fw-bold text-dark mb-1">Belum ada transaksi</h6>
                    <p class="text-muted small">Transaksi yang Anda lakukan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- PAGINATION --}}
        @if($transactions->hasPages())
        <div class="p-3 border-top border-color bg-light d-flex justify-content-center">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>
</div>
@endsection

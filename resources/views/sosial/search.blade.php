@extends('layouts.app')

@section('title', 'Cari Pengguna - Sakuin')

@push('styles')
<style>
    .user-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        padding: 1.25rem;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.75rem;
    }

    .user-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }

    [data-theme="dark"] .user-card {
        background: #1f2937;
        border-color: #374151;
    }

    .search-input-group {
        position: relative;
    }

    .search-input-group input {
        border-radius: 9999px;
        padding: 0.75rem 1.25rem 0.75rem 3rem;
        border: 2px solid #e5e7eb;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    .search-input-group input:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
    }

    .search-input-group .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1.3rem;
    }

    .search-input-group .search-clear {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #9ca3af;
        cursor: pointer;
        display: none;
    }

    .search-input-group .search-clear.visible {
        display: block;
    }

    [data-theme="dark"] .search-input-group input {
        background: #1f2937;
        border-color: #4b5563;
        color: #f9fafb;
    }

    [data-theme="dark"] .search-input-group input:focus {
        border-color: #059669;
        background: #111827;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-xl-4 py-2">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold font-poppins text-dark">
                <i class="ph-fill ph-magnifying-glass text-success me-2"></i> Cari Pengguna
            </h4>
            <p class="text-muted small mb-0 mt-1">Temukan teman dan lihat progres tabungan mereka</p>
        </div>
        <a href="{{ route('sosial.index') }}" class="btn btn-outline-modern rounded-pill d-flex align-items-center gap-2 px-3 py-2 border-color">
            <i class="ph ph-arrow-left"></i> Kembali ke Feed
        </a>
    </div>

    <div class="row justify-content-center mb-4">
        <div class="col-lg-6 col-md-8">
            <form action="{{ route('sosial.search') }}" method="GET" id="searchForm" autocomplete="off">
                <div class="search-input-group">
                    <i class="ph ph-magnifying-glass search-icon"></i>
                    <input type="text" name="q" id="searchInput" placeholder="Cari berdasarkan nama atau username..."
                           value="{{ $query ?? '' }}" autofocus>
                    <button type="button" class="search-clear" id="searchClear">
                        <i class="ph ph-x-circle"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($query) && strlen($query) > 0)
        @if($results->count() > 0)
            <div class="row g-4">
                @foreach($results as $user)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="user-card">
                            <a href="{{ route('user.profile', $user->username) }}" class="text-decoration-none">
                                @if($user->foto_url)
                                    <img src="{{ $user->foto_url }}" alt="{{ $user->inisial }}"
                                         class="rounded-circle border border-color"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-poppins fw-bold mx-auto"
                                         style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ $user->inisial }}
                                    </div>
                                @endif
                            </a>

                            <div class="min-w-0 w-100">
                                <a href="{{ route('user.profile', $user->username) }}" class="text-decoration-none">
                                    <h6 class="fw-bold text-dark mb-0">{{ $user->nama }}</h6>
                                    <p class="text-muted mb-1" style="font-size: 0.75rem;">{{ '@' . $user->username }}</p>
                                </a>
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <span class="streak-badge d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 600; color: #ef4444;">
                                        <i class="ph-fill ph-fire"></i>{{ $user->streak_saat_ini }}
                                    </span>
                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1 px-2 py-1"
                                          style="font-size: 0.65rem; background: {{ $user->badge['warna'] }}20; color: {{ $user->badge['warna'] }};">
                                        {{ $user->badge['ikon'] }} {{ $user->badge['nama'] }}
                                    </span>
                                </div>
                            </div>

                            @php $friendStatus = $user->friend_status ?? 'none'; @endphp
                            @if($friendStatus === 'friends')
                                <span class="badge bg-light-success text-success rounded-pill d-flex align-items-center gap-1 px-3 py-2" style="font-size: 0.75rem;">
                                    <i class="ph-fill ph-user-check"></i> Berteman
                                </span>
                            @elseif($friendStatus === 'pending')
                                <span class="badge bg-light-warning text-warning rounded-pill d-flex align-items-center gap-1 px-3 py-2" style="font-size: 0.75rem;">
                                    <i class="ph-fill ph-clock"></i> Permintaan Terkirim
                                </span>
                            @elseif($friendStatus === 'awaiting')
                                <div class="d-flex gap-2 w-100 justify-content-center">
                                    <form action="{{ route('friends.accept', $user->pending_request_id ?? '') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-modern rounded-pill px-3" style="background: var(--primary); color: white; border: none; font-size: 0.75rem;">
                                            <i class="ph ph-check"></i> Terima
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('friends.send', $user->id) }}" method="POST" class="m-0 w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-modern rounded-pill w-100 d-flex align-items-center justify-content-center gap-1 px-3 py-2 border-color" style="font-size: 0.75rem;">
                                        <i class="ph ph-user-plus"></i> Tambah Teman
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <div class="icon-container bg-light-secondary text-muted mx-auto rounded-circle mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                    <i class="ph ph-user-minus"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Pengguna Tidak Ditemukan</h5>
                <p class="text-muted mb-0">Tidak ada pengguna dengan nama atau username "{{ $query }}"</p>
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <div class="icon-container bg-light-secondary text-muted mx-auto rounded-circle mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                <i class="ph ph-magnifying-glass"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Cari Pengguna</h5>
            <p class="text-muted mb-0">Ketik nama atau username untuk mulai mencari</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('searchInput');
        var searchClear = document.getElementById('searchClear');
        var searchForm = document.getElementById('searchForm');

        function toggleClear() {
            if (searchInput.value.length > 0) {
                searchClear.classList.add('visible');
            } else {
                searchClear.classList.remove('visible');
            }
        }

        toggleClear();

        searchInput.addEventListener('input', toggleClear);

        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            searchClear.classList.remove('visible');
            searchInput.focus();
            searchForm.submit();
        });

        // Auto-submit on enter (default form behavior)
    });
</script>
@endsection

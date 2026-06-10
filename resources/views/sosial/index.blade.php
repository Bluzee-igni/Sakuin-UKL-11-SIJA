@extends('layouts.app')

@section('title', 'Feed Sosial')

@push('styles')
<style>
    .social-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .social-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    .social-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s ease;
    }

    .social-header:hover {
        background: #f9fafb;
    }

    .social-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background: #059669;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .social-body {
        display: flex;
        gap: 1rem;
        padding: 1rem 1.25rem;
        flex: 1;
    }

    .social-thumb {
        width: 120px;
        height: 120px;
        flex-shrink: 0;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #f0fdf4;
    }

    .social-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        background: #fff;
    }

    .social-thumb-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.5);
        font-size: 2.5rem;
    }

    .social-info {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 0;
        flex: 1;
    }

    .social-target-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
    }

    .social-message {
        font-size: 0.85rem;
        color: #4b5563;
        background: #f9fafb;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border-left: 3px solid #059669;
        font-style: italic;
    }

    .social-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.5rem 0 0;
        border-top: 1px solid #f3f4f6;
        margin-top: auto;
    }

    .social-action-btn {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        border: none;
        background: none;
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .social-action-btn:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .social-action-btn.liked {
        color: #ef4444;
    }

    .social-action-btn.liked:hover {
        background: #fef2f2;
    }

    .social-action-btn i {
        font-size: 1.2rem;
    }

    .social-comments {
        padding: 0.75rem 1.25rem;
    }

    .comments-scroll {
        max-height: 240px;
        overflow-y: auto;
    }

    .comments-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .comments-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .comments-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }

    .comments-scroll::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    [data-theme="dark"] .comments-scroll::-webkit-scrollbar-thumb {
        background: #4b5563;
    }

    [data-theme="dark"] .comments-scroll::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    .comment-item {
        display: flex;
        gap: 0.6rem;
        margin-bottom: 0.75rem;
    }

    .comment-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #4b5563;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .comment-bubble {
        background: #f3f4f6;
        padding: 0.5rem 0.75rem;
        border-radius: 0.75rem;
        flex: 1;
    }

    .comment-author {
        font-size: 0.75rem;
        font-weight: 700;
        color: #111827;
    }

    .comment-text {
        font-size: 0.8rem;
        color: #374151;
        margin-top: 0.15rem;
        word-break: break-word;
    }

    .comment-time {
        font-size: 0.65rem;
        color: #9ca3af;
        margin-top: 0.15rem;
    }

    .comment-form {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .comment-input {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 9999px;
        padding: 0.45rem 1rem;
        font-size: 0.8rem;
        background: #f9fafb;
        transition: all 0.15s ease;
    }

    .comment-input:focus {
        outline: none;
        border-color: #059669;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .comment-submit {
        border: none;
        background: #059669;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        cursor: pointer;
        flex-shrink: 0;
    }

    .comment-submit:hover {
        background: #047857;
        transform: scale(1.05);
    }

    .comment-submit i {
        font-size: 1rem;
    }

    .empty-feed {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-feed i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .btn-share-modern {
        border-radius: 9999px;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #059669;
        color: white;
        border: none;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        text-decoration: none;
    }

    .btn-share-modern:hover {
        background: #047857;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(5, 150, 105, 0.2);
    }

    .share-target-card {
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .share-target-card:hover {
        border-color: #059669;
        background: #f0fdf4;
    }

    .share-target-card.selected {
        border-color: #059669;
        background: #f0fdf4;
    }

    .share-target-scroll {
        max-height: 240px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .share-target-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .share-target-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }

    .share-target-progress {
        font-size: 0.85rem;
        font-weight: 700;
        color: #059669;
    }

    .streak-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #ef4444;
    }

    .friend-request-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 1rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.15s ease;
    }

    .friend-request-card:hover {
        background: #f9fafb;
    }

    [data-theme="dark"] .social-card {
        background: #1f2937;
        border-color: #374151;
    }

    [data-theme="dark"] .social-header {
        border-color: #374151;
    }

    [data-theme="dark"] .social-header:hover {
        background: #111827;
    }

    [data-theme="dark"] .social-thumb {
        background: #064e3b;
    }

    [data-theme="dark"] .social-thumb img {
        background: #1f2937;
    }

    [data-theme="dark"] .social-message {
        background: #111827;
        color: #d1d5db;
    }

    [data-theme="dark"] .social-actions {
        border-color: #374151;
    }

    [data-theme="dark"] .social-action-btn {
        color: #9ca3af;
    }

    [data-theme="dark"] .social-action-btn:hover {
        background: #374151;
        color: #f9fafb;
    }

    [data-theme="dark"] .comment-bubble {
        background: #374151;
    }

    [data-theme="dark"] .comment-author {
        color: #f9fafb;
    }

    [data-theme="dark"] .comment-text {
        color: #d1d5db;
    }

    [data-theme="dark"] .comment-input {
        background: #111827;
        border-color: #4b5563;
        color: #f9fafb;
    }

    [data-theme="dark"] .comment-input:focus {
        border-color: #059669;
        background: #1f2937;
    }

    [data-theme="dark"] .share-target-card {
        border-color: #4b5563;
    }

    [data-theme="dark"] .share-target-card:hover,
    [data-theme="dark"] .share-target-card.selected {
        border-color: #059669;
        background: rgba(5, 150, 105, 0.1);
    }

    [data-theme="dark"] .share-target-scroll::-webkit-scrollbar-thumb {
        background: #4b5563;
    }

    [data-theme="dark"] .friend-request-card {
        background: #1f2937;
        border-color: #374151;
    }

    [data-theme="dark"] .friend-request-card:hover {
        background: #111827;
    }

    .feed-grid {
        align-items: flex-start;
    }

    @media (max-width: 575.98px) {
        .social-body {
            flex-direction: column;
        }

        .social-thumb {
            width: 100%;
            height: 180px;
        }
    }

    @media (min-width: 576px) and (max-width: 991.98px) {
        .social-body {
            flex-direction: column;
        }

        .social-thumb {
            width: 100%;
            height: 140px;
        }
    }

    @media (min-width: 992px) {
        .social-body {
            flex-direction: column;
        }

        .social-thumb {
            width: 100%;
            height: 140px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-xl-4 py-2">
    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-0 fw-bold font-poppins text-dark">
                <i class="ph-fill ph-users-three text-success me-2"></i> Feed Sosial
            </h4>
            <p class="text-muted small mb-0 mt-1">Lihat progres teman-temanmu dan saling memberi semangat!</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('sosial.search') }}" class="btn btn-outline-modern rounded-pill d-flex align-items-center gap-2 px-3 py-2 border-color">
                <i class="ph ph-magnifying-glass"></i>
                <span class="d-none d-sm-inline">Cari Pengguna</span>
            </a>
            @if($shareableTargets->count() > 0)
                <button type="button" class="btn-share-modern" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="ph ph-share-network"></i> Bagikan
                </button>
            @elseif($ownedTargetCount > 0)
                <a href="{{ route('tabung.index') }}" class="btn-share-modern">
                    <i class="ph ph-piggy-bank"></i> Tambah Setoran
                </a>
            @else
                <a href="{{ route('tabung.create') }}" class="btn-share-modern">
                    <i class="ph ph-target"></i> Buat Target
                </a>
            @endif
        </div>
    </div>

    {{-- FRIEND REQUESTS --}}
    @if(isset($pendingRequests) && $pendingRequestCount > 0)
        <div class="mb-4">
            <h6 class="font-poppins fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="ph-fill ph-user-plus text-primary"></i>
                Permintaan Teman
                <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">{{ $pendingRequestCount }}</span>
            </h6>
            <div class="d-flex flex-column gap-2">
                @foreach($pendingRequests as $req)
                    <div class="friend-request-card">
                        @if($req->pengirim->foto_url)
                            <img src="{{ $req->pengirim->foto_url }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                {{ $req->pengirim->inisial }}
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <a href="{{ route('user.profile', $req->pengirim->username) }}" class="fw-bold text-dark text-decoration-none" style="font-size: 0.9rem;">{{ $req->pengirim->nama }}</a>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $req->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('friends.accept', $req->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-modern rounded-pill px-3" style="background: var(--primary); color: white; border: none; font-size: 0.75rem;">
                                    <i class="ph ph-check"></i> Terima
                                </button>
                            </form>
                            <form action="{{ route('friends.reject', $req->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" style="font-size: 0.75rem;">
                                    <i class="ph ph-x"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FLASH MESSAGE --}}
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

    {{-- FEED GRID --}}
    @if($shares->count() > 0)
        <div class="row g-4 feed-grid">
            @foreach($shares as $share)
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="social-card" id="share-{{ $share->id }}">
                        {{-- Header: User Info (klik ke profil) --}}
                        <a href="{{ route('user.profile', $share->user->username) }}" class="social-header text-decoration-none">
                            @if($share->user->foto_url)
                                <img src="{{ $share->user->foto_url }}" alt="{{ $share->user->inisial }}" class="social-avatar">
                            @else
                                <div class="social-avatar">{{ $share->user->inisial }}</div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $share->user->nama }}</div>
                                    @php $streak = $share->user->cached_streak ?? $share->user->streak_saat_ini; @endphp
                                    @if($streak > 0)
                                        <span class="streak-badge">
                                            <i class="ph-fill ph-fire"></i>{{ $streak }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $share->created_at->diffForHumans() }}</div>
                            </div>
                            @if($share->user_id === auth()->id())
                                <form action="{{ route('sosial.destroy', $share->id) }}" method="POST" class="m-0" onclick="event.stopPropagation()" onsubmit="return confirm('Hapus postingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-1 border-0 bg-transparent" title="Hapus">
                                        <i class="ph ph-trash fs-5"></i>
                                    </button>
                                </form>
                            @endif
                        </a>

                        {{-- Body --}}
                        <div class="social-body">
                            {{-- Thumbnail --}}
                            <div class="social-thumb">
                                @if($share->target->gambar)
                                    <img src="{{ asset('storage/' . $share->target->gambar) }}"
                                         alt="{{ $share->target->nama }}"
                                         loading="lazy">
                                @else
                                    <div class="social-thumb-placeholder">
                                        <i class="ph-fill ph-target"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="social-info">
                                <div class="social-target-name">
                                    <i class="ph-fill ph-target text-success me-1"></i> {{ $share->target->nama }}
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">Progres</span>
                                        <span class="share-target-progress">{{ number_format($share->persentase, 2) }}%</span>
                                    </div>
                                    <div class="progress-modern w-100" style="height: 10px;">
                                        <div class="progress-bar-modern" style="width: {{ $share->persentase > 0 ? max($share->persentase, 2) : 0 }}%"></div>
                                    </div>
                                </div>

                                @if($share->prediction)
                                    @php $prd = $share->prediction; @endphp
                                    @if($prd['status'] === 'selesai')
                                        <div class="small fw-bold text-success">
                                            🎉 {{ $prd['message'] }}
                                        </div>
                                    @elseif($prd['status'] === 'on_track')
                                        <div class="small text-muted">
                                            <span>🎯 Prediksi selesai: {{ $prd['formatted_prediksi'] }}</span>
                                            <span class="d-block" style="font-size: 0.7rem;">📅 Target diperkirakan selesai: {{ \Carbon\Carbon::parse($prd['tanggal_prediksi'])->isoFormat('D MMMM YYYY') }}</span>
                                        </div>
                                    @elseif($prd['status'] === 'no_data' && $prd['message'])
                                        <div class="small text-muted">
                                            <i class="ph ph-chart-bar"></i> {{ $prd['message'] }}
                                        </div>
                                    @endif
                                @endif

                                @if($share->pesan)
                                    <div class="social-message">
                                        <i class="ph ph-quotes me-1 opacity-50"></i>
                                        {{ $share->pesan }}
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="social-actions">
                                    @php
                                        $isLiked = $share->isLikedByUser(auth()->id());
                                        $likeCount = $share->likes->count();
                                        $commentCount = $share->comments->count();
                                    @endphp
                                    <form action="{{ route('sosial.like', $share->id) }}" method="POST" class="m-0 like-form">
                                        @csrf
                                        <button type="submit" class="social-action-btn {{ $isLiked ? 'liked' : '' }}">
                                            <i class="{{ $isLiked ? 'ph-fill ph-heart' : 'ph ph-heart-straight' }}"></i>
                                            <span class="like-count">{{ $likeCount }}</span> Suka
                                        </button>
                                    </form>

                                    <button type="button" class="social-action-btn toggle-comments" data-target="#comments-{{ $share->id }}">
                                        <i class="ph ph-chat-circle-dots"></i>
                                        <span>{{ $commentCount }}</span> Komentar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Comments --}}
                        <div class="social-comments" id="comments-{{ $share->id }}" style="display: none;">
                            @if($commentCount > 0)
                                <div class="comments-scroll mb-2">
                                    @foreach($share->comments as $comment)
                                        <div class="comment-item">
                                            @if($comment->user->foto_url)
                                                <img src="{{ $comment->user->foto_url }}" alt="" class="comment-avatar" style="object-fit: cover;">
                                            @else
                                                <div class="comment-avatar">{{ $comment->user->inisial }}</div>
                                            @endif
                                            <div class="comment-bubble">
                                                <div class="comment-author">{{ $comment->user->nama }}</div>
                                                <div class="comment-text">{{ $comment->comment }}</div>
                                                <div class="comment-time">{{ $comment->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted small py-2">Belum ada komentar. Jadilah yang pertama!</div>
                            @endif

                            <form action="{{ route('sosial.comment', $share->id) }}" method="POST" class="comment-form">
                                @csrf
                                <input type="text" name="comment" class="comment-input" placeholder="Tulis komentar..." maxlength="1000" required autocomplete="off">
                                <button type="submit" class="comment-submit" title="Kirim">
                                    <i class="ph ph-paper-plane-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($shares->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $shares->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="social-card empty-feed">
            <i class="ph-fill ph-users-three d-block mx-auto"></i>
            <h5 class="fw-bold text-dark mb-2">Belum Ada Postingan</h5>
            <p class="text-muted mb-3">Bagikan progres target tabunganmu dan mulai berinteraksi dengan sesama penabung!</p>
            @if($shareableTargets->count() > 0)
                <button type="button" class="btn-share-modern" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="ph ph-share-network"></i> Bagikan Progres Sekarang
                </button>
            @elseif($ownedTargetCount > 0)
                <a href="{{ route('tabung.index') }}" class="btn-share-modern">
                    <i class="ph ph-piggy-bank"></i> Tambah Setoran
                </a>
            @else
                <a href="{{ route('tabung.create') }}" class="btn-share-modern">
                    <i class="ph ph-target"></i> Buat Target Baru
                </a>
            @endif
        </div>
    @endif
</div>

{{-- SHARE MODAL --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 1.25rem; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold font-poppins text-dark">
                    <i class="ph-fill ph-share-network text-success me-2"></i>Bagikan Progres
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sosial.share') }}" method="POST" id="shareForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Pilih target yang sudah punya setoran untuk dibagikan ke feed sosial.</p>

                    @if($shareableTargets->count() > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih Target</label>
                            <div class="share-target-scroll">
                                @foreach($shareableTargets as $target)
                                    <div class="share-target-card d-block mb-2" data-target-id="{{ $target->id }}" role="button">
                                        <input type="radio" name="target_tabungan_id" value="{{ $target->id }}" class="d-none" required>
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            @if($target->gambar)
                                                <img src="{{ asset('storage/' . $target->gambar) }}" style="width: 48px; height: 48px; border-radius: 0.75rem; object-fit: cover;">
                                            @else
                                                <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: #d1fae5; display: flex; align-items: center; justify-content: center; color: #059669; font-size: 1.5rem;">
                                                    <i class="ph-fill ph-target"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $target->nama }}</div>
                                                <div class="progress-modern mt-1" style="height: 5px;">
                                                    <div class="progress-bar-modern" style="width: {{ $target->persentase_progres > 0 ? max($target->persentase_progres, 2) : 0 }}%"></div>
                                                </div>
                                            </div>
                                            <span class="share-target-progress">{{ number_format($target->persentase_progres, 2) }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small">Pesan Motivasi <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea name="pesan" class="form-control form-control-modern" rows="3" placeholder="Ayo semangat! Targetku..." maxlength="500" style="resize: none;"></textarea>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ph ph-target fs-1 text-muted opacity-50 d-block mb-2"></i>
                            @if($ownedTargetCount > 0)
                                <p class="text-muted mb-0">Belum ada target yang memiliki progres untuk dibagikan.</p>
                                <a href="{{ route('tabung.index') }}" class="btn btn-sm btn-success-modern rounded-pill mt-3">Tambah Setoran</a>
                            @else
                                <p class="text-muted mb-0">Belum ada target tabungan.</p>
                                <a href="{{ route('tabung.create') }}" class="btn btn-sm btn-success-modern rounded-pill mt-3">Buat Target Baru</a>
                            @endif
                        </div>
                    @endif
                </div>
                @if($shareableTargets->count() > 0)
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-modern rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success-modern rounded-pill px-4" id="shareSubmitBtn">
                        <i class="ph ph-share-network"></i> Bagikan
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-comments').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                if (target) {
                    const isHidden = target.style.display === 'none';
                    target.style.display = isHidden ? 'block' : 'none';
                }
            });
        });

        document.querySelectorAll('.like-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const btn = this.querySelector('.social-action-btn');
                const countSpan = this.querySelector('.like-count');
                const url = this.action;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.liked) {
                        btn.classList.add('liked');
                    } else {
                        btn.classList.remove('liked');
                    }
                    countSpan.textContent = data.like_count;
                })
                .catch(() => {
                    form.submit();
                });
            });
        });

        // Share modal: select target card
        const shareTargetCards = document.querySelectorAll('.share-target-card');
        const shareSubmitBtn = document.getElementById('shareSubmitBtn');

        function selectShareTarget(card) {
            shareTargetCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        shareTargetCards.forEach(function(card) {
            card.addEventListener('click', function() {
                selectShareTarget(this);
            });
        });

        // Auto-select first target when modal opens
        const shareModal = document.getElementById('shareModal');
        if (shareModal) {
            shareModal.addEventListener('shown.bs.modal', function() {
                const firstCard = document.querySelector('.share-target-card');
                if (firstCard) {
                    selectShareTarget(firstCard);
                }
            });
        }
    });
</script>
@endpush

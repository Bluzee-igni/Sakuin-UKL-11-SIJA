<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tabungan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="py-4">

<div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3>
                Halo, <span class="text-success">{{ auth()->user()->name }}</span> 👋
            </h3>
            <small class="text-muted">Pantau progres tabungan kamu.</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tabung.create') }}" class="btn btn-success">
                + Tambah Target
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- KALENDER --}}
            <div class="card p-3 mb-3">
                <h5 class="mb-2">Kalender Nabung (Bulan Ini)</h5>

                <div class="calendar">
                    @forelse($checkins as $c)
                        <div class="cal-cell green">
                            <div class="cal-day">
                                {{ \Carbon\Carbon::parse($c->tanggal)->format('d') }}
                            </div>
                            <span class="cal-dot"></span>
                        </div>
                    @empty
                        <div class="text-muted">
                            Belum ada check-in bulan ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- LIST TARGET --}}
            <div class="card p-3">
                <h5 class="mb-3">Daftar Target</h5>

                <div class="row g-3">
                    @forelse($targets as $tg)

                        @php
                            $totalTarget = $tg->checkins->sum('nominal');
                            $progress = $tg->harga_target > 0
                                ? ($totalTarget / $tg->harga_target) * 100
                                : 0;
                        @endphp

                        <div class="col-md-6">
                            <div class="target-card">

                                <div class="fw-bold">
                                    {{ $tg->nama }}
                                    @if($tg->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </div>

                                <div class="text-success fw-bold mt-1">
                                    Rp {{ number_format($totalTarget,0,',','.') }}
                                </div>

                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success"
                                         style="width: {{ $progress }}%">
                                    </div>
                                </div>

                                <small class="text-muted">
                                    {{ number_format($progress,1) }}%
                                </small>

                            </div>
                        </div>

                    @empty
                        <div class="text-muted">
                            Belum ada target. Yuk buat target pertama kamu!
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">

            {{-- STREAK --}}
            <div class="card p-3 mb-3">
                <h6>Streak Sekarang</h6>
                <h3 class="text-success">
                    {{ $streak ?? 0 }} 🔥
                </h3>
            </div>

            {{-- ESTIMASI --}}
            <div class="card p-3 mb-3">
                <h6>Estimasi Selesai</h6>

                @if($estimasiTanggal)
                    <strong>
                        {{ $estimasiTanggal->format('d M Y') }}
                    </strong>
                @else
                    <small class="text-muted">
                        Belum cukup data
                    </small>
                @endif
            </div>

            {{-- FORM NABUNG --}}
            @if($activeTarget)
                <div class="card p-3">
                    <h6>Nabung Hari Ini ({{ $activeTarget->nama }})</h6>

                    <form action="{{ route('checkins.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_id" value="{{ $activeTarget->id }}">

                        <div class="mb-2">
                            <input type="date"
                                   name="tanggal"
                                   class="form-control"
                                   value="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>

                        <div class="mb-2">
                            <input type="number"
                                   name="nominal"
                                   class="form-control"
                                   placeholder="Nominal"
                                   required>
                        </div>

                        <div class="mb-2">
                            <input type="text"
                                   name="catatan"
                                   class="form-control"
                                   placeholder="Catatan (opsional)">
                        </div>

                        <button class="btn btn-success w-100">
                            Simpan
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
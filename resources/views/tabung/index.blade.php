<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tabungan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    {{-- FullCalendar CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
</head>
<body class="py-4">

<div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
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

    {{-- FLASH MESSAGE --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3">

        {{-- KIRI --}}
        <div class="col-lg-8">

            {{-- KALENDER (FullCalendar) --}}
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Kalender Nabung</h5>
                        <small class="text-muted">Klik tanggal untuk memilih. Hari ini otomatis ditandai.</small>
                    </div>
                    <span class="badge badge-soft" id="pickedDateBadge">Pilih tanggal…</span>
                </div>

                <hr class="my-3">

                <div id="savingCalendar"></div>
            </div>

            {{-- DAFTAR TARGET --}}
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Daftar Target</h5>
                    <small class="text-muted">Progress otomatis dari total check-in per target.</small>
                </div>

                <hr class="my-3">

                <div class="row g-3">
                    @forelse($targets as $tg)
                        @php
                            $totalTarget = $tg->checkins->sum('nominal');
                            $progress = $tg->harga_target > 0 ? ($totalTarget / $tg->harga_target) * 100 : 0;
                            if ($progress > 100) $progress = 100;
                        @endphp

                        <div class="col-md-6">
                            <div class="target-card">

                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">{{ $tg->nama }}</div>
                                        <small class="text-muted">
                                            Target: Rp {{ number_format($tg->harga_target,0,',','.') }}
                                        </small>
                                    </div>

                                    @if($activeTarget && $activeTarget->id === $tg->id)
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </div>

                                <div class="mt-2 money text-success">
                                    Terkumpul: Rp {{ number_format($totalTarget,0,',','.') }}
                                </div>

                                <div class="progress">
                                    <div
                                        class="progress-bar bg-success js-progress-bar"
                                        data-progress="{{ number_format($progress, 2, '.', '') }}"
                                    ></div>
                                </div>

                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                    <span>{{ number_format($progress,1) }}%</span>
                                    <span>Sisa: Rp {{ number_format(max(0, $tg->harga_target - $totalTarget),0,',','.') }}</span>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada target. Klik “Tambah Target”.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KANAN --}}
        <div class="col-lg-4">

            {{-- STREAK --}}
            <div class="card p-3 mb-3">
                <h6 class="mb-1">Streak Sekarang</h6>
                <div class="d-flex align-items-end gap-2">
                    <h3 class="text-success mb-0">{{ $streak ?? 0 }} 🔥</h3>
                    <small class="text-muted">hari</small>
                </div>
                <small class="text-muted">Semakin konsisten, makin cepat target tercapai.</small>
            </div>

            {{-- ESTIMASI --}}
            <div class="card p-3 mb-3">
                <h6 class="mb-1">Estimasi Selesai</h6>

                @if($estimasiTanggal)
                    <strong>{{ $estimasiTanggal->format('d M Y') }}</strong>
                    <div class="small text-muted mt-1">Berdasarkan pola nabung terakhir.</div>
                @else
                    <small class="text-muted">Belum cukup data</small>
                @endif
            </div>

            {{-- FORM CHECKIN --}}
            <div class="card p-3">
                <h6 class="mb-2">Nabung Hari Ini</h6>

                @if($activeTarget)
                    <form action="{{ route('checkins.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_id" value="{{ $activeTarget->id }}">

                        <div class="mb-2">
                            <label class="form-label mb-1">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label mb-1">Nominal</label>
                            <input type="number" name="nominal" class="form-control"
                                   placeholder="Contoh: 20000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label mb-1">Catatan (opsional)</label>
                            <input type="text" name="catatan" class="form-control"
                                   placeholder="Misal: nabung receh">
                        </div>

                        <button class="btn btn-success w-100">Simpan Check-in</button>
                    </form>
                @else
                    <small class="text-muted">
                        Kamu belum punya target aktif. Buat target dulu ya.
                    </small>
                @endif
            </div>

        </div>

    </div>

    {{-- PANEL BAWAH --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Ringkasan & Riwayat</h5>
                        <small class="text-muted">Total bulan ini, rata-rata, dan check-in terbaru.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('tabung.create') }}" class="btn btn-outline-success btn-sm">
                            + Target Baru
                        </a>
                        <a href="#" class="btn btn-success btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row g-3">
                    {{-- KIRI: mini stats --}}
                    <div class="col-lg-4">
                        <div class="mini-card">
                            <div class="mini-label">Total Nabung (bulan ini)</div>
                            <div class="mini-value text-success">
                                Rp {{ number_format($totalBulanIni ?? 0,0,',','.') }}
                            </div>
                            <div class="mini-sub text-muted">
                                Check-in: <b>{{ $jumlahCheckinBulanIni ?? 0 }}</b> kali
                            </div>
                        </div>

                        <div class="mini-card mt-3">
                            <div class="mini-label">Rata-rata / check-in</div>
                            <div class="mini-value">
                                Rp {{ number_format($rata2PerCheckin ?? 0,0,',','.') }}
                            </div>
                            <div class="mini-sub text-muted">
                                Berdasarkan data bulan ini
                            </div>
                        </div>

                        <div class="mini-card mt-3">
                            <div class="mini-label">Terakhir Nabung</div>
                            <div class="mini-value">
                                {{ $lastCheckinDate ?? '-' }}
                            </div>
                            <div class="mini-sub text-muted">
                                Jaga konsistensi biar streak naik.
                            </div>
                        </div>
                    </div>

                    {{-- KANAN: riwayat terbaru --}}
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0">Riwayat Check-in Terbaru</h6>
                            <span class="badge badge-soft">Terbaru</span>
                        </div>

                        @if(!empty($recentCheckins) && count($recentCheckins))
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                    <tr class="text-muted small">
                                        <th style="width: 130px;">Tanggal</th>
                                        <th>Target</th>
                                        <th style="width: 160px;">Nominal</th>
                                        <th>Catatan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($recentCheckins as $rc)
                                        <tr>
                                            <td class="text-muted">
                                                {{ \Carbon\Carbon::parse($rc->tanggal)->format('d M Y') }}
                                            </td>
                                            <td class="fw-semibold">{{ $rc->target->nama ?? '-' }}</td>
                                            <td class="fw-bold text-success">
                                                Rp {{ number_format($rc->nominal,0,',','.') }}
                                            </td>
                                            <td class="text-muted">{{ $rc->catatan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="insight mt-3">
                                <div class="insight-dot"></div>
                                <div>
                                    <div class="fw-semibold">Insight</div>
                                    <div class="text-muted small">
                                        Nabung kecil tapi rutin biasanya lebih gampang dijaga daripada sekali besar.
                                        Coba bikin “minimal harian” (misal 5.000).
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">Belum ada riwayat check-in.</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script id="calendarEventsData" type="application/json">@json($calendarEvents ?? [])</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('savingCalendar');
    const badge = document.getElementById('pickedDateBadge');
    const dateInput = document.querySelector('input[name="tanggal"]');
    const events = JSON.parse(document.getElementById('calendarEventsData')?.textContent || '[]');

    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'id',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        navLinks: true,
        selectable: true,
        events: events,

        dateClick: function(info) {
            const picked = info.dateStr; // YYYY-MM-DD
            if (badge) badge.textContent = 'Dipilih: ' + picked;
            if (dateInput) dateInput.value = picked;
        },
    });

    calendar.render();

    const today = new Date().toISOString().slice(0, 10);
    if (badge) badge.textContent = 'Hari ini: ' + today;
});
</script>

<script>
document.querySelectorAll('.js-progress-bar[data-progress]').forEach(function (el) {
    var value = parseFloat(el.getAttribute('data-progress') || '0');
    var clamped = Math.max(0, Math.min(100, isNaN(value) ? 0 : value));
    el.style.width = clamped + '%';
});
</script>

</body>
</html>

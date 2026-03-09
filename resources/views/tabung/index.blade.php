<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tabungan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
</head>
<body class="py-4 bg-light">

<div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h3 class="mb-1">
                Halo, <span class="text-success">{{ auth()->user()->name }}</span> 👋
            </h3>
            <small class="text-muted">Pantau progres tabungan kamu setiap hari.</small>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('tabung.create') }}" class="btn btn-success">
                + Tambah Target
            </a>

            <a href="{{ route('incomes.create') }}" class="btn btn-outline-primary">
                + Tambah Income
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
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- KOLOM KIRI --}}
        <div class="col-lg-8">

            {{-- KALENDER --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1">Kalender Nabung</h5>
                            <small class="text-muted">Klik tanggal untuk memilih check-in.</small>
                        </div>
                        <span class="badge text-bg-success" id="pickedDateBadge">Pilih tanggal…</span>
                    </div>

                    <hr>

                    <div id="savingCalendar"></div>
                </div>
            </div>

            {{-- DAFTAR TARGET --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Daftar Target</h5>
                            <small class="text-muted">Progress dihitung dari total tabungan tiap target.</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        @forelse($targets as $tg)
                            @php
                                $totalTarget = $tg->checkins->sum('nominal');
                                $progress = $tg->harga_target > 0 ? ($totalTarget / $tg->harga_target) * 100 : 0;
                                $progress = min(100, max(0, $progress));
                            @endphp

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div>
                                                <div class="fw-bold fs-6 d-flex align-items-center gap-2 flex-wrap">
                                                    {{ $tg->nama }}

                                                    @if($activeTarget && $activeTarget->id === $tg->id)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @endif
                                                </div>

                                                <small class="text-muted">
                                                    Target: Rp {{ number_format($tg->harga_target, 0, ',', '.') }}
                                                </small>
                                            </div>

                                            <div class="d-flex gap-1 flex-wrap justify-content-end">
                                                <a href="{{ route('tabung.edit', $tg->id) }}"
                                                   class="btn btn-sm btn-outline-warning">
                                                    Edit
                                                </a>

                                                <form action="{{ route('tabung.destroy', $tg->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Yakin ingin hapus target ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="mb-2 text-success fw-semibold">
                                            Terkumpul: Rp {{ number_format($totalTarget, 0, ',', '.') }}
                                        </div>

                                        <div class="progress" style="height: 10px;">
                                            <div
                                                class="progress-bar bg-success js-progress-bar"
                                                data-progress="{{ number_format($progress, 2, '.', '') }}">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2 small text-muted">
                                            <span>{{ number_format($progress, 1) }}%</span>
                                            <span>
                                                Sisa: Rp {{ number_format(max(0, $tg->harga_target - $totalTarget), 0, ',', '.') }}
                                            </span>
                                        </div>

                                        @if($activeTarget && $activeTarget->id !== $tg->id)
                                            <form action="{{ route('targets.active', $tg->id) }}" method="POST" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                                    Jadikan Target Aktif
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4 text-muted">
                                    Belum ada target. Klik <b>Tambah Target</b> untuk mulai menabung.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-4">
            @php
                $activeTotal = $activeTarget ? $activeTarget->checkins->sum('nominal') : 0;
                $activeGoal  = $activeTarget ? (int) $activeTarget->harga_target : 0;
                $ringProgress = $activeGoal > 0 ? ($activeTotal / $activeGoal) * 100 : 0;
                $ringProgress = max(0, min(100, $ringProgress));
            @endphp

            {{-- STREAK --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="mb-1">Streak Sekarang</h6>
                            <div class="d-flex align-items-end gap-2">
                                <h2 class="text-success mb-0">{{ $streak ?? 0 }} 🔥</h2>
                                <small class="text-muted mb-1">hari</small>
                            </div>
                            <small class="text-muted">
                                Konsisten sedikit demi sedikit lebih baik daripada besar tapi jarang.
                            </small>
                        </div>

                        <div class="text-center">
                            <div class="rounded-circle border border-3 border-success d-flex align-items-center justify-content-center"
                                 style="width: 90px; height: 90px; font-weight: 700; font-size: 18px;">
                                {{ number_format($ringProgress, 0) }}%
                            </div>
                        </div>
                    </div>

                    <hr>

                    @if($activeTarget)
                        <div class="small text-muted">
                            Target aktif:
                            <b>{{ $activeTarget->nama }}</b><br>
                            Rp {{ number_format($activeTotal, 0, ',', '.') }}
                            / Rp {{ number_format($activeGoal, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="small text-muted">
                            Belum ada target aktif.
                        </div>
                    @endif

                    @if($estimasiTanggal)
                        <div class="mt-3">
                            <div class="small text-muted">Estimasi target tercapai</div>
                            <strong>{{ $estimasiTanggal->format('d M Y') }}</strong>
                        </div>
                    @else
                        <div class="mt-3">
                            <small class="text-muted">Belum cukup data untuk estimasi.</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- FORM CHECKIN --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h6 class="mb-3">Nabung Hari Ini</h6>

                    @if($activeTarget)
                        <form action="{{ route('checkins.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="target_id" value="{{ $activeTarget->id }}">

                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="{{ now()->format('Y-m-d') }}"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nominal</label>
                                <input type="number"
                                       name="nominal"
                                       class="form-control"
                                       placeholder="Contoh: 20000"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan (opsional)</label>
                                <input type="text"
                                       name="catatan"
                                       class="form-control"
                                       placeholder="Misal: nabung receh">
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                Simpan Check-in
                            </button>
                        </form>
                    @else
                        <div class="text-muted small">
                            Kamu belum punya target aktif. Buat target dulu ya.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- RINGKASAN BAWAH --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1">Ringkasan & Riwayat</h5>
                            <small class="text-muted">Lihat kebiasaan nabung kamu bulan ini.</small>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('tabung.create') }}" class="btn btn-outline-success btn-sm">
                                + Target Baru
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-4">
                        {{-- MINI STATS --}}
                        <div class="col-lg-4">
                            <div class="card border-0 bg-light rounded-4 mb-3">
                                <div class="card-body">
                                    <div class="small text-muted">Total Nabung (bulan ini)</div>
                                    <div class="fs-4 fw-bold text-success">
                                        Rp {{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="small text-muted">
                                        Check-in: <b>{{ $jumlahCheckinBulanIni ?? 0 }}</b> kali
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 bg-light rounded-4 mb-3">
                                <div class="card-body">
                                    <div class="small text-muted">Rata-rata / check-in</div>
                                    <div class="fs-5 fw-bold">
                                        Rp {{ number_format($rata2PerCheckin ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="small text-muted">
                                        Berdasarkan data bulan ini
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 bg-light rounded-4">
                                <div class="card-body">
                                    <div class="small text-muted">Terakhir Nabung</div>
                                    <div class="fs-5 fw-bold">
                                        {{ $lastCheckinDate ?? '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        Jaga konsistensi supaya streak terus naik.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIWAYAT --}}
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <h6 class="mb-0">Riwayat Check-in Terbaru</h6>
                                <span class="badge text-bg-light border">Terbaru</span>
                            </div>

                            @if(!empty($recentCheckins) && count($recentCheckins))
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr class="text-muted small">
                                                <th>Tanggal</th>
                                                <th>Target</th>
                                                <th>Nominal</th>
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
                                                        Rp {{ number_format($rc->nominal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-muted">{{ $rc->catatan ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-light border rounded-4 mt-3 mb-0">
                                    <div class="fw-semibold mb-1">Insight</div>
                                    <div class="small text-muted">
                                        Nabung kecil tapi rutin biasanya lebih mudah dijaga daripada langsung besar.
                                        Coba tentukan nominal minimal harian, misalnya Rp 5.000.
                                    </div>
                                </div>
                            @else
                                <div class="text-muted">
                                    Belum ada riwayat check-in.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            const picked = info.dateStr;
            if (badge) badge.textContent = 'Dipilih: ' + picked;
            if (dateInput) dateInput.value = picked;
        },
    });

    calendar.render();

    const today = new Date().toISOString().slice(0, 10);
    if (badge) badge.textContent = 'Hari ini: ' + today;
});

document.querySelectorAll('.js-progress-bar[data-progress]').forEach(function (el) {
    const value = parseFloat(el.getAttribute('data-progress') || '0');
    const clamped = Math.max(0, Math.min(100, isNaN(value) ? 0 : value));
    el.style.width = clamped + '%';
});
</script>

</body>
</html>
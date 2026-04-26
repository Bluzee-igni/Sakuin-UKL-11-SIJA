@extends('layouts.app')

@section('title', 'Manajemen Keuangan')

@section('content')
<div class="container-fluid p-0">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold font-poppins text-primary d-flex align-items-center gap-2">
                <i class="ph-fill ph-wallet"></i> Manajemen Keuangan
            </h4>
            <small class="text-muted">Kelola pemasukan, pengeluaran, dan automasi keuanganmu.</small>
        </div>
        <a href="{{ route('tabung.index') }}" class="btn btn-outline-modern btn-sm d-flex align-items-center gap-1">
            <i class="ph ph-arrow-left"></i> Kembali ke Dashboard
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

    <div class="row g-4">
        
        {{-- KIRI: FORM INPUT & AUTOMATION --}}
        <div class="col-lg-7">

            <ul class="nav nav-pills mb-3 gap-2" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="pills-transaksi-tab" data-bs-toggle="pill" data-bs-target="#pills-transaksi" type="button" role="tab">Catat Transaksi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="pills-automasi-tab" data-bs-toggle="pill" data-bs-target="#pills-automasi" type="button" role="tab">Automasi (Gaji/Rutin)</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                {{-- TAB TRANSAKSI MANUAL --}}
                <div class="tab-pane fade show active" id="pills-transaksi" role="tabpanel">
                    
                    {{-- FORM PEMASUKAN --}}
                    <div class="fintech-card p-4 mb-4 border-top border-success border-4 shadow-sm">
                        <h6 class="font-poppins fw-semibold mb-3 d-flex align-items-center gap-2 text-success">
                            <i class="ph-fill ph-trend-up"></i> Input Pemasukan
                        </h6>
                        <form action="{{ route('management.income.store') }}" method="POST" id="form-income">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Nama Pemasukan</label>
                                    <input type="text" name="nama" class="form-control form-control-modern" placeholder="Contoh: Bonus Bulanan" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Nominal (Rp)</label>
                                    <input type="text" name="nominal" class="form-control form-control-modern js-currency-format" placeholder="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success-modern w-100 py-2">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- FORM PENGELUARAN --}}
                    <div class="fintech-card p-4 border-top border-danger border-4 shadow-sm">
                        <h6 class="font-poppins fw-semibold mb-3 d-flex align-items-center gap-2 text-danger">
                            <i class="ph-fill ph-trend-down"></i> Input Pengeluaran
                        </h6>
                        <form action="{{ route('management.expense.store') }}" method="POST" id="form-expense">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-medium">Nama Pengeluaran</label>
                                    <input type="text" name="nama" class="form-control form-control-modern" placeholder="Contoh: Beli Baju, Cicilan" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Nominal (Rp)</label>
                                    <input type="text" name="nominal" class="form-control form-control-modern js-currency-format" placeholder="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Kategori</label>
                                    <input type="text" list="kategoriOptions" name="kategori" class="form-control form-control-modern" placeholder="Ketik atau Pilih" required>
                                    <datalist id="kategoriOptions">
                                        <option value="Kebutuhan Pokok">
                                        <option value="Mendesak">
                                        <option value="Kebutuhan Lain">
                                        <option value="Cicilan">
                                        <option value="Hiburan">
                                    </datalist>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control form-control-modern" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-danger-modern w-100 py-2 text-white" style="background: var(--danger);">Catat Pengeluaran</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB AUTOMASI --}}
                <div class="tab-pane fade" id="pills-automasi" role="tabpanel">
                    <div class="fintech-card p-4 mb-4 shadow-sm border-top border-primary border-4">
                        <h6 class="font-poppins fw-semibold mb-3 text-primary d-flex align-items-center gap-2">
                            <i class="ph-fill ph-robot"></i> Tambah Transaksi Otomatis (Gaji / Rutin)
                        </h6>
                        <p class="small text-muted mb-4">Sistem Sakuin akan otomatis mencatat transaksi ini setiap bulannya pada tanggal yang Anda tentukan. Sangat cocok untuk mencatat gaji bulanan atau tagihan listrik.</p>
                        
                        <form action="{{ route('management.automation.store') }}" method="POST">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Tipe</label>
                                    <select name="tipe" class="form-control form-control-modern" required>
                                        <option value="income">Pemasukan (Gaji)</option>
                                        <option value="expense">Pengeluaran (Tagihan)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small fw-medium">Nama Transaksi</label>
                                    <input type="text" name="nama" class="form-control form-control-modern" placeholder="Misal: Gaji PT ABC" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label text-muted small fw-medium">Nominal (Rp)</label>
                                    <input type="text" name="nominal" class="form-control form-control-modern js-currency-format" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Kategori</label>
                                    <input type="text" name="kategori" class="form-control form-control-modern" placeholder="(Opsi u/ Pengeluaran)">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted small fw-medium">Tanggal Rutin</label>
                                    <input type="number" name="tanggal_rutin" class="form-control form-control-modern" min="1" max="31" placeholder="Tgl (1-31)" required>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary-modern w-100 py-2">Aktifkan Automasi</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- LIST AUTOMASI --}}
                    <h6 class="font-poppins fw-semibold mt-4 mb-3">Daftar Transaksi Otomatis Aktif</h6>
                    @forelse($automations as $auto)
                        <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm border border-light mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-container {{ $auto->tipe == 'income' ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}" style="width:40px;height:40px;">
                                    <i class="ph-fill {{ $auto->tipe == 'income' ? 'ph-trend-up' : 'ph-trend-down' }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $auto->nama }}</div>
                                    <div class="small text-muted">Tgl {{ $auto->tanggal_rutin }} setiap bulan • Rp {{ number_format($auto->nominal,0,',','.') }}</div>
                                </div>
                            </div>
                            <form action="{{ route('management.automation.destroy', $auto->id) }}" method="POST" onsubmit="return confirm('Hapus automasi ini?');">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger px-2"><i class="ph ph-trash"></i></button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-muted small p-4 bg-white rounded-3 border">Belum ada automasi yang aktif.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- KANAN: RINGKASAN & CHART --}}
        <div class="col-lg-5">
            
            {{-- SUMMARY CARD --}}
            <div class="fintech-card p-4 mb-3 bg-primary text-white text-center rounded-4 shadow-lg position-relative overflow-hidden" style="border:none;">
                <div class="position-absolute" style="top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -20px; left: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                
                <h6 class="mb-2 fw-medium text-white-50">Saldo Anda</h6>
                <h2 class="font-poppins fw-bold mb-4">Rp {{ number_format($saldo, 0, ',', '.') }}</h2>
                
                <div class="row g-2">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-2">
                            <div class="small text-white-50 mb-1 d-flex justify-content-center align-items-center gap-1">
                                <i class="ph ph-arrow-down-left text-success bg-white rounded-circle p-1"></i> Masuk Bln Ini
                            </div>
                            <div class="fw-semibold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-2">
                            <div class="small text-white-50 mb-1 d-flex justify-content-center align-items-center gap-1">
                                <i class="ph ph-arrow-up-right text-danger bg-white rounded-circle p-1"></i> Keluar Bln Ini
                            </div>
                            <div class="fw-semibold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUDGET BULANAN --}}
            <div class="fintech-card p-3 mb-4 shadow-sm border-start border-4 {{ $isOverBudget ? 'border-danger' : 'border-success' }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-poppins fw-semibold m-0">Kebutuhan / Budget Bulanan</h6>
                    <button class="btn btn-sm btn-light py-0 px-2" data-bs-toggle="modal" data-bs-target="#budgetModal">Set Budget</button>
                </div>
                
                @if($budget > 0)
                    @php $pct = min(100, ($totalExpense / $budget) * 100); @endphp
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Terpakai: Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        <span class="{{ $isOverBudget ? 'text-danger fw-bold' : 'text-success fw-bold' }}">Sisa: Rp {{ number_format($sisaBudget, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar {{ $isOverBudget ? 'bg-danger' : 'bg-success' }}" style="width: {{ $pct }}%"></div>
                    </div>
                    @if($isOverBudget)
                        <div class="small text-danger mt-1"><i class="ph-fill ph-warning"></i> Perhatian: Pengeluaran bulan ini melebihi budget!</div>
                    @else
                        <div class="small text-success mt-1"><i class="ph-fill ph-check-circle"></i> Keuangan Anda bulan ini aman.</div>
                    @endif
                @else
                    <div class="small text-muted">Belum mengatur target batas pengeluaran bulanan.</div>
                @endif
            </div>

            {{-- CHART CARD --}}
            <div class="fintech-card p-4">
                <h6 class="font-poppins fw-semibold mb-4 d-flex align-items-center gap-2">
                    <i class="ph-fill ph-chart-pie-slice text-primary"></i> Statistik Kategori (Bulan Ini)
                </h6>
                
                @if(count($chartKeys) > 0)
                    <div class="position-relative" style="height: 250px;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                @else
                    <div class="text-center text-muted p-4">Belum ada pengeluaran bulan ini.</div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Modal Set Budget -->
<div class="modal fade" id="budgetModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <form action="{{ route('management.budget.set') }}" method="POST">
          @csrf
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title font-poppins fw-bold text-primary">Set Budget Bulanan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small">Tentukan batas maksimal pengeluaran bulanan Anda. Sakuin akan membantu memantau pengeluaran Anda.</p>
            <label class="form-label text-muted small fw-medium">Nominal Budget (Rp)</label>
            <input type="text" name="budget_bulanan" class="form-control form-control-modern js-currency-format" value="{{ number_format($budget,0,'','') }}" required>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="submit" class="btn btn-primary-modern w-100">Simpan Budget</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Data untuk Chart.js (Dinamis) -->
<script>
    window.chartData = {
        labels: {!! json_encode($chartKeys) !!},
        data: {!! json_encode($chartValues) !!}
    };
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/management.js') }}?v={{ time() }}"></script>
@endpush

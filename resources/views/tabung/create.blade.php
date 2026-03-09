<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Target - Sakuin Aja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-success text-white rounded-top-4">
                        <h5 class="mb-0">🎯 Tambah Target Tabungan</h5>
                    </div>

                    <div class="card-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tabung.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Target</label>
                                <input
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    placeholder="Contoh: Beli Laptop"
                                    value="{{ old('nama') }}"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Harga Target</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input
                                        type="number"
                                        name="harga_target"
                                        class="form-control"
                                        placeholder="Contoh: 5000000"
                                        value="{{ old('harga_target') }}"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rencana Nabung per Hari</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input
                                        type="number"
                                        name="rencana_per_hari"
                                        class="form-control"
                                        placeholder="Opsional, misal 10000"
                                        value="{{ old('rencana_per_hari') }}"
                                    >
                                </div>
                                <small class="text-muted">Boleh dikosongkan kalau belum ada target harian.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input
                                    type="date"
                                    name="mulai"
                                    class="form-control"
                                    value="{{ old('mulai', now()->format('Y-m-d')) }}"
                                >
                                <small class="text-muted">Opsional, tapi bagus untuk pencatatan progress.</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success fw-bold">
                                    Simpan Target
                                </button>
                                <a href="{{ route('tabung.index') }}" class="btn btn-light border text-secondary">
                                    Batal
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
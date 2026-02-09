<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tabungan - Sakuin Aja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">✏️ Edit Tabungan</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="{{ route('tabung.update', $tabung->id) }}" method="POST">
                            @csrf 
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Penabung</label>
                                <input type="text" name="nama" value="{{ $tabung->nama }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Uang (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_tabung" value="{{ $tabung->jumlah_tabung }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 shadow-sm">
                                <small class="fw-bold d-block mb-1">ℹ️ Informasi Sistem:</small>
                                <ul class="mb-0 ps-3 small text-muted">
                                    <li>Total saldo akan dihitung ulang otomatis.</li>
                                    <li>Tanggal akan diupdate ke <b>Hari Ini ({{ date('d-m-Y') }})</b>.</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-warning fw-bold">💾 Update Sekarang</button>
                                <a href="{{ route('tabung.index') }}" class="btn btn-light text-secondary">Batal</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tabungan - Sakuin Aja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5"> 
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">➕ Nabung Yuk!</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="{{ route('tabung.store') }}" method="POST">
                            @csrf 

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Penabung</label>
                                <input type="text" name="nama" class="form-control" placeholder="Siapa yang nabung?" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Mau Nabung Berapa?</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="jumlah_tabung" class="form-control" placeholder="0" required>
                                </div>
                                <small class="text-muted fst-italic">
                                    *Total saldo dan Tanggal akan dihitung otomatis oleh sistem.
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary fw-bold">💰 Simpan Tabungan</button>
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
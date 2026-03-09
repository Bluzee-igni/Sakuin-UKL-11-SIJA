<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemasukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h3 class="mb-3">Tambah Pemasukan</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incomes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tipe</label>
            <select name="tipe" class="form-control" required>
                <option value="">-- Pilih tipe --</option>
                <option value="gaji">Gaji</option>
                <option value="uang_bulanan">Uang Bulanan</option>
                <option value="freelance">Freelance</option>
                <option value="bonus">Bonus</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Sumber</label>
            <input type="text" name="sumber" class="form-control" placeholder="Contoh: Kantor / Orang tua">
        </div>

        <div class="mb-3">
            <label class="form-label">Nominal</label>
            <input type="number" name="nominal" class="form-control" placeholder="Contoh: 3000000" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control" placeholder="Opsional">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
</body>
</html>
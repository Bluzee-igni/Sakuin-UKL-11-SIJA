<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Income</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-light py-4">

<div class="container" style="max-width: 600px;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Tambah Income</h4>
        <a href="{{ route('tabung.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ALERT --}}
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

    {{-- FORM --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('incomes.store') }}" method="POST">
                @csrf

                {{-- TIPE --}}
                <div class="mb-3">
                    <label class="form-label">Tipe Income</label>
                    <select name="tipe" class="form-control" required>
                        <option value="">-- Pilih tipe --</option>
                        <option value="gaji">Gaji</option>
                        <option value="uang_bulanan">Uang Bulanan</option>
                        <option value="freelance">Freelance</option>
                        <option value="bonus">Bonus</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                {{-- SUMBER --}}
                <div class="mb-3">
                    <label class="form-label">Sumber</label>
                    <input type="text" name="sumber" class="form-control"
                           placeholder="Contoh: Kantor / Orang tua">
                </div>

                {{-- NOMINAL --}}
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" name="nominal" class="form-control"
                           placeholder="Contoh: 3000000" required>
                </div>

                {{-- TANGGAL --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" required>
                </div>

                {{-- CATATAN --}}
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="catatan" class="form-control"
                           placeholder="Opsional">
                </div>

                {{-- BUTTON --}}
                <button type="submit" class="btn btn-success w-100">
                    Simpan Income
                </button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
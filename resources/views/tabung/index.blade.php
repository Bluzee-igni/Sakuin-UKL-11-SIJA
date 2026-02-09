<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakuin Aja - Daftar Tabungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: none; }
    </style>
</head>
<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold text-primary">💰 Daftar Tabungan</h3>
                        <a href="{{ route('tabung.create') }}" class="btn btn-success">
                            + Tambah Data
                        </a>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penabung</th>
                                    <th>Jumlah Setor</th>
                                    <th>Total Saldo</th>
                                    <th>Tanggal</th>
                                    <th width="150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $key => $t)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    
                                    <td class="fw-bold">{{ $t->nama }}</td>
                                    
                                    <td class="text-end">Rp {{ number_format($t->jumlah_tabung, 0, ',', '.') }}</td>
                                    <td class="text-end text-success fw-bold">Rp {{ number_format($t->total_tabungan, 0, ',', '.') }}</td>
                                    
                                    <td class="text-center">{{ date('d M Y', strtotime($t->tanggal)) }}</td>
                                    
                                    <td class="text-center">
                                        <form action="{{ route('tabung.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            
                                            <a class="btn btn-sm btn-warning text-white" href="{{ route('tabung.edit', $t->id) }}">
                                                Edit
                                            </a>

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada data tabungan. Yuk nabung!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
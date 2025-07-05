@extends('layout.be.template')

@section('title', 'Data Meja Billiard')

{{-- @push('css') untuk DataTables dihapus --}}

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title">Data Meja Billiard</h5>
                </div>
                <div class="col-md-6 text-end">
                    {{-- Tombol Tambah Data --}}
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
                        <i class="ti ti-plus"></i> Tambah Data
                    </button>
                </div>
            </div>
            {{-- BARU: Form Pencarian ditambahkan di bawah header --}}
            <div class="row mt-3">
                <div class="col-md-6 ms-auto">
                    <form action="{{ route('meja_billiard.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari no. meja / tipe..."
                                value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="ti ti-search"></i></button>
                            <a href="{{ route('meja_billiard.index') }}" class="btn btn-outline-secondary" title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {{-- ID myTable dihapus --}}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Meja</th>
                            <th>Tipe Meja</th>
                            <th>Harga per Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr>
                                {{-- DIUBAH: Penomoran disesuaikan dengan pagination --}}
                                <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td>{{ $row->nomor_meja }}</td>
                                <td>{{ $row->tipe_meja ?? '-' }}</td>
                                <td>Rp {{ number_format($row->harga_per_jam, 0, ',', '.') }}</td>
                                <td>
                                    @if ($row->status == 'tersedia')
                                        <span class="badge bg-light-success text-success">Tersedia</span>
                                    @elseif($row->status == 'digunakan')
                                        <span class="badge bg-light-danger text-danger">Digunakan</span>
                                    @else
                                        <span class="badge bg-light-warning text-dark">Perbaikan</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editDataModal-{{ $row->meja_id }}" style="border-radius: 50px;">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>

                                    <form action="{{ route('meja_billiard.destroy', $row->meja_id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 50px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    @if (!empty($search))
                                        Data tidak ditemukan untuk pencarian "{{ $search }}".
                                    @else
                                        Belum ada data meja billiard.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- BARU: Tautan Pagination --}}
            <div class="mt-3">
                {{ $data->appends(['search' => $search ?? ''])->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDataModalLabel">Tambah Meja Billiard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('meja_billiard.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        {{-- Form untuk tambah data --}}
                        <div class="mb-3">
                            <label for="nomor_meja" class="form-label">Nomor Meja</label>
                            <input type="text" class="form-control @error('nomor_meja', 'store') is-invalid @enderror"
                                name="nomor_meja" value="{{ old('nomor_meja') }}" placeholder="Contoh: 01 atau VIP-1">
                            @error('nomor_meja', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tipe_meja" class="form-label">Tipe Meja</label>
                            <select class="form-select @error('tipe_meja', 'store') is-invalid @enderror" name="tipe_meja">
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="9-ft Reguler" {{ old('tipe_meja') == '9-ft Reguler' ? 'selected' : '' }}>
                                    9-ft
                                    Reguler</option>
                                <option value="9-ft VIP" {{ old('tipe_meja') == '9-ft VIP' ? 'selected' : '' }}>9-ft VIP
                                </option>
                                <option value="8-ft Reguler" {{ old('tipe_meja') == '8-ft Reguler' ? 'selected' : '' }}>
                                    8-ft Reguler</option>
                                <option value="7-ft Reguler" {{ old('tipe_meja') == '7-ft Reguler' ? 'selected' : '' }}>
                                    7-ft Reguler</option>
                                <option value="Snooker" {{ old('tipe_meja') == 'Snooker' ? 'selected' : '' }}>Snooker
                                </option>
                            </select>
                            @error('tipe_meja', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                            <input type="number" class="form-control @error('harga_per_jam', 'store') is-invalid @enderror"
                                name="harga_per_jam" value="{{ old('harga_per_jam') }}" placeholder="Contoh: 50000">
                            @error('harga_per_jam', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status', 'store') is-invalid @enderror" name="status">
                                <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia
                                </option>
                                <option value="digunakan" {{ old('status') == 'digunakan' ? 'selected' : '' }}>Digunakan
                                </option>
                                <option value="perbaikan" {{ old('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan
                                </option>
                            </select>
                            @error('status', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($data as $row)
        <div class="modal fade" id="editDataModal-{{ $row->meja_id }}" tabindex="-1"
            aria-labelledby="editDataModalLabel-{{ $row->meja_id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDataModalLabel-{{ $row->meja_id }}">Edit Meja Billiard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('meja_billiard.update', $row->meja_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nomor_meja" class="form-label">Nomor Meja</label>
                                <input type="text"
                                    class="form-control @error('nomor_meja', 'update') is-invalid @enderror"
                                    name="nomor_meja" value="{{ old('nomor_meja', $row->nomor_meja) }}">
                                @error('nomor_meja', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tipe_meja" class="form-label">Tipe Meja</label>
                                <select class="form-select @error('tipe_meja', 'update') is-invalid @enderror"
                                    name="tipe_meja">
                                    <option value="9-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '9-ft Reguler' ? 'selected' : '' }}>9-ft
                                        Reguler</option>
                                    <option value="9-ft VIP"
                                        {{ old('tipe_meja', $row->tipe_meja) == '9-ft VIP' ? 'selected' : '' }}>9-ft VIP
                                    </option>
                                    <option value="8-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '8-ft Reguler' ? 'selected' : '' }}>8-ft
                                        Reguler</option>
                                    <option value="7-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '7-ft Reguler' ? 'selected' : '' }}>7-ft
                                        Reguler</option>
                                    <option value="Snooker"
                                        {{ old('tipe_meja', $row->tipe_meja) == 'Snooker' ? 'selected' : '' }}>Snooker
                                    </option>
                                </select>
                                @error('tipe_meja', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                                <input type="number"
                                    class="form-control @error('harga_per_jam', 'update') is-invalid @enderror"
                                    name="harga_per_jam" value="{{ old('harga_per_jam', (int) $row->harga_per_jam) }}">
                                @error('harga_per_jam', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status', 'update') is-invalid @enderror"
                                    name="status">
                                    <option value="tersedia"
                                        {{ old('status', $row->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="digunakan"
                                        {{ old('status', $row->status) == 'digunakan' ? 'selected' : '' }}>Digunakan
                                    </option>
                                    <option value="perbaikan"
                                        {{ old('status', $row->status) == 'perbaikan' ? 'selected' : '' }}>Perbaikan
                                    </option>
                                </select>
                                @error('status', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('js')
    {{-- Hapus script DataTables, pertahankan script SweetAlert --}}
    <script>
        // SCRIPT SWEETALERT (Tidak ada perubahan)
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        // SCRIPT UNTUK MENAMPILKAN MODAL JIKA ADA ERROR VALIDASI
        @if ($errors->any())
            var errorModalId = '';
            // Cek jika error berasal dari form 'store'
            @if ($errors->hasBag('store'))
                errorModalId = 'tambahDataModal';
                // Cek jika error berasal dari form 'update'
            @elseif (session('failed_id'))
                var failedId = '{{ session('failed_id') }}';
                errorModalId = 'editDataModal-' + failedId;
            @endif

            if (errorModalId) {
                var myModal = new bootstrap.Modal(document.getElementById(errorModalId), {
                    keyboard: false
                });
                myModal.show();
            }
        @endif
    </script>
@endpush

@extends('layout.be.template')

@section('title', 'Data Pelanggan')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Data Semua Pelanggan</h5>
                </div>
                <div class="col-md-6">
                    {{-- BARU: Form Pencarian --}}
                    <form action="{{ route('pelanggan.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama, username, email..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="ti ti-search"></i> Cari
                            </button>
                            <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary" title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-3">
                {{-- ID myTable dihapus --}}
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Nomor Telepon</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr>
                                {{-- DIUBAH: Penomoran disesuaikan dengan pagination --}}
                                <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td>{{ $row->nama_lengkap }}</td>
                                <td>{{ $row->user->name }}</td>
                                <td>{{ $row->user->email }}</td>
                                <td>{{ $row->nomor_telepon }}</td>
                                <td>{{ $row->jenis_kelamin }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editDataModal-{{ $row->pelanggan_id }}"
                                        style="border-radius: 50px;">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form action="{{ route('pelanggan.destroy', $row->pelanggan_id) }}" method="POST"
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
                                <td colspan="7" class="text-center">
                                    Data tidak ditemukan.
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

    {{-- Modal Edit Data (Tidak ada perubahan) --}}
    @foreach ($data as $row)
        <div class="modal fade" id="editDataModal-{{ $row->pelanggan_id }}" tabindex="-1"
            aria-labelledby="editDataModalLabel-{{ $row->pelanggan_id }}" aria-hidden="true">
            {{-- Isi modal tetap sama --}}
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDataModalLabel-{{ $row->pelanggan_id }}">Edit Pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('pelanggan.update', $row->pelanggan_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            {{-- User Data --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Username</label>
                                <input type="text"
                                    class="form-control @error('name', "update-{$row->pelanggan_id}") is-invalid @enderror"
                                    name="name" value="{{ old('name', $row->user->name) }}">
                                @error('name', "update-{$row->pelanggan_id}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control @error('email', "update-{$row->pelanggan_id}") is-invalid @enderror"
                                    name="email" value="{{ old('email', $row->user->email) }}">
                                @error('email', "update-{$row->pelanggan_id}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr>
                            {{-- Pelanggan Data --}}
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text"
                                    class="form-control @error('nama_lengkap', "update-{$row->pelanggan_id}") is-invalid @enderror"
                                    name="nama_lengkap" value="{{ old('nama_lengkap', $row->nama_lengkap) }}">
                                @error('nama_lengkap', "update-{$row->pelanggan_id}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select
                                    class="form-select @error('jenis_kelamin', "update-{$row->pelanggan_id}") is-invalid @enderror"
                                    name="jenis_kelamin">
                                    <option value="Laki-laki"
                                        {{ old('jenis_kelamin', $row->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="Perempuan"
                                        {{ old('jenis_kelamin', $row->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jenis_kelamin', "update-{$row->pelanggan_id}")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                <input type="text"
                                    class="form-control @error('nomor_telepon', "update-{$row->pelanggan_id}") is-invalid @enderror"
                                    name="nomor_telepon" value="{{ old('nomor_telepon', $row->nomor_telepon) }}">
                                @error('nomor_telepon', "update-{$row->pelanggan_id}")
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
        // SweetAlert Toast untuk sukses/error (Tidak ada perubahan)
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
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        // SweetAlert untuk konfirmasi hapus (Tidak ada perubahan)
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data pelanggan dan akun login yang terkait akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
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

        // Membuka kembali modal jika ada error validasi
        // Perlu sedikit modifikasi di controller agar ini bekerja dengan baik
        @if ($errors->any() && session('failed_id'))
            var failedId = '{{ session('failed_id') }}';
            var myModal = new bootstrap.Modal(document.getElementById('editDataModal-' + failedId), {
                keyboard: false
            });
            myModal.show();
        @endif
    </script>
@endpush

@extends('layout.be.template')

@section('title', 'Data Pelanggan')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Data Semua Pelanggan</h5>
            <div class="table-responsive mt-3">
                <table id="myTable" class="table table-bordered table-striped">
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
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->nama_lengkap }}</td>
                                <td>{{ $row->user->name }}</td>
                                <td>{{ $row->user->email }}</td>
                                <td>{{ $row->nomor_telepon }}</td>
                                <td>{{ $row->jenis_kelamin }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editDataModal-{{ $row->pelanggan_id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('pelanggan.destroy', $row->pelanggan_id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data -->
    @foreach ($data as $row)
        <div class="modal fade" id="editDataModal-{{ $row->pelanggan_id }}" tabindex="-1"
            aria-labelledby="editDataModalLabel-{{ $row->pelanggan_id }}" aria-hidden="true">
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
                                <input type="text" class="form-control @error('name', 'update') is-invalid @enderror"
                                    name="name" value="{{ old('name', $row->user->name) }}">
                                @error('name', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email', 'update') is-invalid @enderror"
                                    name="email" value="{{ old('email', $row->user->email) }}">
                                @error('email', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr>
                            {{-- Pelanggan Data --}}
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text"
                                    class="form-control @error('nama_lengkap', 'update') is-invalid @enderror"
                                    name="nama_lengkap" value="{{ old('nama_lengkap', $row->nama_lengkap) }}">
                                @error('nama_lengkap', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin', 'update') is-invalid @enderror"
                                    name="jenis_kelamin">
                                    <option value="Laki-laki"
                                        {{ old('jenis_kelamin', $row->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="Perempuan"
                                        {{ old('jenis_kelamin', $row->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jenis_kelamin', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                <input type="text"
                                    class="form-control @error('nomor_telepon', 'update') is-invalid @enderror"
                                    name="nomor_telepon" value="{{ old('nomor_telepon', $row->nomor_telepon) }}">
                                @error('nomor_telepon', 'update')
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
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#myTable');

        // SweetAlert Toast untuk sukses/error
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

        // SweetAlert untuk konfirmasi hapus
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
        @if ($errors->hasBag('update'))
            var failedId = '{{ session('failed_id') }}';
            var myModal = new bootstrap.Modal(document.getElementById('editDataModal-' + failedId), {
                keyboard: false
            });
            myModal.show();
        @endif
    </script>
@endpush

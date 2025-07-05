@extends('layout.be.template')

@section('title', 'Profil Saya')

@push('css')
    <style>
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>

    @section('content')
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-1">Profil Saya</h5>
                        <p class="text-muted mb-0">Lihat dan kelola informasi akun Anda.</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal"
                        style="border-radius: 50px">
                        <i class="fas fa-edit me-1"></i> Edit Profil
                    </button>
                </div>

                {{-- Info Ringkasan Profil --}}
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Username:</strong> {{ $user->name }}
                            </li>
                            <li class="list-group-item">
                                <strong>Email:</strong> {{ $user->email }}
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-6">
                        @if ($user->role == 'admin')
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Nama Admin:</strong> {{ $user->admin->nama_admin }}</li>
                                <li class="list-group-item"><strong>No HP:</strong> {{ $user->admin->no_hp }}</li>
                                <li class="list-group-item"><strong>Alamat:</strong> {{ $user->admin->alamat }}</li>
                            </ul>
                        @elseif ($user->role == 'pelanggan')
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Nama Lengkap:</strong> {{ $user->pelanggan->nama_lengkap }}
                                </li>
                                <li class="list-group-item"><strong>Nomor Telepon:</strong>
                                    {{ $user->pelanggan->nomor_telepon }}</li>
                                <li class="list-group-item"><strong>Jenis Kelamin:</strong>
                                    {{ $user->pelanggan->jenis_kelamin }}</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit Profil --}}
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <h6 class="mb-3">Informasi Akun</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name', $user->name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}"
                                        readonly>
                                </div>
                            </div>

                            <hr class="my-3">

                            @if ($user->role == 'admin')
                                <h6 class="mb-3">Profil Admin</h6>
                                <div class="mb-3">
                                    <label for="nama_admin" class="form-label">Nama Admin</label>
                                    <input type="text" class="form-control" name="nama_admin"
                                        value="{{ old('nama_admin', $user->admin->nama_admin) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" name="no_hp"
                                        value="{{ old('no_hp', $user->admin->no_hp) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" name="alamat" rows="3">{{ old('alamat', $user->admin->alamat) }}</textarea>
                                </div>
                            @elseif ($user->role == 'pelanggan')
                                <h6 class="mb-3">Profil Pelanggan</h6>
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_lengkap"
                                        value="{{ old('nama_lengkap', $user->pelanggan->nama_lengkap) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="nomor_telepon"
                                        value="{{ old('nomor_telepon', $user->pelanggan->nomor_telepon) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin', $user->pelanggan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin', $user->pelanggan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                            @endif

                            <hr class="my-3">
                            <h6 class="mb-2">Ganti Password</h6>
                            <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="btn-simpan" class="btn btn-primary" style="border-radius: 50px">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                    aria-hidden="true"></span>
                                Simpan
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="border-radius: 50px">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('js')
        <script>
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
        </script>
        <script>
            @if ($errors->any())
                let errorMsg = '';
                @foreach ($errors->all() as $error)
                    errorMsg += "- {{ $error }}\n";
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan!',
                    text: 'Silakan periksa kembali isian Anda.',
                    footer: `<pre class="text-start" style="white-space: pre-wrap;">${errorMsg}</pre>`,
                    confirmButtonText: 'Tutup'
                });
            @endif
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('#editProfileModal form');
                const btnSimpan = document.getElementById('btn-simpan');
                const spinner = btnSimpan.querySelector('.spinner-border');

                form.addEventListener('submit', function() {
                    btnSimpan.setAttribute('disabled', true);
                    spinner.classList.remove('d-none');
                    btnSimpan.innerHTML =
                        `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...`;
                });
            });
        </script>
    @endpush

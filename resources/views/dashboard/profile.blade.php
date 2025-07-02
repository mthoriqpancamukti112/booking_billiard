@extends('layout.be.template')

@section('title', 'Profil Saya')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Profil Saya</h5>
            <p>Perbarui informasi profil dan akun Anda di sini.</p>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Bagian Akun (Sama untuk semua role) --}}
                <h6 class="mt-4">Informasi Akun</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Username</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        {{-- Input email dibuat readonly --}}
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                            readonly>
                        <small class="form-text text-muted">Email tidak dapat diubah.</small>
                    </div>
                </div>

                <hr>

                {{-- Bagian Profil (Berbeda sesuai role) --}}
                @if (Auth::user()->role == 'admin')
                    <h6 class="mt-4">Profil Admin</h6>
                    <div class="mb-3">
                        <label for="nama_admin" class="form-label">Nama Admin</label>
                        <input type="text" class="form-control @error('nama_admin') is-invalid @enderror" id="nama_admin"
                            name="nama_admin" value="{{ old('nama_admin', $user->admin->nama_admin) }}">
                        @error('nama_admin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp"
                            name="no_hp" value="{{ old('no_hp', $user->admin->no_hp) }}">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $user->admin->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif(Auth::user()->role == 'pelanggan')
                    <h6 class="mt-4">Profil Pelanggan</h6>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                            id="nama_lengkap" name="nama_lengkap"
                            value="{{ old('nama_lengkap', $user->pelanggan->nama_lengkap) }}">
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror"
                            id="nomor_telepon" name="nomor_telepon"
                            value="{{ old('nomor_telepon', $user->pelanggan->nomor_telepon) }}">
                        @error('nomor_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin"
                            name="jenis_kelamin">
                            <option value="Laki-laki"
                                {{ old('jenis_kelamin', $user->pelanggan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="Perempuan"
                                {{ old('jenis_kelamin', $user->pelanggan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <hr>

                {{-- Ganti Password --}}
                <h6 class="mt-4">Ganti Password (Opsional)</h6>
                <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Profil</button>
            </form>
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
@endpush

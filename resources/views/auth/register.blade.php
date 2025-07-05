<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Billiard</title>
    <link rel="shortcut icon" type="image/png" href="/backend/assets/images/logos/logo.png" />
    <link rel="stylesheet" href="/backend/assets/css/styles.min.css" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="/backend/assets/images/logos/logo.png" width="180" alt="">
                                </a>
                                <p class="text-center">Daftar Sebagai Pelanggan</p>

                                <form action="{{ route('register.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="InputName" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="InputName" name="name"
                                            value="{{ old('name') }}" placeholder="Masukkan username Anda"
                                            style="border-radius: 50px;">

                                        @error('name')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="InputEmail" name="email"
                                            value="{{ old('email') }}" placeholder="Masukkan alamat email Anda"
                                            style="border-radius: 50px;">

                                        @error('email')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="InputPassword" name="password"
                                            placeholder="Minimal 8 karakter" style="border-radius: 50px;">

                                        @error('password')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputNamaLengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="InputNamaLengkap"
                                            name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                            placeholder="Masukkan nama lengkap Anda" style="border-radius: 50px;">

                                        @error('nama_lengkap')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="InputJenisKelamin" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" id="InputJenisKelamin" name="jenis_kelamin"
                                            style="border-radius: 50px;">
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <option value="Laki-laki"
                                                {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="Perempuan"
                                                {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>

                                        @error('jenis_kelamin')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="InputNomorTelepon" class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="InputNomorTelepon"
                                            name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                                            placeholder="Contoh: 081234567890" style="border-radius: 50px;">

                                        @error('nomor_telepon')
                                            <div class="text-danger mt-1 small">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                        class="btn btn-primary w-100 fs-4 mb-4 rounded-5">Daftar</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="mb-0 fw-bold">Sudah punya akun?</p>
                                        <a class="text-primary fw-bold ms-2"
                                            href="{{ route('login.index') }}">Login</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/backend/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/backend/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Registrasi Gagal!',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
</body>

</html>

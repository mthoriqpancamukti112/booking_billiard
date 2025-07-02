<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Billiard</title>
    <link rel="shortcut icon" type="image/png" href="/backend/assets/images/logos/logo.png" />
    <link rel="stylesheet" href="/backend/assets/css/styles.min.css" />
    <style>
        .password-container {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            top: 70%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
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
                                    <img src="/backend/assets/images/logos/logo.png" width="100" alt="">
                                </a>
                                <p class="text-center">Silakan Login</p>

                                <form action="{{ route('login.auth') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="InputEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="InputEmail" name="email"
                                            placeholder="Masukan email anda" required>
                                    </div>
                                    <div class="mb-4 password-container">
                                        <label for="InputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="InputPassword" name="password"
                                            placeholder="Masukan password anda" required>
                                        <i class="ti ti-eye-off password-toggle-icon" id="togglePassword"></i>
                                    </div>

                                    <button type="submit"
                                        class="btn btn-primary w-100 fs-4 mb-4 rounded-5">Login</button>

                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-bold">Belum punya akun?</p>
                                        <a class="text-primary fw-bold ms-2" href="{{ route('register.index') }}">Buat
                                            Akun</a>
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
        // Menampilkan alert jika ada pesan sukses dari session (setelah registrasi)
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

        // Menampilkan alert jika ada pesan error dari session (login gagal)
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#InputPassword');

            togglePassword.addEventListener('click', function() {
                // Ganti tipe input
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Ganti ikon mata
                this.classList.toggle('ti-eye-off');
                this.classList.toggle('ti-eye');
            });
        });
    </script>
</body>

</html>

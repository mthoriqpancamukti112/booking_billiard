@extends('layout.be.template')

@section('title', 'Booking Meja')

@push('css')
    {{-- CSS Kustom untuk Kartu Meja --}}
    <style>
        .card-meja {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease-in-out;
            height: 100%;
        }

        .card-meja .card-header {
            border-bottom: 1px solid #dee2e6;
        }

        .card-meja.status-tersedia {
            border-left: 5px solid #13deb9;
            /* Success color */
        }

        .card-meja.status-digunakan {
            border-left: 5px solid #ff5b5b;
            /* Danger color */
        }

        .card-meja.status-perbaikan {
            border-left: 5px solid #ffae1f;
            /* Warning color */
            background-color: #f8f9fa;
        }

        .timer-display {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2a3547;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <h3 class="fw-semibold">Pilih Meja Billiard</h3>
                <p>Pilih meja yang tersedia untuk memulai permainan.</p>
            </div>
        </div>

        {{-- Logika Notifikasi --}}
        @if ($has_active_booking)
            <div class="card bg-light-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Anda Sedang Bermain!</h5>
                    <p class="mb-0">Saat ini Anda tercatat memiliki sesi permainan yang sedang berlangsung.</p>
                </div>
            </div>
        @elseif ($first_in_line && $first_in_line->user_id == Auth::id())
            <div class="card bg-light-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Giliran Anda!</h5>
                    <p class="mb-0">Sekarang adalah giliran Anda. Silakan pilih meja yang tersedia untuk memulai
                        permainan.</p>
                </div>
            </div>
        @elseif ($is_waiting)
            <div class="card bg-light-info">
                <div class="card-body">
                    <h5 class="card-title text-info">Anda Berada di Daftar Tunggu</h5>
                    <p class="mb-0">Harap tunggu giliran Anda. Sistem akan memberitahu jika Anda sudah dipanggil.</p>
                </div>
            </div>
        @elseif ($all_unavailable)
            <div class="card bg-light-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Semua Meja Penuh!</h5>
                    <p>Anda bisa masuk ke dalam daftar tunggu untuk mendapatkan giliran berikutnya.</p>
                    <form action="{{ route('waitinglist.join') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">Masuk Daftar Tunggu</button>
                    </form>
                </div>
            </div>
        @endif

        {{-- BAGIAN DAFTAR MEJA --}}
        <div class="row">
            @foreach ($meja as $m)
                <div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-meja status-{{ $m->status }} shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0">{{ $m->nomor_meja }}</h6>
                            <span
                                class="badge {{ $m->status == 'tersedia' ? 'bg-success' : ($m->status == 'digunakan' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ ucfirst($m->status) }}</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="text-muted mb-3">{{ $m->tipe_meja }}</p>

                            @if ($m->status == 'digunakan' && $m->bookings->isNotEmpty())
                                @php $activeBooking = $m->bookings->first(); @endphp
                                <div class="text-center my-auto">
                                    <small class="text-muted">Durasi Bermain</small>
                                    <p class="timer-display" id="timer-{{ $m->meja_id }}"
                                        data-start-time="{{ $activeBooking->waktu_mulai->timestamp }}">00:00:00</p>
                                    <div class="mt-2 border-top pt-2 text-muted small">
                                        <p class="mb-0"><i class="ti ti-user-circle me-1"></i>
                                            {{ $activeBooking->user?->pelanggan?->nama_lengkap ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @elseif ($m->status == 'tersedia')
                                <div class="text-center my-auto">
                                    <h5 class="fw-semibold fs-6 mb-0">Rp {{ number_format($m->harga_per_jam, 0, ',', '.') }}
                                    </h5>
                                    <p class="text-muted small">/ jam</p>
                                </div>
                            @else
                                <div class="text-center my-auto">
                                    <i class="ti ti-tool fs-1 text-warning"></i>
                                    <p class="text-muted mt-2">Meja dalam perbaikan</p>
                                </div>
                            @endif

                            <div class="mt-auto">
                                @php
                                    // Tombol bisa diklik jika:
                                    // 1. Meja tersedia
                                    // 2. User tidak punya booking aktif
                                    // 3. (Tidak ada antrian SAMA SEKALI ATAU user adalah antrian pertama)
                                    $canBook =
                                        $m->status == 'tersedia' &&
                                        !$has_active_booking &&
                                        (!$first_in_line || $first_in_line->user_id == Auth::id());
                                @endphp

                                @if ($canBook)
                                    <form action="{{ route('booking.store') }}" method="POST" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="meja_id" value="{{ $m->meja_id }}">
                                        <button type="submit" class="btn btn-primary w-100">Booking Sekarang</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary w-100 mt-3" disabled>
                                        @if ($m->status != 'tersedia')
                                            Sudah Diboking
                                        @elseif($has_active_booking)
                                            Sesi Aktif
                                        @else
                                            Menunggu Giliran
                                        @endif
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('js')
    <script>
        // SCRIPT UNTUK SWEETALERT
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
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
                timerProgressBar: true
            });
        @endif
        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '{{ session('info') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // SCRIPT UNTUK TIMER PADA SETIAP KARTU
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer-display');

            function updateAllTimers() {
                timers.forEach(timerElement => {
                    const startTime = parseInt(timerElement.getAttribute('data-start-time'), 10);
                    if (!startTime) return;

                    const now = Math.floor(Date.now() / 1000);
                    const elapsed = now - startTime;

                    const hours = Math.floor(elapsed / 3600);
                    const minutes = Math.floor((elapsed % 3600) / 60);
                    const seconds = elapsed % 60;

                    const format = (num) => num.toString().padStart(2, '0');

                    timerElement.textContent = `${format(hours)}:${format(minutes)}:${format(seconds)}`;
                });
            }

            if (timers.length > 0) {
                setInterval(updateAllTimers, 1000);
                updateAllTimers();
            }
        });
    </script>
@endpush

@extends('layout.be.template')

@section('title', 'Dashboard')

@section('content')

    {{-- =============================================== --}}
    {{-- |           TAMPILAN UNTUK ADMIN            | --}}
    {{-- =============================================== --}}
    @if (Auth::user()->role == 'admin')
        <div class="row">
            <div class="col-lg-8">
                <div class="card welcome-card">
                    <div class="card-body">
                        <h4 class="card-title">Selamat Datang Kembali, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted">Berikut adalah ringkasan kondisi operasional billiard Anda saat ini.</p>
                    </div>
                </div>
                {{-- Tabel Booking Terbaru --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Aktivitas Booking Terbaru</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary btn-sm"
                                    title="Refresh">
                                    <i class="ti ti-refresh"></i>
                                    Refresh
                                </a>
                                <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <tbody>
                                    @forelse ($recent_bookings as $booking)
                                        <tr>
                                            <td>
                                                <h6 class="fw-semibold mb-1">
                                                    {{ $booking->user?->pelanggan?->nama_lengkap ?? 'N/A' }}</h6>
                                                <p class="fs-3 mb-0 text-muted">Meja
                                                    {{ $booking->meja?->nomor_meja ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fs-3">
                                                    {{ \Carbon\Carbon::parse($booking->waktu_mulai)->diffForHumans() }}</p>
                                            </td>
                                            <td class="text-end">
                                                @if ($booking->status_booking == 'berlangsung')
                                                    <span class="badge bg-light-info text-info fs-2">Berlangsung</span>
                                                @elseif($booking->status_booking == 'selesai')
                                                    <span class="badge bg-light-success text-success fs-2">Selesai</span>
                                                @else
                                                    <span
                                                        class="badge bg-light-primary text-primary fs-2">{{ ucfirst($booking->status_booking) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <p class="mb-0">Belum ada aktivitas booking.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                {{-- Kartu Status Meja --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Status Meja</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total Meja
                                <span class="badge bg-primary rounded-pill">{{ $total_meja }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Tersedia
                                <span class="badge bg-success rounded-pill">{{ $meja_tersedia }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Digunakan
                                <span class="badge bg-danger rounded-pill">{{ $meja_digunakan }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- Kartu Status Antrian --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Status Antrian</h5>
                        <div class="d-flex align-items-center">
                            <div
                                class="round-40 text-white d-flex align-items-center justify-content-center rounded-circle bg-warning">
                                <i class="ti ti-hourglass fs-7"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fs-4 fw-semibold">Pelanggan Menunggu</h6>
                                <p class="mb-0 fs-3">{{ $antrian_menunggu }} orang</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.waitinglist.index') }}"
                            class="btn btn-sm btn-outline-primary w-100 mt-3">Lihat Daftar Tunggu</a>
                    </div>
                </div>
            </div>
        </div>


        {{-- =============================================== --}}
        {{-- |         TAMPILAN UNTUK PELANGGAN          | --}}
        {{-- =============================================== --}}
    @elseif (Auth::user()->role == 'pelanggan')
        <div class="row">
            <div class="col-lg-8">
                {{-- KARTU STATUS UTAMA --}}
                <div class="card">
                    <div class="card-body">
                        @if ($active_booking)
                            {{-- Tampilan jika sedang bermain --}}
                            <h5 class="card-title text-success"><i class="ti ti-player-play me-2"></i>Anda Sedang Bermain
                            </h5>
                            <p>Sesi permainan Anda sedang berlangsung di <strong>Meja
                                    {{ $active_booking->meja->nomor_meja }}</strong>.</p>
                            <div class="d-flex align-items-center justify-content-center bg-light p-3 rounded my-3">
                                <i class="ti ti-clock-hour-4 fs-7 me-3"></i>
                                <div>
                                    <h4 class="mb-0" id="timer"
                                        data-start-time="{{ $active_booking->waktu_mulai->timestamp }}">00:00:00</h4>
                                    <small class="text-muted">Durasi Permainan</small>
                                </div>
                            </div>
                            <p class="text-center text-muted small">Hubungi admin untuk menyelesaikan sesi permainan Anda.
                            </p>
                        @elseif ($my_queue)
                            @if ($my_queue->status_antrian == 'dipanggil')
                                {{-- Tampilan jika dipanggil --}}
                                <div class="text-center">
                                    <i class="ti ti-bell-ringing fs-5 text-primary"></i>
                                    <h5 class="card-title mt-3 text-primary">Sekarang Giliran Anda!</h5>
                                    <p class="mb-4">Anda telah dipanggil dari daftar tunggu. Silakan pilih meja yang
                                        tersedia untuk memulai permainan.</p>
                                    <a href="{{ route('booking_meja.index') }}" class="btn btn-primary">Pilih Meja
                                        Sekarang</a>
                                </div>
                            @else
                                {{-- Tampilan jika sedang menunggu --}}
                                <div class="text-center">
                                    <i class="ti ti-hourglass fs-5 text-warning"></i>
                                    <h5 class="card-title mt-3">Anda di Daftar Tunggu</h5>
                                    <p>Posisi antrian Anda saat ini adalah nomor:</p>
                                    <h1 class="display-4 fw-bolder">{{ $position }}</h1>
                                    <p class="text-muted">Harap tunggu giliran Anda. Sistem akan memberitahu jika Anda sudah
                                        dipanggil.</p>
                                </div>
                            @endif
                        @else
                            {{-- Tampilan jika tidak ada aktivitas --}}
                            <div class="text-center">
                                <i class="ti ti-mood-smile fs-5 text-info"></i>
                                <h5 class="card-title mt-3">Selamat Datang, {{ Auth::user()->name }}!</h5>
                                <p class="mb-4">Anda tidak memiliki sesi bermain atau antrian yang aktif. Siap untuk
                                    bermain?</p>
                                <a href="{{ route('booking_meja.index') }}" class="btn btn-primary">Lihat Meja &
                                    Booking</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                {{-- KARTU STATISTIK PELANGGAN --}}
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Statistik Anda</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total Booking
                                <span class="badge bg-info rounded-pill">{{ $total_bookings }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total Waktu Bermain
                                <span class="badge bg-primary rounded-pill">{{ floor($total_time_played / 60) }} jam
                                    {{ $total_time_played % 60 }} m</span>
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- KARTU BOOKING TERAKHIR --}}
                @if ($last_booking)
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Permainan Terakhir</h6>
                            <p class="mb-1"><i class="ti ti-table me-2"></i>Meja
                                {{ $last_booking->meja?->nomor_meja ?? 'N/A' }}</p>
                            <p class="mb-2"><i
                                    class="ti ti-calendar me-2"></i>{{ \Carbon\Carbon::parse($last_booking->waktu_selesai)->format('d M Y') }}
                            </p>
                            <a href="{{ route('booking.history') }}" class="btn btn-sm btn-outline-primary w-100">Lihat
                                Semua Riwayat</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

@endsection

@push('js')
    {{-- Skrip untuk timer jika ada booking aktif --}}
    @if (Auth::user()->role == 'pelanggan' && isset($active_booking))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const timerElement = document.getElementById('timer');
                const startTime = parseInt(timerElement.getAttribute('data-start-time'), 10);

                function updateTimer() {
                    const now = Math.floor(Date.now() / 1000);
                    const elapsed = now - startTime;

                    const hours = Math.floor(elapsed / 3600);
                    const minutes = Math.floor((elapsed % 3600) / 60);
                    const seconds = elapsed % 60;

                    const format = (num) => num.toString().padStart(2, '0');

                    timerElement.textContent = `${format(hours)}:${format(minutes)}:${format(seconds)}`;
                }

                // Update timer setiap detik
                setInterval(updateTimer, 1000);
                updateTimer(); // Panggil sekali saat load
            });
        </script>
    @endif
@endpush

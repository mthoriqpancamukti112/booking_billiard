@extends('layout.be.template')

@section('title', 'Dashboard')

@push('css')
    <style>
        .stat-card {
            border-left: 5px solid #5d87ff;
        }

        .stat-label {
            font-weight: 600;
            color: #555;
        }
    </style>
@endpush

@section('content')

    {{-- =============================================== --}}
    {{-- |           TAMPILAN UNTUK ADMIN            | --}}
    {{-- =============================================== --}}
    @if (Auth::user()->role == 'admin')
        <div class="row">
            <div class="col-md-12">
                <div class="card welcome-card">
                    <div class="card-body">
                        <h4 class="card-title">Selamat Datang Kembali, {{ Auth::user()->name }}!</h4>
                        <p class="text-muted">Berikut adalah ringkasan operasional billiard Anda saat ini.</p>
                    </div>
                </div>
            </div>

            {{-- Kartu Statistik Harian --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h5 class="card-title">Total Booking Hari Ini</h5>
                        <h3 class="fw-bold">{{ $total_bookings_today }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-money-check-alt fa-2x text-success mb-2"></i>
                        <h5 class="card-title">Pembayaran Berhasil</h5>
                        <h3 class="fw-bold">{{ $paid_today }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <h5 class="card-title">Pembayaran Gagal</h5>
                        <h3 class="fw-bold">{{ $failed_today }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                        <h5 class="card-title">Total Pendapatan Hari Ini</h5>
                        <h3 class="fw-bold">Rp {{ number_format($total_income_today, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            {{-- Chart Booking Harian --}}
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Grafik Booking Mingguan</h5>
                            <canvas id="bookingChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Booking Saat Ini --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Aktivitas Booking Saat Ini</h5>
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-outline-primary"
                                style="border-radius: 50px;">Lihat Semua</a>
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
                                                <p class="mb-0 fs-3 fw-semibold">
                                                    {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                                </p>
                                            </td>
                                            <td class="text-end">
                                                @if ($booking->status_booking == 'berlangsung')
                                                    <span class="badge bg-light-info text-info fs-2"
                                                        style="border-radius: 50px">Berlangsung</span>
                                                @elseif($booking->status_booking == 'dipesan')
                                                    <span class="badge bg-light-primary text-primary fs-2"
                                                        style="border-radius: 50px">Akan Datang</span>
                                                @else
                                                    <span class="badge bg-light-success text-success fs-2"
                                                        style="border-radius: 50px">Selesai</span>
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

            {{-- Kartu Status Meja & Booking Segera Dimulai --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Status Meja</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">Tersedia
                                <span class="badge bg-success rounded-pill">{{ $meja_tersedia }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">Digunakan
                                <span class="badge bg-danger rounded-pill">{{ $meja_digunakan }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">Penuh (Hari
                                Ini) <span class="badge bg-secondary rounded-pill">{{ $meja_penuh }}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">Perbaikan
                                <span class="badge bg-warning text-dark rounded-pill">{{ $meja_perbaikan }}</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center px-0 border-top pt-3 mt-2">
                                <strong>Total Meja</strong> <span
                                    class="badge bg-primary rounded-pill">{{ $total_meja }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Segera Dimulai</h5>
                        @forelse ($upcoming_bookings as $booking)
                            <div class="d-flex align-items-center mb-3">
                                <div
                                    class="round-40 text-white d-flex align-items-center justify-content-center rounded-circle bg-primary">
                                    <i class="ti ti-clock-play fs-7"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fs-4 fw-semibold">
                                        {{ $booking->user?->pelanggan?->nama_lengkap ?? 'N/A' }}</h6>
                                    <p class="mb-0 fs-3 text-muted">Meja {{ $booking->meja->nomor_meja }} - Jam
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Tidak ada booking yang akan dimulai dalam waktu dekat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- |         TAMPILAN UNTUK PELANGGAN              --}}
        {{-- =============================================== --}}
    @elseif (Auth::user()->role == 'pelanggan')
        <div class="row">
            {{-- KOLOM UTAMA --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        @if ($active_booking)
                            {{-- AKTIF - BERLANGSUNG --}}
                            @if ($active_booking->status_booking == 'berlangsung')
                                <div class="text-center">
                                    <h5 class="card-title text-success mb-3">
                                        <i class="fas fa-play-circle me-2 text-success"></i>
                                        Sesi Anda Sedang Berlangsung
                                    </h5>
                                    <p>Anda sedang bermain di <strong>Meja
                                            {{ $active_booking->meja->nomor_meja }}</strong>.</p>
                                    <div class="bg-light p-3 my-3 rounded-pill">
                                        <small class="text-muted">Sisa Waktu</small>
                                        <h4 class="mb-0 timer-display"
                                            data-end-time="{{ $active_booking->waktu_selesai }}">00:00:00</h4>
                                    </div>
                                    <p class="text-muted small">Sesi akan berakhir otomatis sesuai jadwal.</p>
                                </div>

                                {{-- AKTIF - DIPESAN --}}
                            @elseif ($active_booking->status_booking == 'dipesan')
                                <div class="text-center">
                                    <h5 class="card-title text-primary mb-3">
                                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                                        Booking Dikonfirmasi
                                    </h5>
                                    <p>Anda akan bermain di <strong>Meja {{ $active_booking->meja->nomor_meja }}</strong>
                                    </p>
                                    <p class="mb-2">
                                        Jadwal:
                                        <strong>{{ \Carbon\Carbon::parse($active_booking->waktu_mulai)->translatedFormat('l, d M Y H:i') }}</strong>
                                    </p>
                                    <p>Status Pembayaran:
                                        @if ($active_booking->status_pembayaran === 'belum_bayar')
                                            <span class="badge bg-warning text-dark" style="border-radius: 50px">Belum
                                                Dibayar</span>
                                        @else
                                            <span class="badge bg-success" style="border-radius: 50px">Lunas</span>
                                        @endif
                                    </p>
                                    <div class="bg-light p-3 my-3 rounded-pill">
                                        <small class="text-muted">Waktu Dimulai Dalam</small>
                                        <h4 class="mb-0 timer-display"
                                            data-start-time="{{ $active_booking->waktu_mulai }}">00:00:00</h4>
                                    </div>
                                    <p class="text-muted small">Sesi akan dimulai otomatis. Silakan datang tepat waktu.</p>
                                </div>
                            @endif
                        @else
                            {{-- TIDAK ADA BOOKING --}}
                            <div class="text-center">
                                <i class="fas fa-mug-hot fs-2 text-info"></i>
                                <h5 class="card-title mt-3">Halo, {{ Auth::user()->name }}!</h5>
                                <p class="mb-4">Saat ini Anda belum memiliki sesi booking aktif.</p>
                                <a href="{{ route('booking_meja.index') }}" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-calendar-plus me-1"></i> Booking Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM SAMPING --}}
            <div class="col-lg-4">
                {{-- STATISTIK PELANGGAN --}}
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="fas fa-chart-line me-1 text-secondary"></i> Statistik Anda</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total Booking
                                <span class="badge bg-info rounded-pill">{{ $total_bookings }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Total Waktu Bermain
                                <span class="badge bg-primary rounded-pill">
                                    {{ floor($total_time_played / 60) }} jam {{ $total_time_played % 60 }} menit
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- BOOKING TERAKHIR --}}
                @if ($last_booking)
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-history me-1 text-muted"></i> Booking Terakhir</h6>
                            <p class="mb-1"><i class="fas fa-table me-1 text-dark"></i> Meja:
                                {{ $last_booking->meja->nomor_meja }}</p>
                            <p class="mb-1"><i class="fas fa-calendar me-1 text-dark"></i> Tanggal:
                                {{ \Carbon\Carbon::parse($last_booking->waktu_selesai)->translatedFormat('d M Y') }}</p>
                            <p class="mb-2"><i class="fas fa-clock me-1 text-dark"></i> Durasi:
                                {{ $last_booking->durasi_menit / 60 }} jam</p>
                            <a href="{{ route('booking.history') }}" class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="fas fa-list-ul me-1"></i> Lihat Riwayat
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if (Auth::user()->role == 'admin')
        <script>
            const ctx = document.getElementById('bookingChart').getContext('2d');
            const bookingChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! $chart_labels !!},
                    datasets: [{
                            label: 'Booking',
                            data: {!! $chart_data_bookings !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)'
                        },
                        {
                            label: 'Pendapatan',
                            data: {!! $chart_data_income !!},
                            backgroundColor: 'rgba(75, 192, 192, 0.6)'
                        }
                    ]
                }
            });
        </script>
    @endif

    {{-- Skrip untuk timer jika ada booking aktif --}}
    @if (Auth::user()->role == 'pelanggan' && isset($active_booking))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const timerElement = document.querySelector('.timer-display');
                if (!timerElement) return;

                const startTime = timerElement.dataset.startTime ? new Date(timerElement.dataset.startTime).getTime() :
                    null;
                const endTime = timerElement.dataset.endTime ? new Date(timerElement.dataset.endTime).getTime() : null;

                function formatTime(ms) {
                    if (ms < 0) ms = 0;
                    let hours = String(Math.floor(ms / 3600000)).padStart(2, '0');
                    let minutes = String(Math.floor((ms % 3600000) / 60000)).padStart(2, '0');
                    let seconds = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
                    return `${hours}:${minutes}:${seconds}`;
                }

                const timerInterval = setInterval(() => {
                    const now = new Date().getTime();
                    let remaining;

                    if (endTime) { // Countdown untuk sesi 'berlangsung'
                        remaining = endTime - now;
                        if (remaining <= 0) {
                            timerElement.textContent = "Waktu Habis";
                            clearInterval(timerInterval);
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            timerElement.textContent = formatTime(remaining);
                        }
                    } else if (startTime) { // Countdown untuk sesi 'dipesan'
                        remaining = startTime - now;
                        if (remaining <= 0) {
                            timerElement.textContent = "Telah Dimulai";
                            clearInterval(timerInterval);
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            timerElement.textContent = formatTime(remaining);
                        }
                    }
                }, 1000);
            });
        </script>
    @endif
@endpush

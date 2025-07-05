@extends('layout.be.template')

@section('title', 'Data Booking')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Data Semua Booking</h5>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.booking.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama, no. meja, no. hp..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="ti ti-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-secondary" title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu Booking</th>
                            <th>Biaya</th>
                            <th class="text-center">Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-0">
                                            <h6 class="fw-semibold mb-1">
                                                {{ $booking->user?->pelanggan?->nama_lengkap ?? $booking->user->name }}</h6>
                                            <p class="fs-3 mb-0 text-muted">
                                                {{ $booking->user?->pelanggan?->nomor_telepon ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="fw-semibold mb-1">{{ $booking->meja?->nomor_meja ?? 'N/A' }}</p>
                                    <p class="fs-3 mb-0 text-muted">{{ $booking->meja?->tipe_meja ?? '' }}</p>
                                </td>
                                <td>
                                    {{-- Bagian Tanggal --}}
                                    <p class="fw-semibold mb-1">
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->isoFormat('D MMM YYYY') }}
                                    </p>

                                    {{-- Bagian Detail Waktu --}}
                                    @if ($booking->status_booking == 'berlangsung')
                                        <div class="dynamic-timer" data-status="berlangsung"
                                            data-start-time="{{ $booking->waktu_mulai }}"
                                            data-end-time="{{ $booking->waktu_selesai }}">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-semibold fs-3">Sisa Waktu:</span>
                                                <span class="fs-3 time-display text-danger">Memuat...</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                                    role="progressbar" style="width: 100%" aria-valuenow="100"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($booking->status_booking == 'dipesan')
                                        <div class="dynamic-timer" data-status="dipesan"
                                            data-start-time="{{ $booking->waktu_mulai }}">
                                            <p class="fw-semibold mb-1 time-display">
                                                Dimulai dalam: <span class="text-info">Memuat...</span>
                                            </p>
                                            <p class="fs-3 mb-0 text-muted">
                                                Pukul: {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="fw-normal mb-1">
                                            <span class="fw-semibold">Pukul:</span>
                                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                        </p>
                                        <p class="fs-3 mb-0 text-muted">
                                            <span class="fw-semibold">Durasi:</span>
                                            {{ $booking->durasi_menit ? $booking->durasi_menit . ' menit' : '-' }}
                                        </p>
                                    @endif
                                </td>
                                <td>
                                    <p class="fw-semibold mb-0">
                                        {{ $booking->total_biaya ? '' . number_format($booking->total_biaya, 0, ',', '.') : '-' }}
                                    </p>
                                </td>
                                <td class="text-center">
                                    @if ($booking->status_booking == 'berlangsung')
                                        <span class="badge bg-light-info text-info fs-2">Berlangsung</span>
                                    @elseif($booking->status_booking == 'selesai')
                                        <span class="badge bg-light-success text-success fs-2">Selesai</span>
                                    @elseif($booking->status_booking == 'dibatalkan')
                                        <span class="badge bg-light-danger text-danger fs-2">Dibatalkan</span>
                                    @else
                                        <span
                                            class="badge bg-light-primary text-primary fs-2">{{ ucfirst($booking->status_booking) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($booking->status_pembayaran == 'dp_lunas')
                                        <span class="badge bg-light-success text-success">DP Lunas</span>
                                    @elseif ($booking->status_pembayaran == 'belum_bayar')
                                        <span class="badge bg-light-warning text-warning">Belum Bayar</span>
                                    @else
                                        <span class="badge bg-light-secondary text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $booking->booking_id }}"
                                        style="border-radius: 50px;">
                                        <i class="ti ti-eye"></i>
                                    </button>

                                    {{-- @if ($booking->status_pembayaran == 'belum_bayar')
                                        <form action="{{ route('booking.cancel', $booking->booking_id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                style="border-radius: 50px;">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </form>
                                    @endif --}}

                                    {{-- MODAL DETAIL BOOKING --}}
                                    <div class="modal fade" id="detailModal{{ $booking->booking_id }}" tabindex="-1"
                                        aria-labelledby="detailModalLabel{{ $booking->booking_id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="detailModalLabel{{ $booking->booking_id }}">Detail Booking</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Nama Pelanggan:</strong><br>
                                                            {{ $booking->user?->pelanggan?->nama_lengkap ?? '-' }}
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Nomor Telepon:</strong><br>
                                                            {{ $booking->user?->pelanggan?->nomor_telepon ?? '-' }}
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Meja:</strong><br>
                                                            {{ $booking->meja?->nomor_meja }}
                                                            ({{ $booking->meja?->tipe_meja }})
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Waktu:</strong><br>
                                                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y H:i') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Total Biaya:</strong><br>
                                                            Rp {{ number_format($booking->total_biaya, 0, ',', '.') }}
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Status:</strong><br>
                                                            @php
                                                                $status = $booking->status_booking;
                                                                $statusClass =
                                                                    [
                                                                        'berlangsung' => 'info',
                                                                        'selesai' => 'success',
                                                                        'dibatalkan' => 'danger',
                                                                        'dipesan' => 'primary',
                                                                    ][$status] ?? 'secondary';
                                                            @endphp
                                                            <span
                                                                class="badge bg-light-{{ $statusClass }} text-{{ $statusClass }} fs-2">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <strong>Status Pembayaran:</strong><br>
                                                            @php
                                                                $pembayaranStatus = $booking->status_pembayaran;
                                                                $pembayaranClass =
                                                                    [
                                                                        'dp_lunas' => 'success',
                                                                        'lunas' => 'success',
                                                                        'belum_bayar' => 'warning',
                                                                        'gagal' => 'danger',
                                                                    ][$pembayaranStatus] ?? 'secondary';

                                                                $formattedStatus = ucwords(
                                                                    str_replace('_', ' ', $pembayaranStatus),
                                                                );
                                                            @endphp
                                                            <span
                                                                class="badge bg-light-{{ $pembayaranClass }} text-{{ $pembayaranClass }} fs-2">
                                                                {{ $formattedStatus }}
                                                            </span>
                                                        </div>
                                                        @if ($booking->pembayaran)
                                                            <div class="col-md-6 mb-3">
                                                                <strong>Metode:</strong><br>
                                                                @php
                                                                    $metode =
                                                                        $booking->pembayaran?->metode_pembayaran ?? '-';
                                                                    $issuer = $booking->pembayaran?->issuer;
                                                                    $metodeMap = [
                                                                        'qris' => 'QRIS',
                                                                        'gopay' => 'GoPay',
                                                                        'shopeepay' => 'ShopeePay',
                                                                        'bank_transfer' => 'Bank Transfer',
                                                                        'echannel' => 'Mandiri Bill',
                                                                        'bca_klikpay' => 'BCA KlikPay',
                                                                        'credit_card' => 'Kartu Kredit',
                                                                    ];

                                                                    $metodeFormatted =
                                                                        $metodeMap[$metode] ?? ucfirst($metode);

                                                                    if ($issuer) {
                                                                        $issuerFormatted = ucwords($issuer); // Dana, BCA, BRI, dll
                                                                        $metodeFormatted .= " ({$issuerFormatted})";
                                                                    }

                                                                    $metodeClass = match ($metode) {
                                                                        'qris' => 'info',
                                                                        'gopay' => 'primary',
                                                                        'shopeepay' => 'warning',
                                                                        'bank_transfer' => 'secondary',
                                                                        default => 'light',
                                                                    };

                                                                @endphp
                                                                <span
                                                                    class="badge bg-light-{{ $metodeClass }} text-{{ $metodeClass }} fs-2">
                                                                    {{ $metodeFormatted }}
                                                                </span>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <strong>Jumlah:</strong><br>
                                                                Rp
                                                                {{ number_format($booking->pembayaran->jumlah, 0, ',', '.') }}
                                                            </div>
                                                        @else
                                                            <div class="col-12 text-muted">
                                                                <em>Belum ada data pembayaran.</em>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="mb-0">Tidak ada data booking yang ditemukan.</p>
                                    @if ($search)
                                        <p class="text-muted">Coba kata kunci lain.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $bookings->appends(['search' => $search])->links() }}
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

        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk format waktu HH:MM:SS
            function formatTime(ms) {
                if (ms < 0) ms = 0;
                let hours = String(Math.floor(ms / 3600000)).padStart(2, '0');
                let minutes = String(Math.floor((ms % 3600000) / 60000)).padStart(2, '0');
                let seconds = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`; // <-- menggunakan backtick
            }

            // Cari semua elemen timer di tabel
            const timers = document.querySelectorAll('.dynamic-timer');

            // Jalankan interval untuk setiap timer
            timers.forEach(timer => {
                const status = timer.dataset.status;
                const display = timer.querySelector('.time-display');
                const progressBar = timer.querySelector('.progress-bar');

                const intervalId = setInterval(() => {
                    const now = new Date().getTime();

                    if (status === 'berlangsung') {
                        const startTime = new Date(timer.dataset.startTime).getTime();
                        const endTime = new Date(timer.dataset.endTime).getTime();
                        const remaining = endTime - now;
                        const totalDuration = endTime - startTime;

                        if (remaining > 0) {
                            display.textContent = formatTime(remaining);
                            const percentage = (remaining / totalDuration) * 100;
                            if (progressBar) {
                                progressBar.style.width = percentage + '%';
                            }
                        } else {
                            display.textContent = "Selesai";
                            if (progressBar) {
                                progressBar.style.width = '0%';
                            }
                            clearInterval(intervalId); // Hentikan timer jika sudah selesai
                        }

                    } else if (status === 'dipesan') {
                        const startTime = new Date(timer.dataset.startTime).getTime();
                        const remaining = startTime - now;

                        if (remaining > 0) {
                            display.innerHTML =
                                `Dimulai dalam: <span class="text-info">${formatTime(remaining)}</span>`;
                        } else {
                            display.innerHTML =
                                `Dimulai dalam: <span class="text-success">Sekarang</span>`;
                            clearInterval(intervalId); // Hentikan timer jika sudah dimulai
                        }
                    }
                }, 1000);
            });
        });
    </script>
@endpush

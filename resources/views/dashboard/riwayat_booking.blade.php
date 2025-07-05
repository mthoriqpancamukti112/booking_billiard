@extends('layout.be.template')

@section('title', 'Riwayat Booking Saya')

@section('content')

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Riwayat Booking Saya</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Meja</th>
                            <th>Jadwal</th>
                            <th>Durasi</th>
                            <th>Status Booking</th>
                            <th>Pembayaran</th>
                            <th>Metode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                                <td>
                                    <strong>{{ $booking->meja?->nomor_meja ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $booking->meja?->tipe_meja ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @php
                                        $waktuMulai = \Carbon\Carbon::parse($booking->waktu_mulai);
                                    @endphp
                                    {{ $waktuMulai->isoFormat('dddd, D MMM Y') }}<br>
                                    <span class="text-muted">
                                        {{ $waktuMulai->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                    </span>
                                </td>
                                <td>{{ number_format($booking->durasi_menit / 60, 0) }} Jam</td>
                                <td>
                                    {{-- PERUBAHAN: Menambahkan kembali badge untuk Status Booking --}}
                                    @php
                                        $statusClass =
                                            [
                                                'berlangsung' => 'info',
                                                'selesai' => 'success',
                                                'dibatalkan' => 'danger',
                                                'dipesan' => 'primary',
                                            ][$booking->status_booking] ?? 'secondary';
                                    @endphp
                                    <span class="badge fs-2 bg-light-{{ $statusClass }} text-{{ $statusClass }}">
                                        {{ ucfirst($booking->status_booking) }}
                                    </span>
                                </td>
                                <td>
                                    {{-- PERUBAHAN: Menambahkan kembali badge untuk Status Pembayaran --}}
                                    @php
                                        $payClass =
                                            [
                                                'dp_lunas' => 'success',
                                                'lunas' => 'success',
                                                'belum_bayar' => 'warning',
                                                'gagal' => 'danger',
                                            ][$booking->status_pembayaran] ?? 'secondary';

                                        $payText =
                                            [
                                                'dp_lunas' => 'DP Lunas',
                                                'lunas' => 'Lunas',
                                                'belum_bayar' => 'Belum Bayar',
                                                'gagal' => 'Gagal',
                                            ][$booking->status_pembayaran] ?? '-';
                                    @endphp
                                    <span class="badge fs-2 bg-light-{{ $payClass }} text-{{ $payClass }}">
                                        {{ $payText }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $metode = $booking->pembayaran?->metode_pembayaran;

                                        $issuer = $booking->pembayaran?->issuer;

                                        // Jika metode null atau kosong, pakai default
                                        if (empty($metode) || $metode === '-') {
                                            $metode = null;
                                        }

                                        // Badge color
                                        $metodeClass = match ($metode) {
                                            'qris' => 'info',
                                            'gopay' => 'primary',
                                            'shopeepay' => 'warning',
                                            'ovo' => 'danger',
                                            'dana' => 'info',
                                            'bank_transfer' => 'secondary',
                                            'echannel' => 'secondary',
                                            'credit_card' => 'dark',
                                            'cstore' => 'light',
                                            'akulaku' => 'success',
                                            'kredivo' => 'success',
                                            default => 'danger', // NULL / tidak dikenal → warna abu tua
                                        };

                                        // Teks label
                                        $textMetode = match ($metode) {
                                            'bank_transfer' => 'Bank Transfer',
                                            'echannel' => 'Mandiri Bill',
                                            'qris' => 'QRIS',
                                            'gopay' => 'GoPay',
                                            'shopeepay' => 'ShopeePay',
                                            'dana' => 'Dana',
                                            'ovo' => 'OVO',
                                            'credit_card' => 'Kartu Kredit',
                                            'cstore' => 'Convenience Store',
                                            'akulaku' => 'Akulaku',
                                            'kredivo' => 'Kredivo',
                                            default => 'Gagal',
                                        };

                                        // Tambahkan issuer jika valid
                                        if (!empty($issuer) && $issuer !== '-') {
                                            $textMetode .= ' (' . strtoupper($issuer) . ')';
                                        }
                                    @endphp

                                    <span class="badge fs-2 bg-light-{{ $metodeClass }} text-{{ $metodeClass }}">
                                        {{ $textMetode }}
                                    </span>
                                </td>

                                <td>
                                    @if ($booking->status_pembayaran == 'belum_bayar')
                                        <a href="{{ route('booking.pay', encrypt($booking->booking_id)) }}"
                                            class="btn btn-success btn-sm" style="border-radius: 50px;">
                                            <i class="fas fa-wallet"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="mb-0">Anda belum memiliki riwayat booking.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-end">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Script notifikasi tidak diubah --}}
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status_pembayaran');
        if (status === 'success') {
            Swal.fire('Berhasil!', 'Pembayaran Anda telah dikonfirmasi.', 'success');
        } else if (status === 'pending') {
            Swal.fire('Pending', 'Pembayaran Anda sedang diproses.', 'warning');
        } else if (status === 'error') {
            Swal.fire('Gagal', 'Terjadi kesalahan saat pembayaran.', 'error');
        }
    </script>
@endpush

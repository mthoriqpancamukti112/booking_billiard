@extends('layout.be.template')

@section('title', 'Riwayat Booking Saya')

@push('css')
    {{-- CSS untuk DataTables agar tabel interaktif --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Riwayat Booking Saya</h5>
            <div class="table-responsive mt-3">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Meja</th>
                            <th>Tipe Meja</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                            <th>Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Gunakan @forelse untuk menangani jika data kosong --}}
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{-- INI BAGIAN YANG DIPERBAIKI --}}
                                <td>{{ $booking->meja?->nomor_meja ?? 'N/A' }}</td>
                                <td>{{ $booking->meja?->tipe_meja ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y, H:i') }}</td>
                                <td>
                                    @if ($booking->waktu_selesai)
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{-- Memberi warna pada status booking --}}
                                    @if ($booking->status_booking == 'berlangsung')
                                        <span class="badge bg-info">Berlangsung</span>
                                    @elseif($booking->status_booking == 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($booking->status_booking == 'dibatalkan')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-primary">{{ ucfirst($booking->status_booking) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($booking->total_biaya)
                                        Rp {{ number_format($booking->total_biaya, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Anda belum memiliki riwayat booking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- JS untuk DataTables --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        // Periksa apakah tabel memiliki isi sebelum inisialisasi
        if ($('#myTable tbody tr').length > 1 || ($('#myTable tbody tr').length === 1 && $('#myTable tbody tr td').length >
                1)) {
            new DataTable('#myTable');
        }
    </script>
@endpush

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
                    {{-- Form Pencarian --}}
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
                            <th>Detail Waktu</th>
                            <th>Biaya</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
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
                                    <p class="fw-normal mb-1">
                                        <span class="fw-semibold">Mulai:</span>
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y, H:i') }}
                                    </p>
                                    <p class="fs-3 mb-0 text-muted">
                                        <span class="fw-semibold">Durasi:</span>
                                        {{ $booking->durasi_menit ? $booking->durasi_menit . ' menit' : '-' }}
                                    </p>
                                </td>
                                <td>
                                    <p class="fw-semibold mb-0">
                                        {{ $booking->total_biaya ? 'Rp ' . number_format($booking->total_biaya, 0, ',', '.') : '-' }}
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
                                <td class="text-center">
                                    @if ($booking->status_booking == 'berlangsung')
                                        <form action="{{ route('admin.booking.finish', $booking->booking_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">Selesaikan</button>
                                        </form>
                                    @else
                                        -
                                    @endif
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
            {{-- Link Paginasi --}}
            <div class="mt-4">
                {{ $bookings->appends(['search' => $search])->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- SweetAlert sudah ada di layout utama, jadi tidak perlu di-push lagi jika sudah ada --}}
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
                timerProgressBar: true,
            });
        @endif
    </script>
@endpush

@extends('layout.be.template')

@section('title', 'Data Waiting List')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Data Waiting List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No. Urut</th>
                            <th>Nama Pelanggan</th>
                            <th>Nomor Telepon</th>
                            <th>Waktu Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waiting_list as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->user?->pelanggan?->nama_lengkap ?? 'N/A' }}</td>
                                <td>{{ $item->user?->pelanggan?->nomor_telepon ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('d M Y, H:i:s') }}</td>
                                <td>
                                    @if ($item->status_antrian == 'menunggu')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status_antrian == 'dipanggil')
                                        <span class="badge bg-info">Telah Dipanggil</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($item->status_antrian) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Daftar tunggu kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
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

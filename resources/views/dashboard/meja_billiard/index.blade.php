@extends('layout.be.template')

@section('title', 'Data Meja Billiard')

@push('css')
    {{-- CSS untuk DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Data Meja Billiard</h5>
                {{-- Tombol untuk mentrigger modal tambah --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
                    <i class="ti ti-plus"></i> Tambah Data
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table id="myTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Meja</th>
                            <th>Tipe Meja</th>
                            <th>Harga per Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->nomor_meja }}</td>
                                <td>{{ $row->tipe_meja ?? '-' }}</td>
                                <td>Rp {{ number_format($row->harga_per_jam, 0, ',', '.') }}</td>
                                <td>
                                    @if ($row->status == 'tersedia')
                                        <span class="badge bg-success">Tersedia</span>
                                    @elseif($row->status == 'digunakan')
                                        <span class="badge bg-danger">Digunakan</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Perbaikan</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tombol Edit diubah untuk mentrigger modal edit --}}
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editDataModal-{{ $row->meja_id }}">
                                        Edit
                                    </button>
                                    {{-- Form Hapus diubah untuk menggunakan SweetAlert --}}
                                    <form action="{{ route('meja_billiard.destroy', $row->meja_id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDataModalLabel">Tambah Meja Billiard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('meja_billiard.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        {{-- Form untuk tambah data --}}
                        <div class="mb-3">
                            <label for="nomor_meja" class="form-label">Nomor Meja</label>
                            <input type="text" class="form-control @error('nomor_meja', 'store') is-invalid @enderror"
                                name="nomor_meja" value="{{ old('nomor_meja') }}" placeholder="Contoh: 01 atau VIP-1">
                            @error('nomor_meja', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tipe_meja" class="form-label">Tipe Meja</label>
                            <select class="form-select @error('tipe_meja', 'store') is-invalid @enderror" name="tipe_meja">
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="9-ft Reguler" {{ old('tipe_meja') == '9-ft Reguler' ? 'selected' : '' }}>9-ft
                                    Reguler</option>
                                <option value="9-ft VIP" {{ old('tipe_meja') == '9-ft VIP' ? 'selected' : '' }}>9-ft VIP
                                </option>
                                <option value="8-ft Reguler" {{ old('tipe_meja') == '8-ft Reguler' ? 'selected' : '' }}>
                                    8-ft Reguler</option>
                                <option value="7-ft Reguler" {{ old('tipe_meja') == '7-ft Reguler' ? 'selected' : '' }}>
                                    7-ft Reguler</option>
                                <option value="Snooker" {{ old('tipe_meja') == 'Snooker' ? 'selected' : '' }}>Snooker
                                </option>
                            </select>
                            @error('tipe_meja', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                            <input type="number" class="form-control @error('harga_per_jam', 'store') is-invalid @enderror"
                                name="harga_per_jam" value="{{ old('harga_per_jam') }}" placeholder="Contoh: 50000">
                            @error('harga_per_jam', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status', 'store') is-invalid @enderror" name="status">
                                <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia
                                </option>
                                <option value="digunakan" {{ old('status') == 'digunakan' ? 'selected' : '' }}>Digunakan
                                </option>
                                <option value="perbaikan" {{ old('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan
                                </option>
                            </select>
                            @error('status', 'store')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data (dibuat di dalam loop) -->
    @foreach ($data as $row)
        <div class="modal fade" id="editDataModal-{{ $row->meja_id }}" tabindex="-1"
            aria-labelledby="editDataModalLabel-{{ $row->meja_id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDataModalLabel-{{ $row->meja_id }}">Edit Meja Billiard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('meja_billiard.update', $row->meja_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nomor_meja" class="form-label">Nomor Meja</label>
                                <input type="text"
                                    class="form-control @error('nomor_meja', 'update') is-invalid @enderror"
                                    name="nomor_meja" value="{{ old('nomor_meja', $row->nomor_meja) }}">
                                @error('nomor_meja', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tipe_meja" class="form-label">Tipe Meja</label>
                                <select class="form-select @error('tipe_meja', 'update') is-invalid @enderror"
                                    name="tipe_meja">
                                    <option value="9-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '9-ft Reguler' ? 'selected' : '' }}>9-ft
                                        Reguler</option>
                                    <option value="9-ft VIP"
                                        {{ old('tipe_meja', $row->tipe_meja) == '9-ft VIP' ? 'selected' : '' }}>9-ft VIP
                                    </option>
                                    <option value="8-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '8-ft Reguler' ? 'selected' : '' }}>8-ft
                                        Reguler</option>
                                    <option value="7-ft Reguler"
                                        {{ old('tipe_meja', $row->tipe_meja) == '7-ft Reguler' ? 'selected' : '' }}>7-ft
                                        Reguler</option>
                                    <option value="Snooker"
                                        {{ old('tipe_meja', $row->tipe_meja) == 'Snooker' ? 'selected' : '' }}>Snooker
                                    </option>
                                </select>
                                @error('tipe_meja', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                                <input type="number"
                                    class="form-control @error('harga_per_jam', 'update') is-invalid @enderror"
                                    name="harga_per_jam" value="{{ old('harga_per_jam', (int) $row->harga_per_jam) }}">
                                @error('harga_per_jam', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status', 'update') is-invalid @enderror"
                                    name="status">
                                    <option value="tersedia"
                                        {{ old('status', $row->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="digunakan"
                                        {{ old('status', $row->status) == 'digunakan' ? 'selected' : '' }}>Digunakan
                                    </option>
                                    <option value="perbaikan"
                                        {{ old('status', $row->status) == 'perbaikan' ? 'selected' : '' }}>Perbaikan
                                    </option>
                                </select>
                                @error('status', 'update')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('js')
    {{-- JS untuk DataTables --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#myTable');

        // SCRIPT UNTUK SWEETALERT TOAST
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

        // SCRIPT UNTUK SWEETALERT KONFIRMASI HAPUS
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Mencegah form submit secara langsung
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit form jika dikonfirmasi
                        }
                    });
                });
            });
        });

        // SCRIPT UNTUK MENAMPILKAN MODAL JIKA ADA ERROR VALIDASI
        @if ($errors->any())
            var errorModalId = '';
            @if ($errors->hasBag('store'))
                errorModalId = 'tambahDataModal';
            @elseif ($errors->hasBag('update'))
                var failedId = '{{ session('failed_id') }}';
                errorModalId = 'editDataModal-' + failedId;
            @endif

            if (errorModalId) {
                var myModal = new bootstrap.Modal(document.getElementById(errorModalId), {
                    keyboard: false
                });
                myModal.show();
            }
        @endif
    </script>
@endpush

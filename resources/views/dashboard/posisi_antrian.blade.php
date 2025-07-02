@extends('layout.be.template')

@section('title', 'Posisi Antrian Saya')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body text-center p-4">

                        @if ($myQueue)
                            @if ($myQueue->status_antrian == 'dipanggil')
                                {{-- Tampilan jika status 'dipanggil' --}}
                                <i class="ti ti-bell-ringing text-primary" style="font-size: 4rem;"></i>
                                <h4 class="card-title mt-3 fw-semibold">Sekarang Giliran Anda!</h4>
                                <p class="text-muted">
                                    Anda telah dipanggil dari daftar tunggu. Silakan pilih meja yang tersedia untuk memulai
                                    permainan.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('booking_meja.index') }}" class="btn btn-primary">Pilih Meja
                                        Sekarang</a>
                                </div>
                            @else
                                {{-- Tampilan jika status 'menunggu' --}}
                                <i class="ti ti-hourglass-high text-warning" style="font-size: 4rem;"></i>
                                <h4 class="card-title mt-3 fw-semibold">Anda di Daftar Tunggu</h4>
                                <p class="text-muted">Posisi antrian Anda saat ini adalah nomor:</p>
                                <h1 class="display-3 fw-bolder my-3">{{ $position }}</h1>
                                <p class="mb-4">
                                    Harap tunggu giliran Anda. <br>
                                    Anda masuk antrian pada:
                                    {{ \Carbon\Carbon::parse($myQueue->waktu_masuk)->format('d M Y, H:i') }}
                                </p>
                                <form action="{{ route('waitinglist.cancel') }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">Batalkan Antrian</button>
                                </form>
                            @endif
                        @else
                            {{-- Tampilan jika pengguna tidak ada di dalam antrian --}}
                            <img src="/backend/assets/images/backgrounds/empty-state.png" width="200"
                                class="img-fluid mb-3" alt="Tidak ada antrian">
                            <h4 class="card-title mt-3 fw-semibold">Anda Tidak Sedang Mengantri</h4>
                            <p class="mb-4 text-muted">
                                Saat ini Anda tidak berada di dalam daftar tunggu manapun. <br>
                                Silakan lakukan booking jika meja tersedia.
                            </p>
                            <a href="{{ route('booking_meja.index') }}" class="btn btn-primary">Lihat Meja & Booking</a>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
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
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif

        // SCRIPT UNTUK SWEETALERT KONFIRMASI BATAL
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Mencegah form submit secara langsung
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda akan keluar dari daftar tunggu.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, batalkan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit form jika dikonfirmasi
                        }
                    });
                });
            });
        });
    </script>
@endpush

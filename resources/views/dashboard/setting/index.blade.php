@extends('layout.be.template')

@section('title', 'Pengaturan Operasional')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pengaturan Jam Operasional</h5>
                </div>
                <div class="card-body">
                    <p class="card-subtitle mb-4">
                        Atur jam buka dan tutup untuk operasional billiard. Pengaturan ini akan memengaruhi ketersediaan
                        slot waktu booking untuk pelanggan.
                    </p>

                    {{-- Form untuk update pengaturan --}}
                    <form id="form-setting" action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        {{-- Input untuk Jam Buka --}}
                        <div class="mb-3">
                            <label for="jam_buka" class="form-label">Jam Buka</label>
                            <input type="time" class="form-control @error('jam_buka') is-invalid @enderror"
                                id="jam_buka" name="jam_buka"
                                value="{{ old('jam_buka', $settings->get('jam_buka')?->value) }}">

                            @error('jam_buka')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Input untuk Jam Tutup --}}
                        <div class="mb-4">
                            <label for="jam_tutup" class="form-label">Jam Tutup</label>
                            <input type="time" class="form-control @error('jam_tutup') is-invalid @enderror"
                                id="jam_tutup" name="jam_tutup"
                                value="{{ old('jam_tutup', $settings->get('jam_tutup')?->value) }}">

                            @error('jam_tutup')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Tombol Simpan --}}
                        <button type="submit" id="btn-submit-setting"
                            class="btn btn-primary d-flex align-items-center gap-2" style="border-radius: 50px">
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-setting" role="status"
                                aria-hidden="true"></span>
                            <span id="text-setting">Simpan Perubahan</span>
                        </button>

                    </form>
                </div>
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
                timerProgressBar: true
            });
        @endif
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-submit-setting');
            const spinner = document.getElementById('spinner-setting');
            const text = document.getElementById('text-setting');

            if (btn && spinner && text) {
                btn.addEventListener('click', function() {
                    // Tampilkan spinner dan ubah teks sebelum form submit berjalan
                    spinner.classList.remove('d-none');
                    text.textContent = 'Menyimpan...';
                });
            }
        });
    </script>
@endpush

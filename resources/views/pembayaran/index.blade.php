@extends('layout.be.template')

@section('title', 'Proses Pembayaran')

@push('css')
    <style>
        .payment-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="payment-container">
                    <h3 class="fw-semibold mb-3">Selesaikan Pembayaran Anda</h3>

                    <p class="text-muted">
                        Anda akan membayar DP untuk <strong>Meja {{ $booking->meja->nomor_meja }}</strong><br>
                        Jadwal: <strong class="text-dark">
                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->isoFormat('dddd, D MMMM Y [pukul] HH:mm') }}
                        </strong>
                    </p>

                    <p class="fw-semibold mt-4 mb-1 text-danger">Harap selesaikan pembayaran dalam waktu:</p>
                    <div id="payment-countdown"
                        class="d-inline-block bg-danger text-white fw-bold py-2 px-4 rounded-pill mb-3"
                        style="font-size: 1.25rem;">
                        Memuat...
                    </div>

                    {{-- Informasi penting --}}
                    <p class="text-muted mb-4">
                        Jika Anda tidak menyelesaikan pembayaran dalam waktu yang ditentukan,<br>
                        sistem akan otomatis membatalkan booking Anda.
                    </p>

                    <img src="{{ asset('backend/assets/images/logos/midtrans.png') }}" height="24" alt="Midtrans Logo"
                        class="mb-3" style="max-width: 200px;">

                    <button id="pay-button" class="btn btn-primary fw-bold px-4 py-2 rounded-pill">
                        Bayar Sekarang
                    </button>

                    {{-- Data tersembunyi --}}
                    <div id="booking-data" data-created-at="{{ $booking->created_at->toIso8601String() }}"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

    <script>
        const payButton = document.getElementById('pay-button');
        const originalButtonText = payButton.innerHTML;
        const snapToken = "{{ $snapToken }}";

        function resetPayButton() {
            payButton.disabled = false;
            payButton.innerHTML = originalButtonText;
        }

        function proceedToPayment() {
            payButton.disabled = true;
            payButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Memuat...
            `;

            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    resetPayButton();
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil',
                        text: 'Terima kasih! Pembayaran Anda telah kami terima.',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.href = "{{ route('booking.history') }}";
                    });
                },
                onPending: function(result) {
                    resetPayButton();
                    Swal.fire({
                        icon: 'info',
                        title: 'Pembayaran Pending',
                        text: 'Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran.',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                },
                onError: function(result) {
                    resetPayButton();
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: 'Terjadi kesalahan saat memproses pembayaran Anda.',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                },
                onClose: function() {
                    resetPayButton();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pembayaran Dibatalkan',
                        text: 'Anda menutup halaman pembayaran sebelum menyelesaikannya.',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                }
            });
        }

        payButton.onclick = proceedToPayment;

        const countdownElement = document.getElementById('payment-countdown');
        const bookingDataElement = document.getElementById('booking-data');

        if (countdownElement && bookingDataElement) {
            const createdAt = new Date(bookingDataElement.dataset.createdAt);
            const expiryTime = createdAt.getTime() + (10 * 60 * 1000); // 10 menit

            const timerInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                if (distance > 0) {
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    countdownElement.innerHTML =
                        `${String(minutes).padStart(2, '0')} : ${String(seconds).padStart(2, '0')}`;
                } else {
                    clearInterval(timerInterval);
                    countdownElement.innerHTML = "Waktu Habis";
                    countdownElement.classList.remove('bg-danger');
                    countdownElement.classList.add('bg-secondary');
                    payButton.classList.add('disabled');
                    payButton.innerText = 'Waktu Pembayaran Habis';
                }
            }, 1000);
        }
    </script>
@endpush

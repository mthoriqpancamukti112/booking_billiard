@extends('layout.be.template')

@section('title', 'Booking Meja')

@push('css')
    <style>
        .card-meja {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease-in-out;
            height: 100%;
        }

        .card-meja .card-header {
            border-bottom: 1px solid #dee2e6;
        }

        .card-meja.status-tersedia {
            border-left: 5px solid #13deb9;
        }

        .card-meja.status-digunakan {
            border-left: 5px solid #ff5b5b;
        }

        .card-meja.status-perbaikan {
            border-left: 5px solid #ffae1f;
            background-color: #f8f9fa;
        }

        .card-meja.status-penuh {
            border-left: 5px solid #adb5bd;
        }

        .timer-display {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2a3547;
        }

        .time-slot {
            margin: 5px;
            width: calc(20% - 10px);
            padding: 0.375rem 0.75rem;
        }


        #timeSlotsContainer {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .list-group-item-action.active {
            z-index: 2;
            color: #fff;
            background-color: #5d87ff;
            border-color: #5d87ff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <h3 class="fw-semibold">Pilih Meja Billiard</h3>
                <p>Pilih meja yang tersedia untuk memulai permainan hari ini, atau pesan untuk besok jika semua meja penuh.
                </p>
            </div>
        </div>
        @if ($active_booking)

            @if ($active_booking->status_pembayaran == 'belum_bayar')
                <div class="card bg-light-warning shadow-none mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-warning"><i class="ti ti-hourglass-high me-1"></i>Satu Langkah Lagi!
                            Konfirmasi Booking Anda</h5>

                        <p class="mt-3">
                            Booking Anda untuk <strong>Meja {{ $active_booking->meja->nomor_meja }}</strong> pada jadwal:
                            <br>
                            <strong
                                class="text-dark">{{ \Carbon\Carbon::parse($active_booking->waktu_mulai)->isoFormat('dddd, D MMM YYYY, HH:mm') }}</strong>
                            <br>
                            menunggu konfirmasi pembayaran.
                        </p>

                        <hr>

                        <p class="fw-semibold mb-2 text-center">Jadwal Anda akan otomatis dibatalkan jika pembayaran tidak
                            diselesaikan
                            dalam:</p>

                        <center>
                            <div id="payment-countdown" class="d-inline-block bg-danger text-white fw-bold py-2 px-3 mb-3"
                                style="border-radius: 50px; font-size: 1.2rem; min-width: 100px;">
                                Memuat...
                            </div>
                        </center>

                        {{-- Hidden data untuk JavaScript --}}
                        <div id="booking-data" data-created-at="{{ $active_booking->created_at->toIso8601String() }}"></div>

                        <a href="{{ route('booking.pay', Crypt::encrypt($active_booking->booking_id)) }}"
                            class="btn btn-warning w-100 fw-bold" style="border-radius: 50px">
                            Selesaikan Pembayaran Sekarang
                        </a>
                    </div>
                </div>

                {{-- Prioritas #2: Jika bukan 'belum_bayar' (artinya sudah 'paid'), tampilkan info booking. --}}
            @else
                <div class="card bg-light-info shadow-none mb-4">
                    <div class="card-body">
                        {{-- Judul dinamis berdasarkan status booking --}}
                        @if ($active_booking->status_booking == 'berlangsung')
                            <h5 class="card-title text-success"><i class="ti ti-player-play me-1"></i>Sesi Anda Sedang
                                Berlangsung</h5>
                            <p>Selamat bermain di <strong>Meja {{ $active_booking->meja->nomor_meja }}</strong>. Berikut
                                adalah detail sesi Anda:</p>
                        @else
                            <h5 class="card-title text-info"><i class="ti ti-circle-check me-1"></i>Booking Dikonfirmasi!
                            </h5>
                            <p>Terima kasih, pembayaran DP Anda berhasil. Berikut adalah detail jadwal Anda untuk
                                <strong>Meja {{ $active_booking->meja->nomor_meja }}</strong>:
                            </p>
                        @endif

                        <ul class="list-unstyled">
                            <li><strong>Jadwal:</strong>
                                {{ \Carbon\Carbon::parse($active_booking->waktu_mulai)->isoFormat('dddd, D MMM YYYY') }},
                                Pukul {{ \Carbon\Carbon::parse($active_booking->waktu_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($active_booking->waktu_selesai)->format('H:i') }}</li>
                            <li><strong>Durasi:</strong> {{ $active_booking->durasi_menit / 60 }} jam</li>
                            @if ($active_booking->pembayaran)
                                <li><strong>DP Dibayar:</strong> Rp
                                    {{ number_format($active_booking->pembayaran->jumlah, 0, ',', '.') }}</li>
                            @endif
                        </ul>

                        <p class="fs-3 fw-semibold text-dark mt-3">
                            Sisa pembayaran dapat dilunasi di kasir sebelum bermain.
                        </p>
                        <hr>
                        <p class="mb-0 fw-semibold text-danger"><i class="ti ti-alert-circle me-1"></i> Mohon Diperhatikan
                        </p>
                        <p class="fs-3 text-dark mb-0">Sistem kami bekerja secara otomatis. Waktu bermain Anda akan
                            <strong>dimulai dan diakhiri tepat sesuai jadwal</strong>, terlepas dari waktu kedatangan Anda.
                        </p>
                    </div>
                </div>
            @endif
        @endif
        @if ($show_book_tomorrow_button)
            <div class="card bg-light-primary shadow-none mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Jadwal Hari Ini Penuh!</h5>
                    <p>Mohon maaf, seluruh slot waktu bermain untuk hari ini telah habis dipesan. Silakan pesan untuk hari
                        esok.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#bookingModalTomorrow" style="border-radius: 50px">Booking untuk Besok</button>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach ($meja as $m)
                <div class="col-sm-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card card-meja status-{{ $m->is_full_today ? 'penuh' : $m->status }} shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold fs-4 mb-0">{{ $m->nomor_meja }}</h6>
                            @if ($m->is_full_today && $m->status == 'tersedia')
                                <span class="badge bg-danger" style="border-radius: 50px">Penuh</span>
                            @elseif ($m->status == 'tersedia')
                                <span class="badge bg-success" style="border-radius: 50px">Tersedia</span>
                            @elseif ($m->status == 'digunakan')
                                <span class="badge bg-primary" style="border-radius: 50px">Digunakan</span>
                            @elseif ($m->status == 'perbaikan')
                                <span class="badge bg-warning text-dark" style="border-radius: 50px">Perbaikan</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="text-muted mb-3">{{ $m->tipe_meja }}</p>
                            @if ($m->status == 'tersedia' && !$m->is_full_today)
                                <div class="text-center my-auto">
                                    <h5 class="fw-semibold fs-6 mb-0">Rp
                                        {{ number_format($m->harga_per_jam, 0, ',', '.') }}</h5>
                                    <p class="text-muted small">/ jam</p>
                                </div>
                            @else
                                <div class="text-center my-auto">
                                    @if ($m->status == 'digunakan' && $m->activeBooking)
                                        <i class="fas fa-hourglass-half text-danger mb-2"></i>
                                        <p class="mb-1 fw-semibold">Digunakan</p>
                                        <div class="dynamic-meja-timer"
                                            data-end-time="{{ $m->activeBooking->waktu_selesai }}">
                                            <span class="fs-3 text-muted">Sisa waktu:</span>
                                            <span class="fs-3 fw-semibold time-display-meja text-danger">Memuat...</span>
                                        </div>
                                    @elseif ($m->is_full_today)
                                        <i class="fas fa-calendar-times text-primary mb-2"></i>
                                        <p class="text-muted mt-2">Semua slot hari ini telah dipesan.</p>
                                    @else
                                        <i class="fas fa-tools text-warning"></i>
                                        <p class="text-muted mt-2">Meja dalam perbaikan</p>
                                    @endif
                                </div>
                            @endif
                            <div class="mt-auto">
                                @php
                                    // Izinkan booking jika status meja 'tersedia' ATAU 'digunakan'.
                                    $isBookableStatus = $m->status == 'tersedia' || $m->status == 'digunakan';

                                    // status harus bisa dibooking, tidak penuh hari ini,
                                    // dan pengguna tidak sedang memiliki booking aktif.
                                    $canBook = $isBookableStatus && !$m->is_full_today && !$active_booking;
                                @endphp
                                @if ($canBook)
                                    <button type="button" class="btn btn-primary w-100 mt-3 book-button"
                                        data-bs-toggle="modal" data-bs-target="#bookingModal"
                                        data-meja-id="{{ $m->meja_id }}" data-nomor-meja="{{ $m->nomor_meja }}"
                                        data-harga-per-jam="{{ $m->harga_per_jam }}"
                                        data-booked-slots="{{ json_encode($m->booked_slots_today) }}"
                                        style="border-radius: 50px;">
                                        Booking
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary w-100 mt-3" disabled>
                                        @if ($m->status == 'perbaikan')
                                            Dalam Perbaikan
                                        @elseif ($m->is_full_today)
                                            Penuh Hari Ini
                                        @elseif($active_booking)
                                            Anda Punya Sesi Aktif
                                        @else
                                            Tidak Tersedia
                                        @endif
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL BOOKING HARI INI --}}
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Booking Meja <span id="modalNomorMeja"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('booking.store') }}" method="POST" class="booking-form">
                    @csrf
                    <input type="hidden" name="meja_id" id="modalMejaId">
                    <input type="hidden" name="waktu_mulai" id="modalWaktuMulai">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="durasiSelect" class="form-label">Pilih Durasi Bermain:</label>
                            <select class="form-select" name="durasi" id="durasiSelect" style="border-radius: 50px;">
                                <option value="1">1 Jam</option>
                                <option value="2">2 Jam</option>
                                <option value="3">3 Jam</option>
                                <option value="4">4 Jam</option>
                            </select>
                        </div>
                        <hr>
                        <p class="mb-2">Pilih Waktu Mulai:</p>
                        <div id="timeSlotsContainer" class="d-flex flex-wrap justify-content-center"></div>

                        <div class="card bg-light-primary shadow-none mt-3" id="bookingInfo" style="display: none;">
                            <div class="card-body">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="border-radius: 50px;">Batal</button>
                        <button type="submit" class="btn btn-primary" id="confirmBookingBtn"
                            style="border-radius: 50px;" disabled>Konfirmasi & Bayar
                            DP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL BOOKING BESOK --}}
    <div class="modal fade" id="bookingModalTomorrow" tabindex="-1" aria-labelledby="bookingModalTomorrowLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalTomorrowLabel">Pilih Meja & Waktu untuk Besok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan pilih meja yang tersedia, kemudian pilih durasi dan slot waktu untuk pemesanan hari esok.
                    </p>
                    <div class="row">
                        <div class="col-md-4 border-end">
                            <h6 class="mb-3">1. Pilih Meja</h6>
                            <div class="list-group" id="mejaListTomorrow">
                                @foreach ($meja as $m)
                                    @if ($m->status == 'tersedia')
                                        <button type="button" class="list-group-item list-group-item-action"
                                            data-meja-id="{{ $m->meja_id }}" data-nomor-meja="{{ $m->nomor_meja }}"
                                            data-harga-per-jam="{{ $m->harga_per_jam }}"
                                            data-booked-slots-tomorrow="{{ json_encode($m->booked_slots_tomorrow) }}">
                                            {{ $m->nomor_meja }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="mb-3">2. Pilih Durasi & Waktu (<span id="selectedMejaTomorrow"
                                    class="text-primary">...</span>)</h6>
                            <form action="{{ route('booking.store') }}" method="POST" id="formBookingTomorrow"
                                class="booking-form">
                                @csrf
                                <input type="hidden" name="meja_id" id="modalMejaIdTomorrow">
                                <input type="hidden" name="waktu_mulai" id="modalWaktuMulaiTomorrow">
                                <div class="mb-3">
                                    <label for="durasiSelectTomorrow" class="form-label">Pilih Durasi:</label>
                                    <select class="form-select" name="durasi" id="durasiSelectTomorrow">
                                        <option value="1">1 Jam</option>
                                        <option value="2">2 Jam</option>
                                        <option value="3">3 Jam</option>
                                        <option value="4">4 Jam</option>
                                    </select>
                                </div>
                                <div id="timeSlotsContainerTomorrow" class="d-flex flex-wrap justify-content-center">
                                    <p class="text-muted">Pilih meja terlebih dahulu.</p>
                                </div>
                                <div class="card bg-light-primary shadow-none mt-3" id="bookingInfoTomorrow"
                                    style="display: none;">
                                    <div class="card-body">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formBookingTomorrow" class="btn btn-primary"
                        id="confirmBookingBtnTomorrow" disabled>Konfirmasi & Bayar DP</button> {{-- Teks tombol diubah --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif

        document.addEventListener('DOMContentLoaded', function() {

            const jamBuka = "{{ $jam_buka }}";
            const jamTutup = "{{ $jam_tutup }}";

            // --- Fungsi timer ---
            function formatTime(ms) {
                if (ms < 0) ms = 0;
                let hours = String(Math.floor(ms / 3600000)).padStart(2, '0');
                let minutes = String(Math.floor((ms % 3600000) / 60000)).padStart(2, '0');
                let seconds = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            }
            const mainTimerDisplay = document.getElementById('timer-display');
            if (mainTimerDisplay) {
                const endTime = new Date(mainTimerDisplay.dataset.endTime).getTime();
                const timerInterval = setInterval(() => {
                    const remaining = endTime - new Date().getTime();
                    if (remaining > 0) {
                        mainTimerDisplay.textContent = formatTime(remaining);
                    } else {
                        mainTimerDisplay.textContent = "Waktu Habis";
                        clearInterval(timerInterval);
                        setTimeout(() => window.location.reload(), 3000);
                    }
                }, 1000);
            }
            const previousPlayerTimer = document.querySelector('.dynamic-previous-player-timer');
            if (previousPlayerTimer) {
                const display = previousPlayerTimer.querySelector('.time-display-previous');
                const endTime = new Date(previousPlayerTimer.dataset.endTime).getTime();
                const timerInterval = setInterval(() => {
                    const remaining = endTime - new Date().getTime();
                    if (remaining > 0) {
                        let minutes = String(Math.floor((remaining % 3600000) / 60000)).padStart(2, '0');
                        let seconds = String(Math.floor((remaining % 60000) / 1000)).padStart(2, '0');
                        display.textContent = `${minutes}:${seconds}`;
                    } else {
                        previousPlayerTimer.style.display = 'none';
                        clearInterval(timerInterval);
                    }
                }, 1000);
            }
            document.querySelectorAll('.dynamic-meja-timer').forEach(timer => {
                const display = timer.querySelector('.time-display-meja');
                const endTime = new Date(timer.dataset.endTime).getTime();
                const timerInterval = setInterval(() => {
                    const remaining = endTime - new Date().getTime();
                    if (remaining > 0) {
                        let minutes = String(Math.floor((remaining % 3600000) / 60000)).padStart(2,
                            '0');
                        let seconds = String(Math.floor((remaining % 60000) / 1000)).padStart(2,
                            '0');
                        display.textContent = `${minutes}:${seconds}`;
                    } else {
                        display.textContent = "Selesai";
                        clearInterval(timerInterval);
                    }
                }, 1000);
            });

            // --- LOGIKA MODAL BOOKING (Berlaku untuk Hari Ini & Besok) ---
            const setupBookingModal = (modalId) => {
                const modalElement = document.getElementById(modalId);
                if (!modalElement) return;

                const isTomorrowModal = modalId === 'bookingModalTomorrow';
                const durasiSelect = document.getElementById(isTomorrowModal ? 'durasiSelectTomorrow' :
                    'durasiSelect');
                const timeSlotsContainer = document.getElementById(isTomorrowModal ?
                    'timeSlotsContainerTomorrow' : 'timeSlotsContainer');
                const bookingInfoContainer = document.getElementById(isTomorrowModal ? 'bookingInfoTomorrow' :
                    'bookingInfo');

                let currentBookedSlots, currentHarga;

                const handleDurasiChange = () => {
                    generateTimeSlots(currentBookedSlots, timeSlotsContainer, durasiSelect,
                        bookingInfoContainer.querySelector('.card-body'), currentHarga, isTomorrowModal);
                }

                if (isTomorrowModal) {
                    const mejaList = document.getElementById('mejaListTomorrow');
                    mejaList.querySelectorAll('button').forEach(button => {
                        button.addEventListener('click', function() {
                            mejaList.querySelectorAll('button').forEach(btn => btn.classList
                                .remove('active'));
                            this.classList.add('active');

                            currentBookedSlots = JSON.parse(this.dataset.bookedSlotsTomorrow);
                            currentHarga = parseFloat(this.dataset.hargaPerJam);

                            document.getElementById('selectedMejaTomorrow').textContent =
                                `Meja ${this.dataset.nomorMeja}`;
                            document.getElementById('modalMejaIdTomorrow').value = this.dataset
                                .mejaId;

                            durasiSelect.value = '1';
                            handleDurasiChange();
                        });
                    });
                } else {
                    modalElement.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        currentBookedSlots = JSON.parse(button.getAttribute('data-booked-slots'));
                        currentHarga = parseFloat(button.getAttribute('data-harga-per-jam'));

                        document.getElementById('modalNomorMeja').textContent = button.getAttribute(
                            'data-nomor-meja');
                        document.getElementById('modalMejaId').value = button.getAttribute(
                            'data-meja-id');

                        durasiSelect.value = '1';
                        handleDurasiChange();
                    });
                }

                durasiSelect.addEventListener('change', handleDurasiChange);
            };

            setupBookingModal('bookingModal');
            setupBookingModal('bookingModalTomorrow');

            // --- FUNGSI GENERATE SLOT WAKTU (DIUBAH TOTAL) ---
            function generateTimeSlots(bookedSlots, container, durasiElement, bookingInfoElement, hargaPerJam,
                isTomorrow = false) {

                container.innerHTML = '';
                const durasi = parseInt(durasiElement.value);
                const modalContent = container.closest('.modal-content');
                const confirmBtn = modalContent.querySelector('button[type="submit"]');
                const waktuMulaiInput = modalContent.querySelector('input[name="waktu_mulai"]');

                confirmBtn.disabled = true;
                waktuMulaiInput.value = '';
                bookingInfoElement.parentElement.style.display = 'none';

                const date = new Date();
                if (isTomorrow) {
                    date.setDate(date.getDate() + 1);
                }

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;

                const [startHour] = jamBuka.split(':').map(Number);
                const [endHour] = jamTutup.split(':').map(Number);
                let availableSlotsCount = 0;

                // --- Optimasi loop agar mulai dari jam sekarang ---
                let loopStartHour = startHour;
                const currentHour = new Date().getHours();

                if (!isTomorrow && currentHour >= startHour) {
                    // Mulai loop dari jam saat ini jika sudah melewati jam buka
                    loopStartHour = currentHour;
                }

                for (let hour = startHour; hour < endHour; hour++) {
                    const slotTime = `${String(hour).padStart(2, '0')}:00`;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('btn', 'time-slot');
                    btn.textContent = slotTime;

                    let isSlotAvailable = true;
                    if (hour + durasi > endHour) {
                        isSlotAvailable = false;
                    } else {
                        for (let i = 0; i < durasi; i++) {
                            const checkHour = hour + i;
                            const checkTime = `${String(checkHour).padStart(2, '0')}:00`;
                            const checkDateTime = new Date(`${dateStr}T${checkTime}:00`);
                            if (bookedSlots.includes(checkTime) || (!isTomorrow && checkDateTime < new Date())) {
                                isSlotAvailable = false;
                                break;
                            }
                        }
                    }

                    if (isSlotAvailable) {
                        availableSlotsCount++;
                        btn.classList.add('btn-outline-primary');
                        btn.onclick = function() {
                            container.querySelectorAll('button').forEach(b => b.classList.remove('btn-primary',
                                'active'));
                            this.classList.add('btn-primary', 'active');

                            const waktuMulai = `${dateStr} ${slotTime}:00`;
                            waktuMulaiInput.value = waktuMulai;
                            confirmBtn.disabled = false;

                            // PERUBAHAN UTAMA: Tampilkan info booking, DP, dan metode pembayaran
                            const totalBiaya = hargaPerJam * durasi;
                            const dpAmount = totalBiaya / 2; // Hitung DP 50%
                            const waktuSelesai = new Date(new Date(waktuMulai).getTime() + durasi * 3600 *
                                1000);
                            const waktuSelesaiFormatted =
                                `${String(waktuSelesai.getHours()).padStart(2, '0')}:${String(waktuSelesai.getMinutes()).padStart(2, '0')}`;

                            bookingInfoElement.innerHTML = `
                                <h6 class="card-title text-primary">Ringkasan Booking</h6>
                                <p class="mb-2 fs-3">
                                    Anda akan booking dari <strong>${slotTime}</strong> - <strong>${waktuSelesaiFormatted}</strong> (${durasi} jam).
                                </p>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fs-3">
                                    <span>Total Biaya:</span>
                                    <strong>Rp ${totalBiaya.toLocaleString('id-ID')}</strong>
                                </div>
                                <div class="d-flex justify-content-between fw-semibold fs-3 text-success">
                                    <span>Uang Muka (DP 50%):</span>
                                    <strong>Rp ${dpAmount.toLocaleString('id-ID')}</strong>
                                </div>
                                <hr class="my-2">
                                <p class="mb-2 fw-semibold fs-3">Pembayaran Awal via:</p>
                                <div>
                                    <img src="{{ asset('backend/assets/images/logos/qris.svg') }}" height="20" alt="QRIS" style="margin-right: 15px; vertical-align: middle;">
                                    <img src="{{ asset('backend/assets/images/logos/gopay.svg') }}" height="15" alt="GoPay" style="margin-right: 15px; vertical-align: middle;">
                                    <img src="{{ asset('backend/assets/images/logos/dana.svg') }}" height="15" alt="Dana" style="vertical-align: middle;">
                                    <img src="{{ asset('backend/assets/images/logos/shopeepay.svg') }}" height="40" alt="ShopeePay" style="vertical-align: middle;">
                                    <img src="{{ asset('backend/assets/images/logos/bank.png') }}" height="40" alt="Bank Transfer" style="vertical-align: middle;">
                                </div>
                                <p class="fs-2 text-muted mt-2 mb-0">Anda akan diarahkan ke halaman pembayaran setelah konfirmasi.</p>
                            `;
                            bookingInfoElement.parentElement.style.display = 'block';
                        };
                    } else {
                        btn.classList.add('btn-secondary');
                        btn.disabled = true;
                    }
                    container.appendChild(btn);
                }

                if (availableSlotsCount === 0) {
                    container.innerHTML = `<div class="text-center my-3 px-3">
                        <i class="ti ti-calendar-off" style="font-size: 3rem; color: #a9a9a9;"></i>
                        <h6 class="fw-semibold mt-3">Tidak Ada Slot Tersedia</h6>
                        <p class="fs-3 text-muted mb-0">Tidak ada slot yang tersedia untuk durasi yang Anda pilih.</p>
                    </div>`;
                }
            }
        });

        // Menambahkan spinner pada tombol submit saat form dikirim
        document.querySelectorAll('.booking-form').forEach(form => {
            form.addEventListener('submit', function() {
                // 'this' merujuk pada form yang sedang disubmit
                const currentForm = this;
                let submitBtn = null;

                // --- LOGIKA BARU YANG LEBIH CERDAS ---
                // 1. Coba cari tombol yang terhubung via atribut 'form="id_form"'
                //    Ini untuk menangani kasus modal "Booking Besok".
                if (currentForm.id) {
                    submitBtn = document.querySelector(`button[type="submit"][form="${currentForm.id}"]`);
                }

                // 2. Jika tidak ketemu (kasus modal "Booking Hari Ini"), cari di dalam form.
                if (!submitBtn) {
                    submitBtn = currentForm.querySelector('button[type="submit"]');
                }

                if (submitBtn) {
                    // Menonaktifkan tombol untuk mencegah klik ganda
                    submitBtn.disabled = true;

                    // Mengganti isi tombol dengan spinner dan teks
                    submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Memproses...
                `;
                }
            });
        });

        const countdownElement = document.getElementById('payment-countdown');
        const bookingDataElement = document.getElementById('booking-data');

        if (countdownElement && bookingDataElement) {
            const createdAt = new Date(bookingDataElement.dataset.createdAt);
            // Tambahkan 10 menit ke waktu pembuatan booking untuk mendapatkan waktu kadaluwarsa
            const expiryTime = createdAt.getTime() + (10 * 60 * 1000);

            const timerInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                if (distance > 0) {
                    // Kalkulasi sisa menit dan detik
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format agar selalu 2 digit (misal: 09:05)
                    const displayMinutes = String(minutes).padStart(2, '0');
                    const displaySeconds = String(seconds).padStart(2, '0');

                    // Tampilkan di elemen countdown
                    countdownElement.innerHTML = `${displayMinutes} : ${displaySeconds}`;
                } else {
                    // Jika waktu habis
                    clearInterval(timerInterval);
                    countdownElement.innerHTML = "Waktu Habis";
                    countdownElement.classList.remove('bg-danger');
                    countdownElement.classList.add('bg-secondary');
                    // Nonaktifkan tombol bayar jika waktu habis
                    document.querySelector('.btn-warning').classList.add('disabled');
                }
            }, 1000);
        }
    </script>
@endpush

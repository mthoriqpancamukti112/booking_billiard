<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MejaBilliard;
use App\Models\Pembayaran;
use App\Models\Setting;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek role pengguna yang sedang login
        if (Auth::user()->role == 'admin') {
            $total_meja = MejaBilliard::count();
            $meja_digunakan = MejaBilliard::where('status', 'digunakan')->count();
            $meja_perbaikan = MejaBilliard::where('status', 'perbaikan')->count();

            $settings = Setting::all()->keyBy('key');
            $jam_buka = $settings->get('jam_buka')?->value ?? '08:00';
            $jam_tutup = $settings->get('jam_tutup')?->value ?? '22:00';

            $meja_penuh = 0;
            $available_tables = MejaBilliard::where('status', 'tersedia')->with(['bookings' => function ($query) {
                $query->whereDate('waktu_mulai', Carbon::today());
            }])->get();

            foreach ($available_tables as $table) {
                $booked_slots = $table->bookings->pluck('waktu_mulai')->map(fn($time) => Carbon::parse($time)->format('H:i'))->toArray();
                $startHour = (int)explode(':', $jam_buka)[0];
                $endHour = (int)explode(':', $jam_tutup)[0];
                $currentHour = max((int)Carbon::now()->format('H'), $startHour);
                $availableSlotsCount = 0;
                for ($hour = $currentHour; $hour < $endHour; $hour++) {
                    if (!in_array(str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00', $booked_slots)) {
                        $availableSlotsCount++;
                    }
                }
                if ($availableSlotsCount === 0) {
                    $meja_penuh++;
                }
            }

            $meja_tersedia = $available_tables->count() - $meja_penuh;

            $recent_bookings = Booking::with('user.pelanggan', 'meja')
                ->whereIn('status_booking', ['berlangsung', 'dipesan'])
                ->whereDate('waktu_mulai', Carbon::today())
                ->orderBy('waktu_mulai', 'asc')
                ->take(5)
                ->get();

            $upcoming_bookings = Booking::with('user.pelanggan', 'meja')
                ->where('status_booking', 'dipesan')
                ->where('waktu_mulai', '>', now())
                ->orderBy('waktu_mulai', 'asc')
                ->limit(3)
                ->get();

            $total_bookings_today = Booking::whereDate('created_at', today())->count();
            $paid_today = Pembayaran::where('status', 'paid')->whereDate('created_at', today())->count();
            $failed_today = Pembayaran::where('status', 'failed')->whereDate('created_at', today())->count();
            $total_income_today = Pembayaran::where('status', 'paid')->whereDate('created_at', today())->sum('jumlah');

            // Statistik 7 Hari Terakhir
            $labels = [];
            $data_bookings = [];
            $data_income = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');

                $data_bookings[] = Booking::whereDate('created_at', $date)->count();
                $data_income[] = Pembayaran::where('status', 'paid')->whereDate('created_at', $date)->sum('jumlah');
            }

            $chart_labels = json_encode($labels);
            $chart_data_bookings = json_encode($data_bookings);
            $chart_data_income = json_encode($data_income);

            return view('dashboard.index', compact(
                'total_meja',
                'meja_tersedia',
                'meja_digunakan',
                'meja_penuh',
                'meja_perbaikan',
                'recent_bookings',
                'upcoming_bookings',
                'total_bookings_today',
                'paid_today',
                'failed_today',
                'total_income_today',
                'chart_labels',
                'chart_data_bookings',
                'chart_data_income'
            ));
        } elseif (Auth::user()->role == 'pelanggan') {

            // --- DATA UNTUK PELANGGAN ---
            $userId = Auth::id();

            // DIUBAH: active_booking sekarang mencari status 'dipesan' ATAU 'berlangsung'
            $active_booking = Booking::where('user_id', $userId)
                ->whereIn('status_booking', ['dipesan', 'berlangsung'])
                ->with('meja')
                ->first();

            // DIHAPUS: Logika antrian ($my_queue & $position) sudah tidak digunakan
            // $my_queue = ...
            // $position = ...

            // --- DATA STATISTIK TETAP SAMA ---
            $total_bookings = Booking::where('user_id', $userId)->count();
            $total_time_played = Booking::where('user_id', $userId)->where('status_booking', 'selesai')->sum('durasi_menit');
            $last_booking = Booking::where('user_id', $userId)->where('status_booking', 'selesai')->latest('waktu_selesai')->first();


            return view('dashboard.index', compact(
                'active_booking',
                'total_bookings',
                'total_time_played',
                'last_booking'
            ));
        }

        // Fallback jika ada role lain atau tidak terdefinisi
        return view('dashboard.index');
    }
}

<?php

namespace App\Policies;

use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PembayaranPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the payment page.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pembayaran  $pembayaran
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Pembayaran $pembayaran)
    {
        // Pastikan user yang login adalah pemilik booking terkait pembayaran ini.
        return $user->id === $pembayaran->booking->user_id;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\ApplicationAccepted;
use App\Notifications\ApplicationUnderReview;
use App\Notifications\BriefingReminder;
use App\Notifications\ProfileReminder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Cari user berdasarkan email
        $user = User::where('email', 'adhee@example.com')->first();

        if ($user) {
            // Opsional: Hapus notifikasi lama user ini agar tidak dobel saat di-seed berulang
            $user->notifications()->delete();

            // Kirim 4 notifikasi dummy
            $user->notify(new ApplicationAccepted());
            $user->notify(new BriefingReminder());
            $user->notify(new ApplicationUnderReview());
            $user->notify(new ProfileReminder());
            
            $this->command->info('✅ 4 Notifikasi dummy berhasil dikirim ke adhee@example.com');
        } else {
            $this->command->error('❌ User dengan email adhee@example.com tidak ditemukan!');
        }
    }
}
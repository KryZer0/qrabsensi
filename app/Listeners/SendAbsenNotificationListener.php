<?php

namespace App\Listeners;

use App\Models\Siswa;
use App\Models\User;
use App\Events\SiswaAbsenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAbsenNotificationListener
{
    public function handle(SiswaAbsenEvent $event)
    {
        $wali_users = User::where('id_wali', $event->siswa->wali)->whereNotNull('fcm_token')->get();

        foreach ($wali_users as $wali) {
            $siswa_list = Siswa::where('wali', $wali->id_wali)->pluck('nama')->toArray();
            $siswa_text = implode(', ', $siswa_list);

            $this->sendNotification($wali->fcm_token, "Absen Siswa", "Anak Anda ($siswa_text) telah melakukan absen: {$event->ket}.");
        }
    }

    private function sendNotification($fcmToken, $title, $body)
    {
        $serverKey = 'YOUR_FIREBASE_SERVER_KEY';
        $data = [
            "to" => $fcmToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ],
            "data" => ["click_action" => "FLUTTER_NOTIFICATION_CLICK"]
        ];

        Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $data);
    }
}

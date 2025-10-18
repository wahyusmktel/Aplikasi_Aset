<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\VehicleLog; // <-- Impor model

class VehicleLogNotification extends Notification
{
    use Queueable;

    protected $log;
    protected $actionType; // 'checkout' or 'checkin'

    /**
     * Create a new notification instance.
     */
    public function __construct(VehicleLog $log, string $actionType)
    {
        $this->log = $log;
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        $log = $this->log;
        $asset = $log->asset;
        $employee = $log->employee;
        $url = route('assets.show', $asset->id); // Link ke detail aset

        if ($this->actionType === 'checkout') {
            $actionText = " KELUAR";
            $date = $log->departure_time->isoFormat('D MMM YYYY, HH:mm');
            $condition = $log->condition_on_checkout;
            $odometer = $log->start_odometer;
            $odometerLabel = "KM Awal";
            $docNumber = $log->checkout_doc_number;
        } else {
            $actionText = " KEMBALI";
            $date = $log->return_time->isoFormat('D MMM YYYY, HH:mm');
            $condition = $log->condition_on_checkin;
            $odometer = $log->end_odometer;
            $odometerLabel = "KM Akhir";
            $docNumber = $log->checkin_doc_number;
        }

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'))
            ->content("{$actionText}\n\n*Kendaraan:* {$asset->name} (`{$asset->asset_code_ypt}`)\n*Pengguna:* {$employee->name}\n*Waktu:* {$date}\n*Tujuan:* {$log->destination}\n*{$odometerLabel}:* {$odometer} KM\n*Kondisi:* {$condition}\n*No. Dokumen:* `{$docNumber}`")
            ->button('Lihat Detail Kendaraan', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

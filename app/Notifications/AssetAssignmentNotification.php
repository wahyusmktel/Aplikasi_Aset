<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage; // <-- Gunakan TelegramMessage
use App\Models\AssetAssignment; // <-- Impor model

class AssetAssignmentNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $actionType; // 'checkout' or 'checkin'

    /**
     * Create a new notification instance.
     */
    public function __construct(AssetAssignment $assignment, string $actionType)
    {
        $this->assignment = $assignment;
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram']; // <-- Hanya kirim via Telegram
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        $assignment = $this->assignment;
        $asset = $assignment->asset;
        $employee = $assignment->employee;
        $url = route('assets.show', $asset->id); // Link ke detail aset

        // Pesan berbeda untuk checkout dan checkin
        if ($this->actionType === 'checkout') {
            $actionText = "✅ *Aset Diserahkan*";
            $date = $assignment->assigned_date->isoFormat('D MMM YYYY, HH:mm');
            $condition = $assignment->condition_on_assign;
            $docNumber = $assignment->checkout_doc_number;
        } else {
            $actionText = "🔄 *Aset Dikembalikan*";
            $date = $assignment->returned_date->isoFormat('D MMM YYYY, HH:mm');
            $condition = $assignment->condition_on_return;
            $docNumber = $assignment->return_doc_number;
        }

        return TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID')) // Kirim ke Chat ID dari .env
            ->content("{$actionText}\n\n*Aset:* {$asset->name} (`{$asset->asset_code_ypt}`)\n*Pegawai:* {$employee->name}\n*Tanggal:* {$date}\n*Kondisi:* {$condition}\n*No. Dokumen:* `{$docNumber}`")
            ->button('Lihat Detail Aset', $url); // Tambahkan tombol
    }

    /**
     * Get the array representation of the notification. (Tidak digunakan, tapi biarkan saja)
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

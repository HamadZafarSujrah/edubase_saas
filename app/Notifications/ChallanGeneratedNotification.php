<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Finance\Challan;

class ChallanGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $challan;

    /**
     * Create a new notification instance.
     */
    public function __construct(Challan $challan)
    {
        $this->challan = $challan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Currently supporting Database, but ready for SMS/Mail
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Fee Challan Generated')
                    ->line('A new fee challan has been generated for ' . $this->challan->student->first_name)
                    ->action('View Challan', url('/print-challans?search=' . $this->challan->student->admission_no))
                    ->line('Please pay before ' . $this->challan->due_date->format('d-M-Y'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'challan_id' => $this->challan->id,
            'challan_no' => $this->challan->challan_no,
            'amount' => $this->challan->total_amount,
            'message' => 'Fee challan for ' . $this->challan->month . ' has been generated.',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOverdueStatusMail extends Notification
{
//    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Task $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Aufgabe wurde durch einen Admin bearbeitet')
                    ->line('Deine Aufgabe:')
                    ->line('### '.$this->task->title)
                    ->line('wurde von einem Admin bearbeitet, obwohl die Deadline bereits überschritten ist!')
                    ->greeting('Hallo!')
                    ->salutation('---')
            ;
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

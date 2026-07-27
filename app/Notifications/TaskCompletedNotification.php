<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification
{
    use Queueable;

    public $task;
    public $user;
    public function __construct($task, $user)
    {
        $this->task=$task;
        $this->user=$user;
    }

    public function via( $notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'message' => $this->user->name . ' kullanıcısı "' . $this->task->title . '" görevini tamamladı.'
        ];
    }

   
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;
    public $task;

    
    public function __construct($task)
    {
        $this->task=$task;
    }

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id'=>$this->task->id,
            'title'=>'Teslim Tarihi yaklaşıyor.',
            'message'=>"'" . $this->task->title . "' adlı görevin teslimine 1 gün kaldı!"        ];
    }
}

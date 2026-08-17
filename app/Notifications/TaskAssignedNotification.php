<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
     
    public function __construct($task)
    {
        $this->task=$task;
    }
    public function via($notifiable)
    {
        return ['database'];
    }

    
    public function toDatabase($notifiable)
    {
       return[
        'task_id'=>$this->task->id,
        'message'=>'Yeni bir görev atandı: ' . $this->task->title
       ];
    }

    
}

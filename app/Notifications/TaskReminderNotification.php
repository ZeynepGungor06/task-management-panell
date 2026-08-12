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
        if ($notifiable->fcm_token != null) {
            try {
                $messaging = \Kreait\Laravel\Firebase\Facades\Firebase::messaging();
                
                $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $notifiable->fcm_token)
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create(
                        'Teslim Tarihi Yaklaşıyor! ⏳',
                        "'" . $this->task->title . "' adlı görevin teslimine 1 gün kaldı!"
                    ))
                    ->withData(['task_id' => (string) $this->task->id]); 
                    
                $messaging->send($message);
                
            } catch (\Exception $e) {
                // Firebase veya token hatası olursa sistemi çökertme, sadece loga yaz
                \Illuminate\Support\Facades\Log::error('Hatırlatıcı Firebase Hatası: ' . $e->getMessage());
            }
        }


        return [
            'task_id'=>$this->task->id,
            'title'=>'Teslim Tarihi yaklaşıyor.',
            'message'=>"'" . $this->task->title . "' adlı görevin teslimine 1 gün kaldı!"        ];
    }
}

<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage; // ✅ correct



class MetaAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $meta;
    protected $addedBy;

    public function __construct($meta, $addedBy)
    {
        $this->meta = $meta;
        $this->addedBy = $addedBy;     
    }

    public function via($notifiable)
    {
          return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
        {
            return [
                'meta_id' => $this->meta->id,
                'meta_name' => $this->meta->identifier,
                'added_by_id' => $this->addedBy->id,
                'added_by_name' => $this->addedBy->name,
                'profile_picture' => $this->addedBy->profile ?? null,
                'message' => "New meta '{$this->meta->identifier}' added by {$this->addedBy->name}",
            ];
        }

        public function toArray($notifiable)
        {
            // This is used by broadcasting fallback and for API responses
            return [
                'meta_id' => $this->meta->id,
                'meta_name' => $this->meta->identifier,
                'added_by_id' => $this->addedBy->id,
                'added_by_name' => $this->addedBy->name,
                'profile_picture' => $this->addedBy->profile ?? null,
                'message' => "New meta '{$this->meta->identifier}' added by {$this->addedBy->name}",
            ];
        }



    // // public function toDatabase($notifiable)
    // // {
    // //     $adata =  [
    // //         'meta_id' => $this->meta->id,
    // //         'meta_name' => $this->meta->identifier,
    // //         'added_by_id' => $this->addedBy->id,
    // //         'added_by_name' => $this->addedBy->name,
    // //         'profile_picture' => $this->addedBy->profile_picture ?? null,
    // //         'message' => "New meta '{$this->meta->identifier}' added by {$this->addedBy->name}",
    // //     ];
    // //     return $adata;
    // // }

    // public function toBroadcast($notifiable)
    // {
    //     return new BroadcastMessage([
    //         'meta_id' => $this->meta->id,
    //         'meta_name' => $this->meta->identifier,
    //         'added_by_id' => $this->addedBy->id,
    //         'added_by_name' => $this->addedBy->name,
    //         'profile_picture' => $this->addedBy->profile_picture ?? null,
    //         'message' => "New meta '{$this->meta->identifier}' added by {$this->addedBy->name}",
    //     ]);
    // }


}




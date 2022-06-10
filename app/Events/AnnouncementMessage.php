<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $type_id;
    public $description;
    public $departments;
    public $created_at;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type_id,$departments,$description,$created_at)
    {
        $this->type_id = $type_id ;
        $this->departments = $departments ;
        $this->description = $description ;
        $this->created_at = $created_at;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('announcements');
    }
    public function broadcastAs(){
        return "announcement";
    } 
}

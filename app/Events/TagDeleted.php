<?php

namespace App\Events;

use App\Models\Tag;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TagDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The tag ID.
     *
     * @var int
     */
    public $tagId;

    /**
     * The user ID.
     *
     * @var int
     */
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tag $tag)
    {
        $this->tagId = $tag->id;
        $this->userId = $tag->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'tag.deleted';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'tag_id' => $this->tagId,
        ];
    }
} 
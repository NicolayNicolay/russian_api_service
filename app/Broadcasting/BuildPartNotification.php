<?php

declare(strict_types=1);

namespace App\Broadcasting;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Modules\Users\Models\User;

class BuildPartNotification implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public string $partId = '',
        public string $message = ''
    ) {
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.User.' . $this->user->id);
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param $id
     * @return bool
     */
    public function join(User $user, $id): bool
    {
        return $user->id === (int) $id;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'build.part.notifications';
    }

    /**
     * @return string[]
     */
    public function broadcastWith(): array
    {
        return ['partId' => $this->partId, 'message' => $this->message];
    }
}

<?php

declare(strict_types=1);

namespace App\Broadcasting;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Modules\Users\Models\User;

class UserNotifications implements ShouldBroadcast
{
    use SerializesModels;

    public User $user;
    public string $message;
    public string $title;

    /**
     * Create a new event instance.
     *
     * @param \Modules\Users\Models\User $user
     * @param string $message
     * @param string $title
     */
    public function __construct(User $user, string $message = '', string $title = '')
    {
        $this->user = $user;
        $this->message = $message;
        $this->title = $title;
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->user->id);
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param \Modules\Users\Models\User $user
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
        return 'user.notifications';
    }

    /**
     * @return string[]
     */
    public function broadcastWith(): array
    {
        return ['message' => $this->message, 'title' => $this->title];
    }
}

<?php

namespace App\Events\Ots\Messages;

use App\Models\OtsEvent;
use App\Models\OtsMessageUserStatistic;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReadMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public $user_id;
    /**
     * @var int
     */
    public $message_id;

    /**
     * ReadMessage constructor.
     *
     * @param int $user_id
     * @param int $message_id
     */
    public function __construct(int $user_id, $message_id)
    {
        $this->user_id = $user_id;
        $this->message_id = $message_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.User.' . $this->user_id);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function broadcastWith()
    {
        $result = [];

        $user = (new User())->getById($this->user_id);

        if(!$user) {
            throw new \Exception('User not found');
        }

        $data = (new OtsMessageUserStatistic())
            ->getTagsByUserAndSite($user->id, $user->site_id);

        if($data) {
            foreach($data as $item) {
                $result[] = array_intersect_key(
                    $item->toArray(),
                    array_flip(['count', 'count_read', 'tag'])
                );
            }
        }

        return ['statistic' => $result, 'message_id' => $this->message_id];
    }
}

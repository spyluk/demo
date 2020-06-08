<?php
/**
 *
 * User: sergei
 * Date: 16.01.19
 * Time: 16:53
 */

namespace App\Services\Ots\Messages;

use App\Date;
use App\Events\Ots\Messages\NewMessage;
use App\Events\Ots\Messages\ReadMessage;
use App\Models\OtsEvent;
use App\Models\OtsEventUser;
use App\Models\OtsMessage;
use App\Models\OtsMessageEvent;
use App\Models\OtsMessageStatistic;
use App\Models\OtsMessageUser;
use App\Components\Database\StructuringResult;
use App\Models\OtsMessageUserStatistic;
use App\Models\OtsMessageUserTag;
use App\Models\User;
use App\Services\VariableService;
use Illuminate\Database\Eloquent\Model;

class MessageService
{
    /**
     * Messages tag list
     */
    const DEFAULT_LIST_MESSAGE_TAGS = 'list.tags.messages';

    /**
     * @var User
     */
    protected $user;

    /**
     * Message constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $subject
     * @param string $message
     * @param int|User $from_user
     * @param int $site_id
     * @param int $to_user
     * @param $tags
     * @return OtsMessage|null
     */
    public function messageToUser(string $subject, string $message, $from_user, int $site_id, int $to_user, $tags)
    {
        if($from_user = $this->prepareUser($from_user)) {
            return $this->addMessage($subject, $message, $from_user, $site_id, [$to_user], $tags);
        }
        return null;
    }

    /**
     * @param string $subject
     * @param string $message
     * @param int|User $from_user
     * @param int $main_id
     * @return OtsMessage|bool|null
     */
    public function messageReply(string $subject, string $message, $from_user, int $main_id)
    {
        $messageModel = new OtsMessage();
        $messageUserBridge = new OtsMessageUser();
        if(($from_user = $this->prepareUser($from_user)) &&
            ($parent = $messageModel->getMainMessageByIdAndUser($main_id, $from_user->getId())) &&
            ($to_users = $messageUserBridge->getUsersByMessageId([$main_id])))
        {
            $to_users = StructuringResult::apply($to_users, ['$user_id' => 'user_id']);
            $site_id = $parent['site_id'];
            return $this->addMessage($subject, $message, $from_user, $site_id, $to_users, null, $main_id);
        }

        return false;
    }

    /**
     * @param string $subject
     * @param string $message
     * @param int|User $from_user
     * @param int $event_id
     * @param $tags
     * @return Model|null
     */
    public function messageToEvent(string $subject, string $message, $from_user, int $event_id, $tags)
    {
        if(($from_user = $this->prepareUser($from_user)) &&
            ($to_users = (new OtsEventUser())->getEventUsers($event_id)) &&
            ($event = (new OtsEvent())->getById($event_id)))
        {
            $to_users = StructuringResult::apply($to_users, ['$user_id' => 'user_id']);
            $site_id = $event['site_id'];
            if(($message = $this->addMessage($subject, $message, $from_user, $site_id, $to_users, $tags))) {
                $this->addMessageEvent($message['id'], $event_id);
            }

            return $message;
        }

        return null;
    }

    /**
     * @param int|User $from_user
     * @return User
     */
    protected function prepareUser($from_user)
    {
        if(!$from_user instanceof User) {
            $from_user = (new User())->getById($from_user);
        }

        return $from_user;
    }

    /**
     * @param array $tags
     * @param int $page
     * @param array $joins
     * @param int $per_page
     * @return mixed
     */
    public function getList($tags = [], int $page = 1, $joins = ['users'], $per_page = 10) {

        $data = (new OtsMessage)
            ->getList($this->getUser()->getId(), $tags, $page, $per_page);
        $data = $data->toArray();

        $result['items'] = $data['items'];
        unset($data['items']);
        $result['pagination'] = $data;

        $result['items'] = $this->join($result['items'], $joins);
        return $result;
    }

    /**
     * @param int $message_id
     * @param array $joins
     * @return mixed
     */
    public function getMessage(int $message_id, $joins = ['user'])
    {
        $message = (new OtsMessage)->getMessageByIdAndUser($message_id, $this->getUser()->getId());

        if($message && $joins) {
            $message = $this->join([$message], $joins)[0];
        }

        return $message;
    }

    /**
     * @param int $message_id
     * @param int $page
     * @param bool $set_checked
     * @param int $per_page
     * @param array $joins
     * @return mixed
     */
    public function getMessageThread(int $message_id, int $page = 1, $set_checked = true, $joins = ['user'], $per_page = 100)
    {
        $data = (new OtsMessage)
            ->getPaginationMessageThreadByIdAndUser($message_id, $this->getUser()->getId(), $page, $per_page)
            ->toArray();

        $result['items'] = $data['items'];
        unset($data['items']);
        $result['pagination'] = $data;

        $result['items'] = $this->join($result['items'], $joins);

        if($set_checked && $result['items']) {
            $this->setRead($message_id, $this->getUser()->id);
        }

        return $result;
    }

    /**
     * @param $data
     * @param $joins
     * @return array
     */
    protected function join($data, $joins)
    {
        if($data) {
            foreach ($joins as $join) {
                switch ($join) {
                    case 'users':
                        $data = $this->joinUsers($data);
                        break;
                    case 'user':
                        $data = $this->joinUser($data);
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function joinUser($data)
    {
        foreach ($data as $key => $item) {
            $data[$key]['user'] = [
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'],
                'user_id' => $item['user_id']
            ];
        }

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function joinUsers($data)
    {
        $users = (new OtsMessageUser())->getUsersByMessageId(array_column($data, 'id'));

        if($users) {

            foreach($users as $user) {
                $messageUser[$user['message_id']][] = array_intersect_key(
                    $user,
                    array_flip(['user_id', 'first_name', 'last_name'])
                );
            }

            foreach ($data as $key => $item) {
                if(!empty($messageUser[$data[$key]['id']])) {
                    $data[$key]['users'] = $messageUser[$data[$key]['id']];
                }
            }
        }
        return $data;
    }

    /**
     * @param int $message_id
     * @param int $user_id
     */
    public function setRead($message_id, $user_id)
    {
        if($messages = (new OtsMessage)->getMessageThreadByIdAndUser($message_id, $user_id)) {
            if(($tags = (new OtsMessageUserTag())->getItemsByFields(['user_id' => $user_id, 'message_id' =>  $message_id]))) {
                $tags = StructuringResult::apply($tags->toArray(), ['tag_id']);
                $messageUserStatistic = new OtsMessageUserStatistic();
                $has_not_read = false;
                foreach ($messages as $message) {
                    if (!$message['read']) {
                        $has_not_read = true;
                        $messageUserStatistic->increaseUserSiteTagStatistic($user_id, $message['site_id']);

                        foreach($tags as $tag_id) {
                            $messageUserStatistic->increaseUserSiteTagStatistic($user_id, $message['site_id'], $tag_id);
                        }

                        (new OtsMessageUser())->setUserRead($message['id'], $user_id, Date::nowTimestamp());
                    }
                }

                if($has_not_read) {
                    event(new ReadMessage($user_id, $message_id));
                }
            }
        }
    }

    /**
     * @param string $subject
     * @param string $message
     * @param User $from_user
     * @param int $site_id
     * @param array $to_users
     * @param null $tags
     * @param int|null $main_id
     * @return OtsMessage|null|Model
     */
    protected function addMessage(string $subject, string $message, User $from_user, int $site_id, array $to_users, $tags = null, int $main_id = null)
    {
        $tags = is_array($tags) ? $tags : ($tags ? [$tags] : []);
        if($from_user->getSiteId() == $site_id) {

            $from_user_id = $from_user->getId();
            $messageData = [
                'subject' => $subject,
                'message' => $message,
                'user_id' => $from_user_id,
                'site_id' => $site_id,
            ];

            $message = new OtsMessage();

            if ($main_id) {
                $messageData['main_id'] = $main_id;
            }

            $message = $message->create($messageData);
            $messageArr = $message->toArray();
            $this->addMessageUsers($messageArr['id'], $from_user_id, $to_users);
            $this->addMessageStatistic($messageArr);
            $to_users[] = $from_user_id;
            $this->addMessageUserStatistic($messageArr, $from_user_id, $to_users, $site_id, [$from_user_id], $tags);

            return $message;
        }

        return null;
    }

    /**
     * @param $site_id
     * @param $user
     * @return array
     */
    protected function getFilteredTags($tags, $site_id, $user)
    {
        $result = [];
        $available_tags = VariableService::getVars(static::DEFAULT_LIST_MESSAGE_TAGS, $site_id, $user);
        if($available_tags && !empty($available_tags[static::DEFAULT_LIST_MESSAGE_TAGS])) {
            $result = StructuringResult::apply($available_tags[static::DEFAULT_LIST_MESSAGE_TAGS], [
                '$id' => 'vcode'
            ]);
        }

        return array_flip(array_intersect_key(
            array_flip($result),
            array_flip($tags)));
    }

    /**
     * @param $main_message_id
     * @param $user_id
     * @return array
     */
    protected function getParentMessageTags($main_message_id, $user_id) {
        $result = [];
        $available_tags =  (new OtsMessageUserTag())->getItemsByFields(['message_id' => $main_message_id, 'user_id' => $user_id]);
        if($available_tags) {
            $result = StructuringResult::apply($available_tags->toArray(), [
                '$tag_id' => 'tag_id'
            ]);
        }

        return $result;
    }

    /**
     * @param int $message_id
     * @param int $from_user_id
     * @param array $to_users
     */
    protected function addMessageUsers(int $message_id, int $from_user_id, array $to_users)
    {
        if (($key = array_search($from_user_id, $to_users)) !== false) {
            unset($to_users[$key]);
        }
        $messageUserBridge = new OtsMessageUser();
        $messageUserBridge->create(['message_id' => $message_id, 'user_id' => $from_user_id, 'read' => true, 'read_at' => Date::nowTimestamp()]);
        foreach($to_users as $user) {
            $messageUserBridge->create(['message_id' => $message_id, 'user_id' => $user]);
        }
    }

    /**
     * @param array $message
     */
    protected function addMessageStatistic(array $message)
    {
        $main_id = !empty($message['main_id']) ? $message['main_id'] : $message['id'];
        $messageStatisticBridge = new OtsMessageStatistic();
        $messageStatistic = $messageStatisticBridge->getById($main_id);
        $messageStatistic = $messageStatistic ? $messageStatistic->toArray() : [];

        if(!$messageStatistic) {
            $messageStatistic = [];
            $messageStatistic['main_id'] = $main_id;
        }

        $messageStatistic['last_id'] = $message['id'];
        $messageStatistic['last_at'] = Date::nowTimestamp();
        $messageStatistic['count'] = !empty($messageStatistic['count']) ? ++$messageStatistic['count'] : 1;

        $messageStatisticBridge->updateOrCreate(['main_id' => $main_id], $messageStatistic);
    }

    /**
     * @param array $message
     * @param int $from_user_id
     * @param array $users
     * @param int $site_id
     * @param array $users_read
     * @param array $tags
     */
    protected function addMessageUserStatistic(array $message, $from_user_id, array $users, int $site_id, $users_read = [], $tags = [])
    {
        $users = array_unique($users);
        $messageUserStatisticBridge = new OtsMessageUserStatistic();
        $messageUserTagBridge = new OtsMessageUserTag();
        foreach($users as $user_id) {
            if(!empty($message['main_id'])) { // get tags from parent message
                $approved_tags = $this->getParentMessageTags($message['main_id'], $user_id);
            } else {
                $approved_tags = $this->getFilteredTags($tags, $site_id, $this->prepareUser($user_id));
            }
            $approved_tags[0] = '';
            foreach ($approved_tags as $tag_id => $tag) {
                $tag_id = !$tag_id ? null : $tag_id;
                $userStatistic = $messageUserStatisticBridge->getByUserAndSite($user_id, $site_id, $tag_id);
                $userStatistic = $userStatistic ? $userStatistic->toArray() : [];
                $id = !empty($userStatistic['id']) ? $userStatistic['id'] : null;

                if (!$userStatistic) {
                    $userStatistic = [];
                    $userStatistic['user_id'] = $user_id;
                    $userStatistic['site_id'] = $site_id;
                    $userStatistic['tag_id'] = $tag_id;
                }

                $userStatistic['count'] = !empty($userStatistic['count']) ? ++$userStatistic['count'] : 1;

                if (in_array($user_id, $users_read)) {
                    $userStatistic['count_read'] = !empty($userStatistic['count_read']) ? ++$userStatistic['count_read'] : 1;
                }

                $messageUserStatisticBridge->updateOrCreate(['id' => $id], $userStatistic);

                if($tag_id &&
                    empty($message['main_id'])) { //add tags only for root messages
                    $messageUserTagBridge->create([
                            'user_id' => $user_id,
                            'message_id' => $message['id'],
                            'tag_id' => $tag_id]
                    );
                }
            }

            event(new NewMessage($user_id, $from_user_id, ($message['main_id'] ?? $message['id'])));
        }
    }

    /**
     * @param int $main_id
     * @param int $event_id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function addMessageEvent(int $main_id, int $event_id)
    {
        return (new OtsMessageEvent())->create(['main_id' => $main_id, 'event_id' => $event_id]);
    }
}
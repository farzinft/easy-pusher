<?php

namespace Farzin\EasyPusher;

use function config;
use function count;
use Exception;
use Illuminate\Support\Collection;
use function preg_match;

class EasyPusher
{
    protected $users;
    protected $user;
    protected $eventName;
    protected $formattedChannels = [];
    protected $data = [];
    protected $channels;

    public function __construct()
    {
        $this->loadChannels();
    }

    protected function loadChannels()
    {
        $this->channels = config('easy-pusher.channels');

    }

    public function toUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function toUsers(Collection $users)
    {
        $this->users = $users;
        return $this;
    }

    public function withEvent($eventName)
    {
        $this->eventName = $eventName;
        return $this;
    }

    protected static function generateEventName($eventName)
    {
        if (class_exists($eventName)) {
            $event = class_basename($eventName);
        } elseif (is_string($eventName)) {
            $event = $eventName;
        } else {
            throw new \Exception('Event Name Must Be String Or Class NameSpace');
        }
        return $event;
    }


    public function withData(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function send()
    {
        try {
            $this->generateChannels();

            if (count($this->formattedChannels)) {
                $response = app('pusher')->trigger(
                    $this->formattedChannels, $this->eventName, json_encode($this->data), null, true
                );

                if ((is_array($response) && $response['status'] >= 200 && $response['status'] <= 299)
                    || $response === true
                ) {
                    return;
                }

                throw new Exception(
                    is_bool($response) ? 'Failed to connect to Pusher.' : $response['body']
                );
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }

    }

    protected function getUserChannel($channel, $user)
    {
        return $channel . '.' . $user->id;
    }

    protected function generateChannels()
    {
        foreach ($this->channels as $channel) {
            if (preg_match('/^(private).*/', $channel)) {
                if (isset($this->user)) {
                    $this->formattedChannels[] = $this->getUserChannel($channel, $this->user);
                }
                if (isset($this->users) && $this->users instanceof Collection) {
                    $this->users->each(function ($user) use ($channel) {
                        $this->formattedChannels[] = $this->getUserChannel($channel, $user);
                    });
                }
            } else {
                $this->formattedChannels[] = $channel;
            }
        }
    }
}

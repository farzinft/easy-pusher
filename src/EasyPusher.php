<?php

namespace EasyPusher;

use function config;
use Exception;
use Illuminate\Support\Collection;

class EasyPusher
{
    protected $users;
    protected $user;
    protected $eventName;
    protected $channels = [];
    protected $data = [];
    protected $channel;

    public function __construct()
    {

        if (config('easy-pusher.type') == 'private') {
            $this->channel = 'private-' . config('easy-pusher.channel-name');
        } else {
            $this->channel = config('easy-pusher.channel-name');
        }

    }

    public function to($user)
    {
        $this->user = $user;
        return $this;
    }


    public function withEvent($eventName)
    {
        $this->eventName = $eventName;
        return $this;
    }

    public function toUsers(Collection $users)
    {
        $this->users = $users;
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

            if (!empty($this->channels)) {
                $response = app('pusher')->trigger(
                    $this->channels, $this->eventName, json_encode($this->data), null, true
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

        }


    }

    protected function getUserChannel($user)
    {
        return $this->channel . '.' . $user->id;
    }

    protected function generateChannels()
    {
        if (config('easy-pusher.type') == 'private') {
            if (isset($this->user)) {
                $this->channels[] = $this->getUserChannel($this->user);
            }

            if (isset($this->users) && $this->users instanceof Collection) {
                $this->users->each(function ($user) {
                    $this->channels[] = $this->getUserChannel($user);
                });
            } else {
                throw new Exception('Users Must Be a Collection');
            }
        } else {
            $this->channels[] = $this->channel;
        }

    }
}

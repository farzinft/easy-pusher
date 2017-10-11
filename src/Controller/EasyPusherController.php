<?php

namespace EasyPusher\Controller;

use App\Http\Controllers\Controller;
use function collect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EasyPusherController extends Controller
{
    protected $channels;

    public function __construct()
    {

        $this->middleware('auth');

        $this->setChannels();

    }

    public function auth(Request $request)
    {
        if (Str::startsWith($request->channel_name, ['private-', 'presence-']) &&
            !$request->user()) {
            throw new HttpException(403);
        }

        $channelName = Str::startsWith($request->channel_name, 'private-')
            ? str_replace_first('private-', '', $request->channel_name)
            : str_replace_first('presence-', '', $request->channel_name);

        return $this->verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    protected function setChannels()
    {
        $privateChannels = collect(config('easy-pusher.channels'))
            ->where('type', 'private')->all();

        foreach ($privateChannels as $privateChannel) {
            $this->channels[$privateChannel['channel-name'] . '.{userId}'] = function ($user, $userId) {
                if ($user->id == $userId) {
                    return true;
                }
                return false;
            };
        }

    }

    protected function verifyUserCanAccessChannel($request, $channel)
    {

        foreach ($this->channels as $pattern => $callback) {

            if (!Str::is(preg_replace('/\{(.*?)\}/', '*', $pattern), $channel)) {
                continue;
            }
            $parameters = $this->extractChannelKeys($pattern, $channel);

            if ($result = $callback($request->user(), $parameters['userId'])) {
                return $this->validAuthenticationResponse($request, $result);
            }
        }

        throw new HttpException(403);
    }


    private function extractChannelKeys($pattern, $channel)
    {
        preg_match('/^' . preg_replace('/\{(.*?)\}/', '(?<$1>[^\.]+)', $pattern) . '/', $channel, $keys);

        return $keys;
    }

    public function validAuthenticationResponse($request, $result)
    {
        if (Str::startsWith($request->channel_name, 'private')) {
            return $this->decodePusherResponse(
                app('pusher')->socket_auth($request->channel_name, $request->socket_id)
            );
        }
    }

    protected function decodePusherResponse($response)
    {
        return json_decode($response, true);
    }

}

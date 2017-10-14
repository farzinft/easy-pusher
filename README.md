# Easy Pusher
Now It's not hard to broadcast events through public or private channels,
you can send notify to specific or collection of users,

### Installation

  - `composer require "farzin/easy-pusher":"dev-master"`


### Add Service Providers
  - `Vinkla\Pusher\PusherServiceProvider::class`
  - `Farzin\EasyPusher\EasyPusherServiceProvider::class`
### Add Facade 
    'EasyPusher' => Farzin\EasyPusher\EasyPusherFacade::class


### JS Guide:
first you need add pusher.js to your html file 
`<script src="https://js.pusher.com/4.1/pusher.min.js"></script>`
```
//For Private Channels: 

 window.userId = '{{ auth()->check() ? auth()->user()->id : null}}';
    Pusher.logToConsole = true;
    var pusherPrivate = new Pusher('YOUR PUSHER KEY', {
        authEndpoint: '/easy-pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        cluster: 'us2',
        encrypted: true
    });
    var pusherPublic = new Pusher('YOUR PUSHER KEY', {
        cluster: 'us2',
        encrypted: true
    });
    var channels = {!! json_encode(config('easy-pusher.channels')) !!};
    for(var i = 0; i < channels.length; i++) {
        var channel = channels[i];
        if (channel.match(/^(private).*/)) {
            pusherPrivate.subscribe(channel + '.' + window.userId);
        } else {
            pusherPublic.subscribe(channel);
        }
	}
    pusherPublic.bind('sample-event', function () {
		alert();
    })

```



### Back End Guide:
first you need publish config file:
`php artisan vendor:publish`
then in easy-pusher.php config file your need to define your channels, for private channels prefix with 'private';
```
return [
    'channels' => [
        'private-sample-channel', //prefix with public or private
        'public-sample-channel'
    ]
];
```

 for broadcasting events

```
use EasyPusher;

//broadcast event to public channels
EasyPusher::withEvent('sample-event')->withData(array $data)->send();


//for private channels

//collection of users
EasyPusher::withEvent('sample-event')->toUsers(Collection $users)->withData(array $data)->send();

//specific user | user Model
EasyPusher::withEvent('sample-event')->toUser($user)->withData(array $data)->send();
```
you can pass event name as string or qualified class name.




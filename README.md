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
    var pusherPrivate = new Pusher('YOUR PUSHER APP KEY'
        authEndpoint: '/easy-pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        cluster: 'us2',
        encrypted: true
    });
    
    //for private channels subscribe events with 'private' prefix
       pusherPrivate
        .subscribe('private-sample-channel.' + window.userId)
        .bind('sample-event', function (data) {
            alert();
        })
        
//for public channels
var pusher =  new Pusher('YOUR PUSHER APP KEY');
  pusher
        .subscribe('sample-public-channel'
        .bind('sample-event', function (data) {
            alert();
        })
```



### Back End Guide:
first you need publish config file:
`php artisan vendor:publish`
then in easy-pusher.php config file your need to define your channels:
```
  'channels' => [
      [
          'channel-name' => 'sample-channel',
          'type' => 'private' //or private
      ],
      [
          'channel-name' => 'sample-public-channel',
          'type' => 'public'
      ],
      .
      .
  ]
```

 for broadcasting events

```
use EasyPusher;

//all users
EasyPusher::withEvent('sample-event')->withData(array $data)->send();

//collection of users
EasyPusher::withEvent('sample-event')->toUsers(Collection $users)->withData(array $data)->send();

//specific user | user Model
EasyPusher::withEvent('sample-event')->toUser($user)->withData(array $data)->send();
```
you can pass event name as string or qualified class name.




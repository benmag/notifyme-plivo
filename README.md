# NotifyMe Plivo

A Plivo gateway for [NotifyMe](https://github.com/notifymehq/notifyme).

## Example 
Here is an example of a notification with Plivo:

```php
<?php

// Create a factory for notifications
$notifierFactory = new NotifyMeHQ\NotifyMe\NotifyMeFactory();

// Create the new notification for plivo
$plivoNotifier = $notifierFactory->make([
  // Specify that we will use slack
  'driver' => 'plivo',
  // Who send this message
  'from'   => 'Plivo',
  // Add auth tokens
  'auth_id'  => 'XXXXX',
  'auth_token'  => 'XXXXXXXXXXXXX',
]);

/* @var \NotifyMeHQ\Contracts\ResponseInterface $response */
$response =  $plivoNotifier->notify('1111111111', 'Hi, text from Plivo');

echo $response->isSent() ? 'Message sent' : 'Message going nowhere';
```

## License

NotifyMe is licensed under [The MIT License (MIT)](LICENSE).

# RogerBundle

My common code (abstract classes, interfaces...).

## Configuration

Configure messenger:

```
# config/packages/messenger.yaml

framework:
    messenger:
        # Uncomment this (and the failed transport below) to send failed messages to this transport for later handling.
        failure_transport: failed

        transports:
            # https://symfony.com/doc/current/messenger.html#transport-configuration
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
            failed: 'doctrine://default?queue_name=failed'
            sync: 'doctrine://default?queue_name=sync'

        routing:
            # Route your messages to the transports
            # 'App\Message\YourMessage': async
            'Beerfranz\RogerBundle\Message\RogerAsyncMessage': async
            'Beerfranz\RogerBundle\RogerSyncMessage': sync

when@test:
   framework:
       messenger:
           transports:
               # replace with your transport name here (e.g., my_transport: 'in-memory://')
               # For more Messenger testing tools, see https://github.com/zenstruck/messenger-test
               async: 'test://'
               failed: 'test://'
               sync: 'test://'
               # async: 'in-memory://'

```
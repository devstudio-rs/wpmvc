# Logger

File logger with size-based rotation.

## Setup

```php
'components' => array(
    'logger' => array(
        'class'     => \wpmvc\components\Logger::class,
        'directory' => '@upload.basedir/logs',  // default
        'group'     => 'app',                   // default log file name
        'size'      => 20,                      // rotation threshold in MB
    ),
),
```

## Usage

```php
use wpmvc\components\Logger;

App::$app->logger->log( 'Something happened', Logger::TYPE_INFO );
App::$app->logger->log( 'Something broke', Logger::TYPE_ERROR );
App::$app->logger->log( 'Heads up', Logger::TYPE_WARNING );

// Write to a different group (file): logs/payments.log
App::$app->logger->log( 'Charge failed', Logger::TYPE_ERROR, 'payments' );
```

Messages are written as:

```
[2026-07-14 12:34:56] error: Charge failed
```

## Rotation

When a log file exceeds the `size` limit (default 20 MB), it is renamed to
`{group}-{n}.log` and a fresh file is started.

# CronTranslator

CronTranslator explains / translates crontab settings into human readable format.

## Run

```
composer.phar install
php explain.php "*/15 8-16,3 * * *"
```

## Docker

```
make install
make explain
```

## Example output

```
For cron */15 8-16,3 * * *

    Will run   :
    At minute  : every 15
    At hour    : 8 until 16 and 3
    At day     : any
    At month   : any
    At weekday : any
    
    Last run   : 21/06/2013 16:45:00
    Next runs  : 22/06/2013 03:00:00
                 22/06/2013 03:15:00
                 22/06/2013 03:30:00
                 22/06/2013 03:45:00
                 22/06/2013 08:00:00
                 22/06/2013 08:15:00
                 22/06/2013 08:30:00
                 22/06/2013 08:45:00
                 22/06/2013 09:00:00
                 22/06/2013 09:15:00
```

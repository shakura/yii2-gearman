yii2-gearman
============

This extension built on [this](https://github.com/Filsh/yii2-gearman) and [this](https://github.com/sinergi/gearman).
The goal of the project is opportunity of starting multiple worker processes on one machine. 

## Installation

It is recommended that you install the Gearman library [through composer](http://getcomposer.org/). To do so, add the following lines to your ``composer.json`` file.

```json
{
    "require": {
       "shakura/yii2-gearman": "dev-master"
    }
}
```

## Configuration

```php
'components' => [
  'gearman' => [
      'class' => 'shakura\yii2\gearman\GearmanComponent',
      'servers' => [
          ['host' => '127.0.0.1', 'port' => 4730],
      ],
      'user' => 'www-data',
      'jobs' => [
          'syncCalendar' => [
              'class' => 'common\jobs\SyncCalendar'
          ],
          ...
      ]
  ]
],
...
'controllerMap' => [
    'gearman' => [
        'class' => 'shakura\yii2\gearman\GearmanController',
        'gearmanComponent' => 'gearman'
    ],
    ...
],
```

## Job example

```php
namespace common\jobs;

use shakura\yii2\gearman\JobBase;

class SyncCalendar extends JobBase
{
    public function execute(\GearmanJob $job = null)
    {
        // Do something
    }
}
```

## Manage workers

```cmd
yii gearman/start 1 // start the worker with unique id
yii gearman/restart 1 // restart worker
yii gearman/stop 1 // stop worker
```

## Example using Dispatcher

```php
Yii::$app->gearman->getDispatcher()->background('syncCalendar', new JobWorkload([
    'params' => [
        'data' => 'value'
    ]
])); // run in background
Yii::$app->gearman->getDispatcher()->execute('syncCalendar', new JobWorkload([
    'params' => [
        'data' => 'value'
    ]
])); // run synchronize
```

## Example of [Supervisor](http://supervisord.org/) config to manage multiple workers

```
[program:yii-gearman-worker]
command=php [path_to_your_app]/yii gearman/start %(process_num)s
process_name=gearman-worker-%(process_num)s
priority=1
numprocs=5
numprocs_start=1
autorestart=true
```

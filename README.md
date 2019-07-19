# Phalcon - профилирование запросов 

Библиотека для профилирования запросов. Может отправлять данные профилирования на сервер pinba или в файл. 

**Инициализация**

В конфиг файле нужно прописать настройки профайлера:
````php
  
    return [
        'driver' => env('PROFILER_DRIVER', 'pinba'),
    ];
````

Добавить его в DI контейнер:
````php
    $di = \Phalcon\Di::getDefault();
    $di->setShared('profiler', function () use ($di) {
      $configProfiler = new Config([
        'hostName'   => 'prod1',
        'serverName' => 'test.com'
      ]);

      return new Chocofamily\Profiler\Pinba($configProfiler);
    });  
````

Один раз в начале запуска приложения указать скрипт:

````php
$url = $application->router->getMatchedRoute()->getPattern();
$method = $application->request->getMethod();

$application->getDI()->get('profiler')->script($method.': '.$url);
````

Теперь в нужном месте можно отправлять данные для профилирования в Pinba:

````php
$profiler = \Phalcon\Di::getDefault()->get('profiler');
$this->profiler->start([
    'group'    => 'database',
    'type'     => 'SELECT',
    'query'    => 'SELECT * FROM tags',
    'params' => [],
]);

// Какая-та логика приложения

$profiler->stop();
````

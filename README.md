# Phalcon - профилирование запросов 

Библиотека для профилирования запросов. Может отправлять данные профилирования на сервер pinba или в файл. 

**Инициализация**

В конфиг файле нужно прописать настройки профайлера:
````php
  
    return [
        ...
        'profiler' => [
            'default' => 'pinba',
            
            'drivers' => [
                'pinba' => [
                  'adapter' => 'Pinba',
                  'host' => 'example.com',
                ],
                'file' => [
                  'adapter' => 'File',
                  'host' => 'example.com',
                ],
            ],
        ],
        ...
    ];
````

Добавить его в DI контейнер:
````php
    $di = \Phalcon\Di::getDefault();
    $di->set('profiler', function () use ($di) {
      $host   = $di->getShared('config')->domain;
      $server = $di->getShared('config')->server;

      $config  = $di->getShared('config')->profiler;
      $adapter = 'Chocofamily\Profiler\\'.$config->drivers[$config->default]->adapter;

      return new $adapter($host, $server);
    });  
````

Один раз в начале запуска приложения указать скрипт:

````php
$url = $application->request->getURI();
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

Библиотека автоматом подставляет параметры correlation_id и span_id. Эти параметры нужны для отслеживания запроса по 
нескольким сервисам. Используется библиотека https://github.com/chocofamilyme/pathcorrelation
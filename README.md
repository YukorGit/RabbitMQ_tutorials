# RabbitMQ_tutorials

Запуск и проверка: 

1. __Hello World.__
- Открыть один терминал. Перейти в директорию rabbitmq_tutorials/01_hello_world/. 
- Запустить консьюмера: php receive.php
  - Вывод: [*] Waiting for messages. To exit press CTRL+C
- Открыть второй терминал. Перейди в ту же директорию.
- Запустить продюсера: php send.php
  - Вывод: [x] Sent 'Hello World!'

Результат: _В первом терминале (где консьюмер) должен быть вывод:_ [x] Received Hello World!

2. __Work queues.__
- Открыть два или три терминала. В каждом перейти в директорию rabbitmq_tutorials/02_work_queues/.
- В каждом из этих терминалов запустить по одному экземпляру рабочего: php worker.php
  - Вывод: [*] Waiting for messages. To exit press CTRL+C
- Открыть еще один терминал. Перейди в ту же директорию.
- Запустить несколько раз new_task.php, можно с разными "сложностями" (количеством точек):
  - php new_task.php message.
  - php new_task.php message..
  - php new_task.php message...
  - php new_task.php message....
  - php new_task.php message.....
  
Результат: _Наблюдая за терминалами с рабочими. Можно увидеть, как задачи распределяются между ними. 
Рабочий, получивший задачу с большим количеством точек, будет "занят" дольше, и следующие задачи пойдут другим, свободным рабочим._

3. __Publish - Subscribe.__
- Открыть два или три терминала. В каждом перейти в директорию rabbitmq_php_tutorials/03_publish_subscribe/.
- В каждом из этих терминалов запустить по одному экземпляру консьюмера: php receive_logs.php
  - Вывод: [*] Waiting for logs. To exit press CTRL+C и уникальное имя своей очереди.
- Открыть еще один терминал. Перейти в ту же директорию.
- Запустить несколько раз emit_log.php:
  - php emit_log.php "This is message!"
  - php emit_log.php "Another one."

Результат: _Наблюдая за терминалами с консьюмерами. Можно увидеть, как __каждый__ из них должен получить __каждое__ отправленное сообщение._

4. __Routing.__
- Открыть несколько терминалов. В каждом перейти в директорию rabbitmq_php_tutorials/04_routing/.
- Терминал 1 (слушает только error):
  - php receive_logs_direct.php error
- Терминал 2 (слушает warning и info):
  - php receive_logs_direct.php warning info
- Терминал 3 (слушает все: info, warning, error):
  - php receive_logs_direct.php info warning error
- Открыть еще один терминал (для продюсера). Перейти в ту же директорию.
- Запустить несколько раз emit_log_direct.php с разными уровнями:
  - php emit_log_direct.php info "Informational message."
  - php emit_log_direct.php warning "Warning message."
  - php emit_log_direct.php error "Error message."

Результат: _Команда 1 - должно прийти в Терминал 2 и Терминал 3. Команда 2 - должно прийти в Терминал 2 и Терминал 3.
Команда 3 - должно прийти в Терминал 1 и Терминал 3._

5. __Topics.__
- Открыть несколько терминалов. В каждом перейти в директорию rabbitmq_php_tutorials/05_topics/.
- Терминал 1 (слушает все логи ядра kern):
  - php receive_logs_topic.php "kern.*"
- Терминал 2 (слушает все сообщения, связанные с critical):
  - php receive_logs_topic.php "*.critical"
- Терминал 3 (слушает все сообщения от auth, а также error):
  - php receive_logs_topic.php "auth.#" "*.error"
- Терминал 4 (слушает вообще все сообщения):
  - php receive_logs_topic.php "#"
- Открыть еще один терминал. Перейди в ту же директорию.
- Запустить несколько раз emit_log_topic.php с разными ключами маршрутизации:
  - php emit_log_topic.php kern.info "Kern info."
  - php emit_log_topic.php kern.critical "Kern critical!"
  - php emit_log_topic.php auth.info.login "User login."
  - php emit_log_topic.php app.user.error "User not found."
  - php emit_log_topic.php cron.warning "Cron warnings!"

Результат: _Команда 1 - должно прийти в Терминал 1 и Терминал 4. Команда 2 - должно прийти в Терминал 1, Терминал 2 и Терминал 4.
Команда 3 - должно прийти в Терминал 3 и Терминал 4. Команда 4 - должно прийти в Терминал 3 и Терминал 4.
Команда 5 - должно прийти только в Терминал 4._

6. __RPC__
- Открыть один терминал. Перейти в директорию rabbitmq_php_tutorials/06_rpc/.
- Запустить RPC сервер: php rpc_server.php
  - Вывод: [x] Awaiting RPC requests
- Открыть другой терминал. Перейти в ту же директорию.
- Запустить RPC клиента (можно несколько раз с разными числами):
  - php rpc_client.php
    - Клиент: [x] Requesting fib(30)
    - Сервер: [.] fib(30)
    - Клиент: [.] Got 832040
  - php rpc_client.php 10
    - Клиент: [x] Requesting fib(10)
    - Сервер: [.] fib(10)
    - Клиент: [.] Got 55

7. __Publisher confirms.__
- Открыть один терминал, перейди в rabbitmq_php_tutorials/07_publisher_confirms/.
- Запустить консьюмера:
  - php receive.php
    - Вывод: [*] Waiting for messages. To exit press CTRL+C
- Открыть второй терминал, перейди в ту же директорию.
- Запустить продюсера с подтверждениями:
  - php send_with_confirms.phр
    - Продюссер: [x] Sent 'Hello World with Confirms!'
      [✓] Message ACKed. Body: Hello World with Confirms!
      [!] All messages confirmed by broker.
    - Консьюмер:  [x] Received Hello World with Confirms!
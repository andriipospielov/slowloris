Задание для курса Highload software architecture
=======

для сборки проекта без защиты

    make build-dummy

для сборки с защитой

    make build-protected

запустит сервер (защищенный или нет) и контейнер с масштабированым (5 шт) атакующим скриптом
`make run-dummy` или `make run-protected` 

количество потоков атаки, имя хоста-жертвы и порт задаются переменными окружения

    ATTACK_NUMBER_OF_THREADS [500]
    VICTIM_HOST [victim.localhost]
    VICTIM_PORT [80]

соответсвенно

инструкция, которая выводит время и код ответа раз в несколько секунд (реверс-прокси в проекте нет, поэтому подразумеватся, что веб-сервер зарезолвится через локалхост), запускается на хоствой машине
    
    make show-response-time
для минимизаии урона использованы модули апача
``mod_reqtimeout``, ``mod_qos``, ``mod_security``
для этих модулей использована следующая конфигурация:

ограничиваем время для передачи данных хедера до 20с. время для передачи хедеров -- 40с
на передачу тела запроса -- 20 с. минимальная скорость перерачи для всего -- 500 б/с

    <IfModule mod_reqtimeout.c>
      RequestReadTimeout header=20-40,MinRate=500 body=20,MinRate=500
    </IfModule>
    
    
лимитируем до 256  TCP соединий сервер. для одного ip -- максимум 50 соединий, отключаем keep-alive, если ипспользутся >= 180 соедининй. Ограничиваем минимальную скорость до 150 байтов/с, или до 1200 б/с,когда достигнут лимит по клиентам.
    
    <IfModule mod_qos.c>
       QS_ClientEntries 100000
       QS_SrvMaxConnPerIP 50
       MaxClients 256
       QS_SrvMaxConnClose 180
       QS_SrvMinDataRate 150 1200
    </IfModule>
    
трекаем тайм-ауты (408й код ответа). если их больше 5 за минуту -- баним айпишник на 5 минут
    
    <IfModule mod_security.c>
        SecRule RESPONSE_STATUS "@streq 408" "phase:5,t:none,nolog,pass,
        setvar:ip.slow_dos_counter=+1, expirevar:ip.slow_dos_counter=60, id:'1234123456'"
    
        SecRule IP:SLOW_DOS_COUNTER "@gt 5" "phase:1,t:none,log,drop,
        msg:'Client Connection Dropped due to high number of slow DoS alerts', id:'1234123457'"
    </IfModule>

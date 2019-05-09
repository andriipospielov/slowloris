#!/usr/bin/env bash

while true
do
    curl 'localhost'  --compressed -s -o /dev/null -w  "%{time_starttransfer}\n"

sleep 5s
done
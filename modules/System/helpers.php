<?php

function str_replace_once($search, $replace, $text)
{
    $pos = strpos($text, $search);
    return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
}
function accessHistory(): bool
{
    $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
    $is_admin = ($remote_addr === config('app.admin_ip') || $remote_addr === config('app.homestead_ip') || $remote_addr === config('app.docker_ip'));
    if ($is_admin) {
        return true;
    } else {
        return false;
    }
}

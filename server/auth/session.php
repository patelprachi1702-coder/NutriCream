<?php

$rememberLifetime = 60 * 60 * 24 * 30;
$rememberRequested = (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' &&
    ($_POST['action'] ?? '') === 'login' &&
    ($_POST['remember_device'] ?? '') === '1'
);
$rememberCookieSet = ($_COOKIE['nutricream_remember'] ?? '') === '1';
$rememberSession = $rememberRequested || $rememberCookieSet;

if ($rememberSession) {
    ini_set('session.gc_maxlifetime', (string) $rememberLifetime);
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

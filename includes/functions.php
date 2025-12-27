<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function requireRole($role)
{
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        die("Access Denied: You do not have permission to view this page.");
    }
}

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function formatDate($date)
{
    return date("M d, Y", strtotime($date));
}

function formatTime($time)
{
    return date("h:i A", strtotime($time));
}

// Flash message helper
function setFlash($message, $type = 'success')
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        // Store in data attributes for JS to pick up
        return "<div id='flash-message' data-message='" . htmlspecialchars($flash['message'], ENT_QUOTES) . "' data-type='{$flash['type']}' style='display:none;'></div>";
    }
    return '';
}
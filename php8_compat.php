<?php
/*
|--------------------------------------------------------------------------
| Compatibilidad PHP 8 para funciones legacy de juega123
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('activePage')) {
    function activePage($page = '', $class = 'ui-btn-active')
    {
        $current = isset($_GET['page']) ? (string)$_GET['page'] : '';

        if (is_array($page)) {
            return in_array($current, $page, true) ? $class : '';
        }

        return ($current === (string)$page) ? $class : '';
    }
}

if (!function_exists('redirigir')) {
    function redirigir($url = 'index.php')
    {
        $url = trim((string)$url);
        if ($url === '') {
            $url = 'index.php';
        }

        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }

        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        echo '<!doctype html>';
        echo '<html><head>';
        echo '<meta http-equiv="refresh" content="0;url=' . $safeUrl . '">';
        echo '<script>window.location.href = ' . json_encode($url) . ';</script>';
        echo '</head><body>';
        echo 'Redirigiendo... <a href="' . $safeUrl . '">continuar</a>';
        echo '</body></html>';
        exit;
    }
}

if (!function_exists('redirect')) {
    function redirect($url = 'index.php')
    {
        redirigir($url);
    }
}

if (!function_exists('isPost')) {
    function isPost()
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }
}

if (!function_exists('getParam')) {
    function getParam($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }
}

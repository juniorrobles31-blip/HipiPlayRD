<?php
$good = isset($good) ? $good : "good";
$error = isset($error) ? $error : "error";

if (isset($_GET[$good])) {
    echo '<div class="good">* Se ejecutó correctamente</div>';
} elseif (isset($_GET[$error])) {
    echo '<div class="error">* ' . htmlspecialchars($_GET[$error], ENT_QUOTES, "UTF-8") . '</div>';
}
?>

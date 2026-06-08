<?php
/**
 * System.php local compatible con PHP 8.x.
 * Sustituye el PEAR System.php antiguo de XAMPP cuando alguna librerÃ­a legacy lo requiere.
 */
if (!class_exists('System')) {
    class System {
        public static function which($program, $fallback = false) {
            $paths = explode(PATH_SEPARATOR, getenv('PATH') ?: '');
            $exts = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? array('.exe', '.bat', '.cmd', '') : array('');
            foreach ($paths as $path) {
                foreach ($exts as $ext) {
                    $candidate = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $program . $ext;
                    if (is_file($candidate) && is_executable($candidate)) { return $candidate; }
                }
            }
            return $fallback;
        }
        public static function tmpdir() { return sys_get_temp_dir(); }
        public static function mktemp($args = null) {
            $dir = sys_get_temp_dir();
            $prefix = 'tmp';
            if (is_string($args) && $args !== '') { $prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', basename($args)) ?: 'tmp'; }
            $file = tempnam($dir, $prefix);
            return $file ?: false;
        }
        public static function mkDir($args) {
            $paths = is_array($args) ? $args : array($args);
            $ok = true;
            foreach ($paths as $p) {
                if ($p === '-p') { continue; }
                if (!is_dir($p) && !mkdir($p, 0777, true)) { $ok = false; }
            }
            return $ok;
        }
        public static function rm($args) {
            $paths = is_array($args) ? $args : array($args);
            $ok = true;
            foreach ($paths as $p) {
                if (is_string($p) && strlen($p) && $p[0] === '-') { continue; }
                if (is_dir($p)) {
                    $it = new RecursiveDirectoryIterator($p, FilesystemIterator::SKIP_DOTS);
                    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                    foreach ($files as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
                    if (!rmdir($p)) { $ok = false; }
                } elseif (is_file($p)) {
                    if (!unlink($p)) { $ok = false; }
                }
            }
            return $ok;
        }
        public static function cat($args) {
            $paths = is_array($args) ? $args : array($args);
            $out = '';
            foreach ($paths as $p) { if (is_file($p)) { $out .= file_get_contents($p); } }
            return $out;
        }
    }
}
?>


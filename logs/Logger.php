<?php
declare(strict_types=1);

final class Logger
{
    private static string $file;

    public static function init(string $filePath): void
    {
        self::$file = $filePath;

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    public static function log(string $text): void
    {
        $line = date('Y-m-d H:i:s') . ' | ' . $text . PHP_EOL;
        file_put_contents(self::$file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function echo(string $text): void
    {
        self::log($text);
        echo $text;
    }
}

?>

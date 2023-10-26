<?php

namespace xQuery\Random;

class Random
{
    /**
     * @var string
     */
    private static string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @param string $characters
     * @return void
     */
    public static function AddCharacters(string $characters): void
    {
        self::$characters .= $characters;
    }

    /**
     * @param string ...$characters
     * @return void
     */
    public static function RemoveCharacters(string ...$characters): void
    {
        self::$characters = str_replace($characters, '', self::$characters);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function String(int $length = 10): string
    {
        // Characters
        $chars = self::$characters;
        // Characters Length
        $charsLen = strlen($chars) - 1;
        // Random Characters
        $randStr = '';

        for ($__i__ = 0; $__i__ < $length; ++$__i__) {
            $randStr .= $chars[rand(0, $charsLen)];
        }

        return $randStr;
    }

    /**
     * @param int $min
     * @param int $max
     * @param array $exceptions
     * @return int
     */
    public static function Port(int $min = 1000, int $max = 65000, array $exceptions = []): int
    {
        // Set Minimum Limit
        $min = max($min, 1000);
        // Set Maximum Limit
        $max = min($max, 65000);

        while (true) {
            // Create Random Port
            $randomPort = rand($min, $max);
            // Check Exceptions
            if (!$exceptions || !in_array($randomPort, $exceptions)) return $randomPort;
        }
    }

    /**
     * @param string|null $timeMid
     * @return string
     */
    public static function UUID(string $timeMid = null): string
    {
        // Time Low
        $timeLow = sprintf('%08x', mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16));
        // Time Mid
        $timeMid = $timeMid ?? sprintf('%04x', mt_rand(0, 0xffff));
        $uuid = sprintf('%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            // Time Hi
            (4 << 12) | (mt_rand(0, 0x1000)),
            // Clock Seq Hi
            (1 << 7) | (mt_rand(0, 128)),
            // Clock Seq Low
            mt_rand(0, 255),
            // Nodes
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255),
            mt_rand(0, 255)
        );

        return "{$timeLow}-{$timeMid}-{$uuid}";
    }
}
<?php

namespace xQuery\Tools;

class Format
{
    /**
     * @param int $size
     * @param int $format
     * @param int $precision
     * @param bool $arrayReturn
     * @return array|string
     */
    public static function Bytes(
        int  $size,
        int  $format = 0,
        int  $precision = 0,
        bool $arrayReturn = false
    ): array|string
    {
        $base = log($size, 1024);
        $units = match ($format) {
            1 => ['بایت', 'کلوبایت', 'مگابایت', 'گیگابایت', 'ترابایت'], # Persian
            2 => ['B', 'K', 'M', 'G', 'T'],
            default => ['B', 'KB', 'MB', 'GB', 'TB']
        };

        if ($size) {
            $result = pow(1024, $base - floor($base));
            $result = round($result, $precision);
            $unit = $units[floor($base)];
        } else {
            $result = 0;
            $unit = $units[0];
        }

        return $arrayReturn ? [$result, $unit] : "$result $unit";
    }

    /**
     * @param int $seconds
     * @param int $format
     * @param bool $arrayReturn
     * @return array|string
     */
    public static function Seconds(
        int  $seconds,
        int  $format = 0,
        bool $arrayReturn = false
    ): array|string
    {
        $units = match ($format) {
            1 => ['سال', 'ماه', 'روز', 'ساعت', 'دقیقه', 'ثانیه'], # Persian
            default => ['Year(s)', 'Month(s)', 'Day(s)', 'Hour(s)', 'Minute(s)', 'Second(s)']
        };
        $time = 0;
        $unit = $units[count($units)-1];
        $secFormat = [31207680, 26006400, 86400, 3600, 60, 1];

        for ($__i__ = 0; $__i__ < count($secFormat); $__i__ ++) {
            if ($seconds > $secFormat[$__i__]) {
                $time = round($seconds / $secFormat[$__i__]);
                $unit = $units[$__i__];
                break;
            }
        }

        return $arrayReturn ? [$time, $unit] : "$time $unit";
    }

    public static function ServerAddress(string $address): string
    {
        if (filter_var($address, FILTER_VALIDATE_URL)) {
            $address = str_ends_with($address, '/') ? $address : "$address/";
            $httpsAddress = str_replace(['api://', 'http://', 'https://'], 'https://', $address);
            $curl = curl_init();
            $options = [
                CURLOPT_URL => $httpsAddress,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
            ];
            curl_setopt_array($curl, $options);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode && $httpCode < 300) return $httpsAddress;

            return str_replace('https://', 'http://', $httpsAddress);
        }

        return '';
    }
}
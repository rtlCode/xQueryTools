<?php

namespace xQuery\Tools;

class HTTP
{
    /**
     * @var array
     */
    private static array $httpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @param int $code
     * @return string
     */
    public static function GetHttpCode(int $code): string
    {
        return self::$httpCodes[$code] ?? 'Unknown Error';
    }

    /**
     * @param int $code
     * @param string $message
     * @return void
     */
    public static function SetHttpCode(int $code, string $message): void
    {
        self::$httpCodes[$code] = $message;
    }

    /**
     * @param int $code
     * @param string|null $message
     * @param array|object|null $result
     * @return array
     */
    public static function Response(
        int $code,
        string $message = null,
        array|object $result = null
    ): array
    {
        $response = [
            'status' => $code && $code < 300,
            'message' => $message ?? self::GetHttpCode($code),
            'result' => $result
        ];

        if ($code >= 300) $response['error_code'] = $code;

        return $response;
    }

    /**
     * @param string $address
     * @param array $parameters
     * @param array $curlOptions
     * @param string $method
     * @return object
     */
    private static function Curl(
        string $address,
        array $parameters = [],
        array $curlOptions = [],
        string $method = 'POST'
    ): object
    {
        // Curl init
        $curl = curl_init();
        // Curl set options
        $curlOptions[CURLOPT_URL] = $address;
        $curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        $curlOptions[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        $curlOptions[CURLOPT_POSTFIELDS] = json_encode($parameters);
        curl_setopt_array($curl, $curlOptions);
        // Curl response
        $response = curl_exec($curl);
        // Curl http code
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Result format
        $result = new \stdClass();
        $result->statusCode = $httpCode;
        $result->response = json_decode($response, true) ?: [];
        $result->curl = $curl;
        // Curl close
        curl_close($curl);

        return $result;
    }

    /**
     * @param string $address
     * @return string
     */
    public static function Format(string $address): string
    {
        // Check url format
        if (filter_var($address, FILTER_VALIDATE_URL)) {
            // Add a slash to the end of the address
            $address = str_ends_with($address, '/') ? $address : "$address/";
            // Convert to https address
            $httpsAddress = str_replace(['api://', 'http://', 'https://'], 'https://', $address);
            // SSL check
            $sendRequest = self::Curl($httpsAddress, [], [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
            ], 'GET');
            $httpCode = $sendRequest->statusCode;
            // Return https address
            if ($httpCode && $httpCode < 300) return $httpsAddress;
            // Return http address
            return str_replace('https://', 'http://', $httpsAddress);
        }

        return '';
    }

    /**
     * @param string $address
     * @param array $parameters
     * @param array $curlOptions
     * @param string $method
     * @return object
     */
    public static function Request(
        string $address,
        array $parameters = [],
        array $curlOptions = [],
        string $method = 'POST'
    ): object
    {
        // Format Address
        $address = self::Format($address) ?: $address;

        return self::Curl($address, $parameters, $curlOptions, $method);
    }
}
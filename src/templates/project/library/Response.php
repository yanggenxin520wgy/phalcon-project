<?php


namespace @@namespace@@\Library;


class Response extends \Phalcon\Http\Response
{
    // Informational 1xx
    const CODE_CONTINUE = 100;
    const CODE_SWITCHING_PROTOCOLS = 101;
    const CODE_PROCESSING = 102;            // RFC2518
    const CODE_EARLY_HINTS = 103;           // RFC8297
    // Success 2xx
    const CODE_OK = 200;
    const CODE_CREATED = 201;
    const CODE_ACCEPTED = 202;
    const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    const CODE_NO_CONTENT = 204;
    const CODE_RESET_CONTENT = 205;
    const CODE_PARTIAL_CONTENT = 206;
    const CODE_MULTI_STATUS = 207;          // RFC4918
    const CODE_ALREADY_REPORTED = 208;      // RFC5842
    const CODE_IM_USED = 226;               // RFC3229
    // Redirection 3xx
    const CODE_MULTIPLE_CHOICES = 300;
    const CODE_MOVED_PERMANENTLY = 301;
    const CODE_MOVED_TEMPORARILY = 302;
    const CODE_SEE_OTHER = 303;
    const CODE_NOT_MODIFIED = 304;
    const CODE_USE_PROXY = 305;
    const CODE_RESERVED = 306;
    const CODE_TEMPORARY_REDIRECT = 307;
    const CODE_PERMANENTLY_REDIRECT = 308;  // RFC7238
    // Client Error 4xx
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_PAYMENT_REQUIRED = 402;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_METHOD_NOT_ALLOWED = 405;
    const CODE_NOT_ACCEPTABLE = 406;
    const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const CODE_REQUEST_TIMEOUT = 408;
    const CODE_CONFLICT = 409;
    const CODE_GONE = 410;
    const CODE_LENGTH_REQUIRED = 411;
    const CODE_PRECONDITION_FAILED = 412;
    const CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    const CODE_REQUEST_URI_TOO_LONG = 414;
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const CODE_EXPECTATION_FAILED = 417;
    const CODE_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const CODE_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    const CODE_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const CODE_LOCKED = 423;                                                      // RFC4918
    const CODE_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const CODE_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    const CODE_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const CODE_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const CODE_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    // Server Error 5xx
    const CODE_INTERNAL_SERVER_ERROR = 500;
    const CODE_NOT_IMPLEMENTED = 501;
    const CODE_BAD_GATEWAY = 502;
    const CODE_SERVICE_UNAVAILABLE = 503;
    const CODE_GATEWAY_TIMEOUT = 504;
    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
    const CODE_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const CODE_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const CODE_LOOP_DETECTED = 508;                                               // RFC5842
    const CODE_BANDWIDTH_LIMIT_EXCEEDED = 509;
    const CODE_NOT_EXTENDED = 510;                                                // RFC2774
    const CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585


    /**
     * 设置跨域请求
     */
    public function setCorsPolicy()
    {
        $this->setHeader('Access-Control-Allow-Origin','*');
        $this->setHeader('Access-Control-Allow-Credentials', true);
        $this->setHeader('Access-Control-Allow-Methods','OPTIONS,POST,GET,PUT,DELETE');
        $this->setHeader('Access-Control-Allow-Headers','Authorization,Content-Type,Content-Length,Accept-Encoding,X-Requested-with,Origin');
    }
}
<?php namespace CSP;

class SourceHasher
{
    public $http;
    public $fs;
    function __construct($http, $fs)
    {
        $this->http = $http;
        $this->fs   = $fs;
    }

    function fetch($source)
    {
        if (strpos($source, "http") === 0) {
            return ($this->http)($source);
        }

        return ($this->fs)($source);
    }

    function hash($source, $algo='sha256')
    {
        $content = $this->fetch($source);
        switch ($algo) {
            case "sha384":
            case "sha512":
            case "sha256":
                $algo = $algo;
                break;
            default:
                $algo = 'sha256';
        }
        
        $raw = hash($algo, $content, true);

        if (!$raw) {
            throw new \Exception("Invalid algorightm for hashing");
        }
        return "{$algo}-" . base64_encode($raw);
    }

    static function create()
    {
        return new static(
            function($url) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); 
                curl_setopt($curl, CURLOPT_TIMEOUT, 3);

                $data = curl_exec($curl);
                curl_close($curl);
                return $data;
            },
            function ($file) {
                return file_get_contents($file);
            }
        );
    }
}
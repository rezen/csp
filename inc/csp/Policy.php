<?php namespace CSP;

class Policy
{
    public $sampleAll = false;

    public $directives = [];

    public $nonce;

    function __construct($resolver, $directives = [])
    {
        $this->resolver = $resolver;
        $this->directives = $directives;
    }

    function toString()
    {
        $directives = array_map(function($directive) {
            if ($this->sampleAll) {
                $this->directive->addSource("'report-sample'");
            }
            return $directive->toString();
        }, $this->directives);
        return implode(";", $directives);
    }

    function addDirectiveString($directive)
    {
        $parts = explode(" ", trim(preg_replace('/\s+/', ' ', $directive)));
        $sources = array_slice($parts, 1);
        return $this->addDirective($parts[0], $sources);
    }

    function addDirective($name, $sources=[], $replace=false)
    {
        if (is_string($sources)) {
            $sources = explode(" ", trim(preg_replace('/\s+/', ' ', $sources)));
        }
    
        if (array_key_exists($name, $this->directives) && !$replace) {
            // Existing directive ... extra sources
            array_map(function($source) use ($name) {
                $this->directives[$name]->addSource($source);
            }, $sources);
            return $this;
        } 

        // New directive ....
        $directive = $this->resolver->resolve($name);
        $directive->setSources($sources);
        $this->directives[$name] = $directive;
        return $this;
    }

    // For adding scripts, calculating sha256 etc
    function addScript($source, $calculateHash=false)
    {
        // parse domain or calculate hash
    }

    function hash()
    {   
        $policy = $this->toString();
        $policy = preg_replace('/nonce-[a-z0-9]+/', 'nonce-', $policy);
        $parts = str_split($policy);
        sort($parts);
        return md5(implode('', $parts));
    }

    static function create()
    {
        $resolver = DirectiveResolver::create();
        return new self($resolver, []);
    }

    static function fromString($policy)
    {
        $object = self::create();
        array_map(function($line) use ($object) {
            $object->addDirectiveString($line);
        }, explode(";", $policy));
        return $object;
    }
}
<?php namespace CSP;


class Directive 
{
    public static $quotedSources = [
        'self', 
        'none', 
        'unsafe-inline', 
        'unsafe-eval', 
        'strict-dynamic', 
        'report-sample',
    ];

    public static $quotedWildcardSources = [
        'nonce-',
        'sha256-', 
        'sha384-' ,
        'sha512-' ,
    ];

    public static $schemeSources = [
        'http:',
        'https:',
        'data:',
        'mediastream:',
        'blob:',
        'filesystem:',
    ];

    public $name;
    public $sources;
    
    function __construct($name)
    {
        $this->name = $name;
        $this->sources = [];
    }

    function hasSource($source)
    {
        return in_array($source, $this->sources);
    }

    function hasSelf()
    {
        return in_array("'self'", $this->sources);
    }

    function isQuotedSource($source)
    {
        if (strpos($source, "'") === 0) {
            $source = substr($source , 1, strlen($source) - 2);
        }

        if (in_array($source, static::$quotedSources)) {
            return true;
        }

        $matches =  array_filter(static::$quotedWildcardSources, function($test)  use ($source) {
            return strpos($source, $test) !== false; // @todo can be better
        });

        return count($matches) > 0;
    }

    function isResourceSource($source)
    {
        if (in_array($source, static::$schemeSources)) {
            return true;
        }
        // @todo regex?
        return true;
    }

    function isValidSource($source)
    {   
        $hasQuotes = (strpos($source, "'") === 0);
        if ($this->isQuotedSource($source)) {
            return true;
        } else if ($hasQuotes) {
            return false;
        }

        if ($this->isResourceSource($source)) {
            return true;
        }

        return false;
    }

    function setSources(array $sources)
    {
        return array_map([$this, 'addSource'], $sources);
    }

    function isEmpty()
    {
        return count($this->sources) === 0;
    }

    function removeSource($source)
    {
        $this->sources = array_filter($this->sources, function($src) use ($source) {
            if ($source === 'nonce' && strpos($src, "'nonce-") === 0) {
                return false;
            }
            return $src !== $source;
        });
    }

    function addSource($source)
    {
        if (strlen($source) === 0) {
            // @todo invalid?
            return;
        }
    
        $source = trim($source);
        if (!$this->isValidSource($source)) {
            throw new \Exception("Invalid source $source");
        }

        if ($this->isQuotedSource($source) && strpos($source, "'") !== 0) {
            $source = "'{$source}'";
        }
    
        // @todo normalize source for quoted sources
        if (!in_array($source, $this->sources)) {
            $this->sources[] = $source;
        }
    }

    function sourcesAsString()
    {
        $sources = $this->sources;
        sort($sources);
        return implode(" ", $sources);
    }
    function toString()
    {
        return $this->name . " " . $this->sourcesAsString();
    }

    static function fromString($directive)
    {
        $parts = explode(" ", trim(preg_replace('/\s+/', ' ', $directive)));
        $sources = array_slice($parts, 1);
        $object =  new self($parts[0]);
        $object->setSources($sources);
        return $object;
    }
}

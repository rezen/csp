<?php namespace CSP;

class ReportUriDirective extends Directive 
{
    function isValidSource($source)
    {
        $parsed = parse_url($source);
        return array_key_exists('scheme', $parsed) && 
            array_key_exists('host', $parsed) &&
            array_key_exists('path', $parsed);
    }
}

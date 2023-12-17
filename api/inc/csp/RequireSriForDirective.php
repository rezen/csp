<?php namespace CSP;

// require-sri-for script style;
// only relevant for external domains
class RequireSriForDirective extends Directive 
{
    function isValidSource($source)
    {
        return in_array($source, ['script', 'style']);
    }
}

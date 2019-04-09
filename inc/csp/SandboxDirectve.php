<?php namespace CSP;

// allow-* [scripts, top-navigation, same-origin, presentation, popups-to-escape-sandbox, popups, pointer-lock, orientation-lock, allow-forms]
class SandboxDirective extends Directive 
{
    function isEmpty()
    {
        return false;
    }

    function isValidSource($source)
    {   
        $source = str_replace('allow-', '', $source);
        return in_array($source, [
            'scripts', 
            'top-navigation',
            'same-origin',
            'presentation',
            'popups-to-escape-sandbox',
            'popups',
            'pointer-lock', 
            'orientation-lock', 
            'forms',
        ]);
    }
}

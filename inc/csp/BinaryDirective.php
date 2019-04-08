<?php namespace CSP;

// Has no sources such as block-all-mixed-content
class BinaryDirective extends Directive 
{
    function toString()
    {
        return $this->name;
    }
} 

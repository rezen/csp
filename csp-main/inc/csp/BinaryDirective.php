<?php namespace CSP;

// Has no sources such as block-all-mixed-content
class BinaryDirective extends Directive 
{
    function isEmpty()
    {
        return false;
    }

    function addSource($source) {}
    function toString()
    {
        return $this->name;
    }
} 

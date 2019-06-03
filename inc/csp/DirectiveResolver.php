<?php namespace CSP;

class DirectiveResolver 
{   
    public $directiveNames = [];

    function __construct($directiveNames = [])
    {
        $this->directiveNames = is_array($directiveNames) ? $directiveNames : [];
    }

    function getNames()
    {
        return $this->directiveNames;
    }

    function isValidDirective($name)
    {
        return in_array($name, $this->directiveNames);
    }

    function resolve($directive)
    {
        if (!$this->isValidDirective($directive)) {
            throw Exception("Invalid directive");
        }

        switch ($directive) {
            case "upgrade-insecure-requests":
            case "block-all-mixed-content":
                return new BinaryDirective($directive);
            case "plugin-types":
                return new PluginTypesDirective($directive);
            case "report-uri":
                return new ReportUriDirective($directive);
            default:
                return new Directive($directive);
        }
    }

    static function create()
    {
        return new self([
            'base-uri',
            'block-all-mixed-content',
            'child-src',
            'connect-src',
            'default-src',
            'font-src',
            'form-action',
            'frame-ancestors',
            'frame-src',
            'img-src',
            'manifest-src',
            'media-src',
            'object-src',
            'plugin-types', //  plugin-types application/x-shockwave-flash
            'report-uri', // @todo deprecating
            'require-sri-for', // require-sri-for script style;
            'sandbox',
            'script-src',
            'style-src',
            'upgrade-insecure-requests',
            'worker-src',
            // 'disown-opener',
            // 'navigate-to',
          ]);
    }
}

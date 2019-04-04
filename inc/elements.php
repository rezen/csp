<?php

$elements = [
    [
      'id'  => 'eval-1',
      'label' => "Inline eval",
      'html' => '<p id="{{ id }}">Original before {{ id }}</p>',
      'script' => [
        'nonce'  => false,
        'inline' => 'eval("document.getElementById(\'{{ id }}\').textContent=\'[!] Changed with eval in {{ id }}\'");', 
      ],
      'csp_block' => [
        'script-src'  => "'unsafe-eval'  'nonce-{{ nonce }}'",
      ],
      'category' => 'script-src',
    ],
    [
      'id'  => 'eval-2',
      'label' => "Eval from external" ,
      'html' => '<p id="{{ id }}">Original before {{ id }}</p>',
      'script' => [
        'src'    => 'assets/app.js@evalExample',
        'nonce'  => false,
      ],
      'csp_block' => [
        'script-src'  => "'unsafe-eval'  'nonce-{{ nonce }}'",
      ],
      'category' => 'script-src',
    ],
    [
      'id'  => 'eval-nonce',
      'label' => "Eval from with nonce" ,
      'html' => '<p id="{{ id }}">Original before {{ id }}</p>',
      'script' => [
        'src'    => 'assets/app.js@evalExample',
        'nonce'  => false,
        'inline' => '  eval("document.getElementById(\'{{ id }}\').textContent=\'[!] Changed with eval with nonce\'");'
      ],
      'csp_block' => [
        'script-src'  => "'unsafe-eval'  'nonce-{{ nonce }}'",
      ],
      'category' => 'script-src',
    ],
    [
      'id' => 'style-attr',
      'label' => 'Element with style attribute',
      'html' => '<p style="color:purple">Style in element will make it purple</p>',
      'csp_block' => [],
      'category' => 'style-src',
    ],

    [
      'id' => 'style-inline-nonce',
      "label" => "Style with nonce",
      'html' => [
        "<p id='{{ id }}'>Style makes this blue</p>",
        "<style nonce=\"{{ nonce }}\">#{{ id }}{color: blue;}#{{ id }}:before{content: 'Changed ';}</style>"
      ],
      'category' => 'style-src',
    ],
    [
      'id' => 'style-inline-wo-nonce',
      "label" => "Style without nonce",
      'html' => [
        "<p id='{{ id }}'>Style will make this red</p>",
        "<style>#{{ id }}{color: red;}\n#{{ id }}:before{content: 'Changed ';}</style>"
      ],
      'category' => 'style-src',
    ],

    [
      'id' => 'style-self-external',
      "label" => "Style from stylesheet",
      'html' => [
        "<p id='{{ id }}'>Style will make this aqua</p>",
      ],
      'category' => 'style-src',
    ],

    [
      'id' => 'style-self-external-2',
      "label" => "Style from stylesheet",
      'html' => [
        "<p id='{{ id }}'>Secrets?</p> <input type='password' name='password' value='xyz123' />",
      ],
      'category' => 'style-src',
    ],

    [
      'id'  => 'inline-js-1',
      'label' => "Inline js without nonce" ,
      'html' => '<p id="{{ id }}">Inline JavaScript w/o nonce will change this color to green</p>',
      'script' => [
        'nonce'  => false,
        'inline' => 'document.getElementById("{{ id }}").style.color="green";'
      ],
      'category' => 'style-src',
    ],
    [
      'id'  => 'inline-js-2',
      'label' => "Inline js with nonce" ,
      'html' => '<p id="{{ id }}">Inline JavaScript w/ nonce will change this color to orange</p>',
      'script' => [
        'nonce'  => true,
        'inline' => 'document.getElementById("{{ id }}").style.color="orange";'
      ],
      'category' => 'style-src',
    ],
    [
      'id'  => 'external-style',
      'label' => "Remote stylesheet" ,
      'html' => [
        '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" />',
        '<i class="fas fa-align-justify"></i>',
        'Font awesome icon, remote style'
      ],
      'category' => 'style-src',
    ],
    [
      'id'  => 'fonts-1',
      'label' => "Remote stylesheet & fonts" ,
      'html' => [
        '<p class="google-font">Should be a fancy font if google fonts loaded</p>',
      ],
      'category' => 'style-src',
    
    ],
    [
      'id'  => 'fonts-2',
      'label' => "Remote stylesheet & fonts" ,
      'html' => [
        '<p class="google-font-too">Should be fancy font if @import works in css for google fonts</p>',
      ],
      'category' => 'style-src',
    ],
    [
      "id" => "iframe-remote-youtube",
      "label" => "Youtube embed",
      "html" => [
        "<iframe width='400' height='225' src='https://www.youtube.com/embed/s4wrMMju-Xc' ",
        "frameborder='0' allow='accelerometer;autoplay;encrypted-media;gyroscope;picture-in-picture' allowfullscreen></iframe>",
        ],
        "category" => "",
    ],
    [
        "id" => "iframe-remote-vimeo",
        "label" => "Vimeo embed",
        "html" => [
            "<iframe src='https://player.vimeo.com/video/1084537' width='400' height='225' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>" 
        ],
        "category" => "",
    ],

    [
        "id" => "iframe-local",
        "label" => "Local iframe with xss",
        "html" => [
            "<iframe src='xss.php' width='400' height='225'></iframe>" ,
            "<p id='{{ id }}'>Will change if xss triggers</p>"
        ],
        "category" => "",
    ],

    [
        'id' => 'video-src-local',
        "label" => "Local video",
        "html" => [
          '<video width="400" src="assets/video-1.mp4" controls />'
        ],
          "category" => "",
    ],
  
    [
        'id' => 'img-src-remote',
        "label" => "Remote image",
        "html" => [
          '<img src="https://img.shields.io/badge/build-passing-brightgreen.svg" alt="From another domain" />'
        ],
          "category" => "",
    ],
    [
          'id' => 'img-src-local-1',
          "label" => "Local image",
          "html" => "<img src='assets/pic-1.jpg' height='200' alt='Same source' />",
          "category" => "",
    ],
    [
          'id' => 'img-src-local-2',
          "label" => "Local image",
          "html" => "<img nonce='{{ nonce }}' src='assets/pic-1.jpg' height='200' alt='Same source' />",
          "category" => "",
    ],
    [
        'id' => 'img-src-data',
        "label" => "image dataurl",
        "html" => "<img alt='Data url' src='data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/2wBDAAEBAQEBAQEBAQECAQEBAgICAQECAgMCAgICAgMEAwMDAwMDBAQEBAUEBAQGBgYGBgYICAgICAkJCQkJCQkJCQn/2wBDAQICAgMDAwUEBAUIBgUGCAkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQn/wAARCAAwADADAREAAhEBAxEB/8QAGQABAQEBAQEAAAAAAAAAAAAAAAgKCQUH/8QAKxAAAAQFAwMDBQEAAAAAAAAAAgMEBQABBgcICRESExQWFRciGSRak9YK/8QAFAEBAAAAAAAAAAAAAAAAAAAAAP/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/ANhEAgEAgEAgEBOV3sw8R8fKlQ0ZfvKW3NkKwc0JTo20pV9bs1NOShtONNILVlJXFUSaMkZpJgAmSDxmIApSnuGewfVbb3OtteSi2a5FobhMd1bd1H3Hj1e027JXxlX9meYlP7dciMNIN6R5Qyx8RT4jCIM9pynKA9yp6npqiaaqGs6zqFDSNH0ihVulV1W6KykDa2NqAoR6pWrVHiAUSSSUAQzDBikEIZTnOcpSgJytvnVhHeStGa29ocxrV3VuJUfcePUFTdwmF8el/ZkGKj+3Qolhp5vSIKGYPiGfEARCntKU5wFUwCAxZ/6NdJfUEzxzctbd7FGwHurbunLVslNvNQ+VU2x9F6Rvz4tOT9B4ckZ4uJCwkXMIJgny2kKc5ClIO/WifjfejEbTHxnx6yFoz2+vBb7zPy+kPUULt2nq1Vuzkl+6bT1KUzqJVJY/gYLbltPYUpykFU51W3rS8mEeY1obbs3kdxLq2ruFTdBU93BCPv3p8YViJCn66owogrqnmhDzMGEAd9xClKU5wGLPRP0T9TjEbU4xnyFyFxn9vrP2+8z8vq/zOlHbtPVqUdm1L9q2uylUZ1FSksHwLFty3nsGU5yDfrAIBAIBAIBAIDizqN6YmXGad7qWulYTVYuNgxR7BSqFgcrS0gU8jbXFySr1ysx3Nm3VG0FdY4pUWSLckQuJIdxzlsEIQF9AjUm/Invh+iqf7iAfQI1JvyJ74foqn+4gKpwn0hc3MXsnLZ30u9rO3Uywt3Q3rPkNgKkKfgsr/wCptKtvI7ia2qnIj7U9QBSDkmH8yw7cZ7CCHfqAQCAQCAQCA//Z' />",
        "category" => "",
    ],

    [
      'id' => 'img-src-blob',
      "label" => "image blob",
      "html" => "<img alt='blob url' src='blob:http://localhost:8000/1b7b9af0-127b-43bc-95d9-800df9c08df0' />",
      "category" => "",
    ],

    
    [
        'id' => 'form-local-1',
        "label" => "Local form 1",
        "html" => [
          '<form>',
          '  <input type="text" name="q" placeholder="Search ..." />',
          '  <button type="submit">Search</button>',
          '</form>',
        ],
        "category" => "",
    ],
    [
        'id' => 'form-local-2',
        "label" => "Local form 2",
        "html" => [
          '<form nonce="{{ nonce }}">',
            '  <input type="text" name="q" placeholder="Search ..." />',
            '  <button type="submit">Search</button>',
          '</form>',
        ],
        "category" => "",
    ],
    [
        'id' => 'form-remote-1',
        "label" => "Remote form 1",
        "html" => [
          '<form action="https://www.google.com/search">',
            '  <input type="text" name="q" placeholder="Search ..." />',
            '  <button type="submit">Search</button>',
          '</form>',
        ],
        "category" => "",
    ],
    [
        'id' => 'form-remote-2',
        "label" => "Remote form 2",
        "html" => [
            '<form nonce="{{ nonce }}" action="https://www.google.com/search">',
            '  <input type="text" name="q" placeholder="Search ..." />',
            '  <button type="submit">Search</button>',
            '</form>',
        ],
        "category" => "",
    ],
    [
        'id' => 'ajax-local',
        "label" => "Local AJAX call",
        "html" => "<p id='{{ id }}'>Will change once AJAX is done</p>",
        "category" => "",
        'script' => [
            'src'    => 'assets/app.js@localAjax',
        ],
        "category" => "",
    ],
    [
        'id' => 'stripe-button',
        "label" => "Remote AJAX call to stripe",
        "html" => [
            '<script src="https://checkout.stripe.com/checkout.js"></script>',
            "<button id=\"{{ id }}\">Stripe</button>"
        ],
        "category" => "",
        'script' => [
            'src'    => 'assets/app.js@stripeExample',
        ],
        "category" => "",
    ],
    [
        'id' => 'js-widget-twitter',
        "label" => "Twitter widget",
        "html" => [
            '<a href="https://twitter.com/dandr3ss?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">',
            'Follow @dandr3ss',
            '</a>',
            '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>',
        ],
        "category" => "",
    ],
    [
        'id' => 'media-audio',
        "label" => "Local audio",
        "html" => [
            "<audio src='assets/eddy_-_01_-_Pure_Adrenaline.mp3' controls />",
            "<a href='http://freemusicarchive.org/music/eddy/2_Damn_Loud/Pure_Adrenaline_mastered-with-CloudBounce'>Source</a>"
        ],
        "category" => "",
    ],
    [
        'id' => 'embed-pdf',
        "label" => "Local embed",
        "html" => [
            '<embed src="assets/smashing-the-stack.pdf" type="application/pdf" width="400" height="225">',
            '</embed>'
        ],
        "category" => "",
    ],
    [
        'id' => 'script-src-cloudflare',
        "label" => "Local embed",
        "html" => [
            '<p id="{{ id }}">This changes if cloudflare jquery loads</p>',
            '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>',
        ],
        'script' => [
            'src' => 'assets/app.js@cloudflareJquery',
        ],
        "category" => "",
    ],

    [
        'id' => 'script-src-jsdelivr',
        "label" => "Script from jsdelivr",
        "html" => [
            '<p id="{{ id }}">This changes if jsdelivr d3 loads</p>',
            '<script src="https://cdn.jsdelivr.net/npm/d3@5.9.2/dist/d3.min.js"></script>',
        ],
        'script' => [
            'src' => 'assets/app.js@cdnD3',
        ],
        "category" => "",
    ],
  ];
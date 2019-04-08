'use strict';

window.requestIdleCallback =
  window.requestIdleCallback ||
  function (cb) {
    var start = Date.now();
    return setTimeout(function () {
      cb({
        didTimeout: false,
        timeRemaining: function () {
          return Math.max(0, 50 - (Date.now() - start));
        }
      });
    }, 1);
  }

window.cancelIdleCallback =
  window.cancelIdleCallback ||
  function (id) {
    clearTimeout(id);
  }
// start:localAjax
function localAjax() {
    fetch('ajax.php')
    .then(function(response) {
        return response.json();
    })
    .then(function(d) {
        var el = document.querySelector('#ajax-local');
        el.textContent = d.msg;
    })
    .catch(function(err) {
        var el = document.querySelector('#ajax-local');
        el.textContent = err;
    });
}
// end:localAjax

// start:stripeExample
function stripeExample()
{
    var handler = StripeCheckout.configure({
        key: 'pk_KBCS2K6UgQc8K9VZCtNMOK4AEl5aU',
        image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
        locale: 'auto',
        token: function(token) {
          // You can access the token ID with `token.id`.
          // Get the token ID to your server-side code for use.
        }
    });
      
    document.getElementById('stripe-button').addEventListener('click', function(e) {
        // Open Checkout with further options:
        handler.open({
          name: 'Demo Site',
          description: '2 widgets',
          amount: 10,
        });
        e.preventDefault();
    });
      
    window.addEventListener('popstate', function() {
        handler.close();
    });
}
// end:stripeExample

// start:evalExample
function evalExample() {
    new Function(`document.getElementById('eval-2').textContent='[!] Changed from external using new Function()'`)();
}
// end:evalExample


// start:cloudflareJquery
function cloudflareJquery() {
    jQuery("#script-src-cloudflare").text("Changed using jquery from cloudflare cdn");
}
// end:cloudflareJquery

// start:cdnD3
function cdnD3() {
    d3.select("#script-src-jsdelivr").text("Changed using d3 from jsdelivr");
}
// end:cdnD3


function websocketCsp() {
    var rowTemplate = document.createElement('template');
    rowTemplate.innerHTML = `<tr>
      <td contenteditable class="csp-blocked-uri"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>`;
    var conn = {};
    var id = document.querySelector('[data-doc-id]').getAttribute('data-doc-id');
    var hash = document.querySelector('[pagehash]').getAttribute('pagehash');

    try {
        conn = new WebSocket('ws://localhost:8110?id=' + id + "&h=" + hash);
        console.log(conn);
    } catch (e) {
        console.log("A websocket", e);
    }
    
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log("on-message");
        var reports = document.getElementById('csp-reports');
        var lines = e.data
            .split("\n")
            .filter(l => l.length > 2)
            .map(function(line) {
                try { 
                    return JSON.parse(line.trim());
                } catch (e) {
                    return line.trim();
                }
            })
            .filter(d => !!d)
            .map(d => {
                var el = document.importNode(rowTemplate.content, true);
                var td = el.querySelectorAll("td");
                td[0].textContent = d['blocked-uri'];
                td[0].setAttribute('title', td[0].textContent);
                td[1].textContent = d['violated-directive'];
                td[2].textContent = d['line-number'] || '';
                td[3].textContent = d['column-number'] || '';
                td[4].textContent = d['script-sample'] || '';
                return el;
            })
            .map(el => reports.appendChild(el));

    };
}


function generateBlobImage() {
    var binary = atob("iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==");
    var array = [];
    for (var i = 0; i < binary.length; i++) {
        array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], {type: 'image/png'});
}


document.addEventListener('DOMContentLoaded', function() {
    [
        localAjax,
        cloudflareJquery,
        stripeExample,
        evalExample,
        cdnD3,
    ].map(function(fn) {
        try {
            fn();
        } catch (e) {
            console.error(e);
        }
    });

    window.requestIdleCallback(function() {
        websocketCsp();
    });
});




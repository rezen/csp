'use strict';

window.executed = {};
window.app = {csp: {}};

window.onYouTubeIframeAPIReady = function() {
    Array.from(document.querySelectorAll('[data-youtube]')).map(function(el) {
        var id = el.getAttribute('id');
        new YT.Player(id, {
            height: '225',
            width: '400',
            videoId: 's4wrMMju-Xc'
        });
    })
}

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
  };

  // @todo pageexit to delete logs
function reportViewer() {
    let el1 = document.getElementById('csp-report-viewer');
    if (!el1) {
        return;
    } 
    let d = {
        counter: 0,
        lines: [],
        shouldEnd: false
    };

    el1.style.display = 'block';

    var btn = document.createElement('button');
    btn.innerText = 'Refresh';
    btn.style.display = "none"
    btn.className = "trigger-refresh";
    btn.setAttribute('type', 'button');

    el1.prepend(btn);
    el1.addEventListener('click', function() {
        refresh(d);
    })

    var id = document.querySelector('[data-doc-id]').getAttribute('data-doc-id');
    var hash = document.querySelector('[pagehash]').getAttribute('pagehash');

    var rowTemplate = document.createElement('template');
    rowTemplate.innerHTML = `<tr>
      <td contenteditable class="csp-blocked-uri"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>`;
    let lastSize = -1;
    let counter = 0;

    function redraw(lines) {
        var reports = document.getElementById('csp-reports');
        reports.innerHTML = "";
        lines.map(d => {
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
    }

    function refresh(state, cb) {
        cb = cb || function() {};
        state.counter += 1;
        fetch('/reader.php?id='  + id)
        .then(res => res.text())
        .then(d => {
            let lines = d.split("\n")
                .filter(l => l.length > 2)
                .map(function(line) {
                    try { 
                        return JSON.parse(line.trim());
                    } catch (e) {
                        return line.trim();
                    }
                })
                .filter(d => !!d);
            
            state.lastSize = lines.length;
            redraw(lines);
            if (state.lastSize === lines.length && state.counter > 3) {
                state.shouldEnd = true;
                cb();
            }
        })
    }
    let handle = setInterval(function() {
        refresh(d, function() {
            btn.style.display = "block";
            clearInterval(handle);
            setTimeout(function() {
                refresh(d);
            }, 4000);
        });
    }, 1000);
}

function generateBlobImage() {
    var binary = atob("iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==");
    var array = [];
    for (var i = 0; i < binary.length; i++) {
        array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], {type: 'image/png'});
}

function cspForm() {
    try {
        document.getElementById('img-src-blob').src = URL.createObjectURL(generateBlobImage());
    } catch (e) {}

    // @todo prevent selection of existing directive
    function notSelected() {
        var selects = document.querySelectorAll('#csp-directives select');
        var opts = Array.from(selects[0].querySelectorAll('option')).map(function(el) {
            return el.innerText;
        });
        var selected = Array.from(selects).map(function(el) {
            return el.value;
        })

        return opts.findIndex(function(o) {
            return !selected.includes(o);
        });
    }

    function addNode() {
        if (counter === max) {
            return;
        }
        counter++;
        rowTemplate.innerHTML = row.outerHTML.replace(/\[[0-9]+\]/g, `[${counter}]`);
        var el = document.importNode(rowTemplate.content, true);
        var select = el.querySelector('select');
        select.selectedIndex = notSelected()
        form.appendChild(el);
    }

    function addControls(el) {
        var controls = document.createElement('div');
        controls.className = 'controls';

        el.appendChild(controls)
        var add = document.createElement('button');
        add.type="button"
        add.innerText = "+";
        add.className = "btn-add";
        controls.appendChild(add);
        
        var rem = document.createElement('button');
        rem.type="button";
        rem.innerText = "-";
        rem.className = "btn-remove";
        controls.appendChild(rem);
    }

    var counter = document.querySelectorAll('.csp-directive').length;
    var form = document.getElementById('csp-directives');
    if (!form) {
        return;
    }

    form.addEventListener('click', function(e) {
        if (e.target.className === 'btn-remove') {
            form.removeChild(e.target.parentNode.parentNode);
        } else if (e.target.className === 'btn-add') {
            addNode();
        }
    });
    
    var row = document.querySelector('[form-repeatable]');
    var rowTemplate = document.createElement('template');
    var max = document.querySelectorAll('#csp-directives option').length - 1;

    Array.from(document.querySelectorAll('#csp-form [form-repeatable]')).map(function(el) {
        addControls(el);
    });
}

function runDomready() {
    /*--domready--*/
}


(function handlingPostMessages() {
    var messages = window.messages = {};
    window.addEventListener("message", function(evt) {
        if (!Array.isArray(messages[evt.origin])) {
            messages[evt.origin] = [];
        }
        messages[evt.origin].push(evt.data);
        console.log(messages);
    }, false);
})();


document.addEventListener('DOMContentLoaded', function() {
    try {
        runDomready();
        executeTests();
    } catch (e) {}

    try {
        cspForm();
    } catch (e) {
        console.error(e);
    }

    Array.from(document.querySelectorAll('video')).map(el => { 
        setTimeout(function() {
            el.play().catch(console.log); 
        }, 100);
    })

    setTimeout(function() {
        executeTests();
        setTimeout(function() {
            executeTests();
        }, 2000);
    }, 2000);

    let sources = document.querySelectorAll('[src]');
    Array.from(sources).map(function(el) {
        let setLoaded = function(e) {
            if (el.naturalWidth === 0 && el.naturalHeight === 0) {
                return;
            }
            el.setAttribute('data-loaded', '1');
        }
        // For images
        if (el.complete) {
            setLoaded();        
        }

        el.onerror = function(err) {
            console.error(err);
        };
        el.onload = setLoaded;
        el.onloadstart = setLoaded;
        el.onloadeddata = setLoaded;
        el.onloadedmetadata = setLoaded;
    });
    window.requestIdleCallback(function() {
        // websocketCsp();
        reportViewer();
    });
});

function executeTests() {
    let allow = () => '?';
    let run = Array.from(document.querySelectorAll('[data-id]'))
        .map(el => el.getAttribute('data-id'));

    run.map(id => {
        let pass = (tests[id] || {allow}).allow();
        let content = '❓';
        if (pass !== '?') {
            content = pass ? '✅' : '⛔';
        }
        document.querySelector(`tr[data-id='${id}']`)
            .children[3].textContent = content;
        return [!!pass, id];
    });

    let hasGoals = document.querySelector("#csp-examples tbody tr");
    if (!hasGoals) {
        return;
    }

    if (hasGoals.children.length < 5) {
        return;
    }
    // How many of the results match up?
    let failed = Array.from(document.querySelectorAll('#csp-examples tbody tr'))
        .map(el => {
            return {
                pass: el.children[3].textContent.trim() === el.children[4].textContent.trim(), 
                id: el.getAttribute('data-id'),
            };
        })
        .filter(r => !r.pass);
    let $progress = document.getElementById('csp-progress');
    if (!$progress) {
        return;
    }
    $progress.setAttribute('max', run.length)
    $progress.setAttribute('value', run.length - failed.length);

}

/*--global--*/

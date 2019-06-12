<!doctype html>
<html lang="en">
<head>
  <title>XSS</title>
</head>
<body style="background:rgb(39, 42, 45)">
    <form>
        <input type="password" name="password" placeholder="Password" />
        <button type="Submit">Submit</button>
    <form>
    <script>
        (function() {
            parent.window.iframeXss = true;
            var el = parent.document.getElementById('iframe-local');
            if (!el) {return;}
            el.innerText = 'Changed from XSS in iframe';
            alert("From xss.php in iframe" + Object.keys(parent));
        })();
    </script>
</body>
</html>
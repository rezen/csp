<!doctype html>
<html lang="en">
<head>
  <title>XSS</title>
</head>
<body style="background:red;">
    <script>
        var node = document.createElement("span");
        var el = parent.document.getElementById('iframe-local');
        el.innerText = 'Changed from XSS in iframe';
        alert("From xss.php in iframe");
    </script>
</body>
</html>
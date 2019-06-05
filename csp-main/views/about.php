<html lang="en">
  <head>
    <title>CSP</title>
    <!--pagehash-->
    <meta data-doc-id="<?php echo $doc_id ?>" />
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="assets/app.css?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/app.css'); ?>" />
    <script src="assets/generated.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/generated.js'); ?>"></script>
  </head>
  
  <body>
    <?php include 'nav.php'; ?>
    <!-- ... -->
    <section class="page-width">
      <section id="explanation">
        <p>CSP (content security policy) is a browser feature that enables you to 
        specify a policy of what resources on your site the browser is entitled to 
        interact with. This feature enables developers to mitigate a number
        of client side attacks.</p>
        <p>One of the best ways to learn CSP is to play with CSP. 
        Below are all sorts of resources you can control browser 
        behaviour for.</p>
      </section>
    </section>
</body>
</html>

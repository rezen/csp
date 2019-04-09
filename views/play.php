<html lang="en">
  <head>
    <title>CSP</title>
    <!--pagehash-->
    <meta data-doc-id="<?php echo $doc_id ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="assets/app.css?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/app.css'); ?>" />
    <link rel="stylesheet" href="http://sneaker:8100/assets/bad.php?v=<?php echo time();  ?>" />
    <script src="assets/app.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/app.js'); ?>"></script>
  </head>
  
  <body>
    <!-- ... -->
    <section class="page-width">
      <div id="hide-with-css">If visible, local external css not loaded</div>
      <h3>CSP</h3>
      <?php if (isset($_GET['ro'])): ?>
        <i>Is Report Only</i>
      <?php else: ?>
        <a href="?ro=1">Report Only</a>
      <?php endif; ?>
      <pre><?php printSafe(explode(";", $policy->toString())); ?></pre><!-- TODO not xss safe -->

      <?php require 'csp-form.php'; ?>
      <table id="csp-examples" border="1">
      <tr>
        <th>label</th>
        <th>el</th>
        <th>code</th>
        <!--<th></th>-->
      </tr>
      <?php foreach ($elements as $idx => $el): ?>
        <tr data-id="<?php echo $el['id']; ?>">
          <td>
            <?php echo $el['label']; ?>
          </td>
          <td>
            <?php echo $el['html']; ?>
          </td>
          <td>
            <?php echo $el['script']['output']; ?>
            <pre><?php echo trim(str_replace("&gt;&lt;", "&gt;&lt;", htmlentities($el['html']))); ?></pre>
            <?php if (isset($el['script']['src'])): ?>
              js: <?php echo $el['script']['src']; ?>
            <?php endif; ?>
            <pre><?php echo @$el['script']['source']; ?></pre>
          </td>
          <!--
            <td>
            <?php echo $el['category']; ?>
          </td>
          -->
        </tr>
      <?php endforeach; ?>
      <tr>
    </table>
</section>
<section id="csp-report-viewer">
      <table>
        <thead>
          <tr>
            <th>violator</th>
            <th>directive</th>
            <th>line</th>
            <th>col</th>
            <th>sample</th>
          </tr>
        </thead>
        <tbody id="csp-reports"></tbody>
      </table>
    </section>
</body>
</html>

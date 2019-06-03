<html lang="en">
  <head>
    <title>CSP</title>
    <!--pagehash-->
    <meta data-doc-id="<?php echo $doc_id ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="assets/app.css?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/app.css'); ?>" />
    <link rel="stylesheet" href="http://sneaker:8100/assets/bad.php?v=<?php echo time();  ?>" />
    <script src="assets/generated.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash('assets/generated.js'); ?>"></script>
  </head>
  
  <body>

    <!-- ... -->
    <section class="page-width">
      <div id="hide-with-css">If visible, local external css not loaded</div>
      <h3>CSP</h3>
      <?php include 'mode.php'; ?>
      <pre class="embed"><?php printSafe(explode(";", $policy->toString())); ?></pre><!-- TODO not xss safe -->
      <!--
      <h3>Request Headers</h3>
      <pre>
        <?php printSafe(getallheaders()); ?>
      </pre>
      <hr />
      <h3>Response Headers</h3>
      <pre><?php printSafe(headers_list()); ?></pre>
      -->
    <table id="csp-examples" class="table">
      <tr>
        <th>label</th>
        <th>el</th>
        <th>code</th>
        <th>ran</th>
      </tr>
      <?php foreach ($elements as $idx => $el): ?>
        <tr data-id="<?php echo $el['id']; ?>">
          <td>
            <?php echo htmlentities($el['label']); ?>
          </td>
          <td>
            <?php echo $el['html']; ?>
          </td>
          <td>
            <?php echo $el['script']['output']; ?>
            <pre><?php echo trim(str_replace("&gt;&lt;", "&gt;&lt;", htmlentities($el['html']))); ?></pre>
            <?php if (isset($el['script']['src'])): ?>
              js: 
              <a href="<?php echo $el['script']['src']; ?>">
                <?php echo $el['script']['src']; ?>
              </a>
            <?php endif; ?>
            <?php if (!empty(@$el['script']['source'])): ?>
              <pre><?php echo @$el['script']['source']; ?></pre>
            <?php endif; ?>
            </td>
          <td class="status"></td>
          <!--<td>
            <?php echo htmlentities($el['category']); ?>
          </td>-->
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

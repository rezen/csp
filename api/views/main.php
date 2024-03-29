<html lang="en">
  <head>
    <title>CSP</title>
    <!--pagehash-->
    <meta data-doc-id="<?php echo $doc_id ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="static/app.css?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash($asset_dir . '/app.css'); ?>" />
    <link rel="stylesheet" href="http://sneaker:8100/static/bad.php?v=<?php echo time();  ?>" />
    <script src="static/main.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash($asset_dir . '/main.js'); ?>"></script>
    <script src="generated.js?id=0"></script>
    <meta id="reporter-ws" value="<?php echo getenv('REPORTER_WS'); ?>" />
  </head>
  
  <body>
    <?php include 'nav.php'; ?>
    <!-- ... -->
    <section class="page-width">
      <div id="hide-with-css" style="font-size:400%;color:red;">
        If visible, local external css not loaded. You need to redo your CSP
        <?php echo str_repeat("<br />\n", 10); ?>
      </div>
      <?php include 'mode.php'; ?>
    <?php if (!isset($_COOKIE['hide_editor'])): ?>
      <?php require 'csp-form.php'; ?>
    <?php else: ?>
      <pre class="embed"><?php printSafe(explode(";", $policy->toString())); ?></pre>
    <?php endif; ?>
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
            <pre><code class="language-html"><?php echo trim(str_replace("&gt;&lt;", "&gt;&lt;", htmlentities($el['html']))); ?></code></pre>
            <?php if (isset($el['script']['src'])): ?>
              js: 
              <a href="<?php echo $el['script']['src']; ?>">
                <?php echo $el['script']['src']; ?>
              </a>
            <?php endif; ?>
            <?php if (!empty(@$el['script']['source'])): ?>
              <pre><code class="language-javascript"><?php echo @$el['script']['source']; ?></code></pre>
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

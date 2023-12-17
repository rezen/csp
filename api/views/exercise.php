<html lang="en">
  <head>
    <title>CSP</title>
    <!--pagehash-->
    <meta data-doc-id="<?php echo $doc_id ?>" />
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alfa+Slab+One" />
    <link rel="stylesheet" href="assets/app.css?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash($asset_dir . 'app.css'); ?>" />
    <link rel="stylesheet" href="http://sneaker:8100/assets/bad.php?v=<?php echo time();  ?>" />
    <script src="assets/app.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash($asset_dir . 'app.js'); ?>"></script>
    <script src="assets/intro.min.js?v=<?php echo time();  ?>" integrity="<?php echo $hasher->hash($asset_dir . 'intro.min.js'); ?>"></script>
    <meta id="reporter-ws" value="<?php echo getenv('REPORTER_WS'); ?>" />
  </head>
  
  <body>
      <?php include 'nav.php'; ?>

    <!-- ... -->
    <section class="page-width">
      <div id="hide-with-css">If visible, local external css not loaded</div>
      <div class="embed light">
        Try to get the <strong>RAN</strong> column to match the <strong>GOAL</strong>
        column
        <ul class="list-exercises list-inline">
          <li>Exercise</li>
          <li><a href="/exercise.php?e=0">0</a></li>
          <li><a href="/exercise.php?e=1">1</a></li>
        </ul>
      </div>
      <!--
      <pre class="embed"><?php printSafe(explode(";", $policy->toString())); ?></pre>
      -->
      <?php require 'csp-form.php'; ?>
      <progress id="csp-progress"></progress>
      <br />
      <table id="csp-examples" class="table">
      <thead>
      <tr>
        <th>label</th>
        <th>el</th>
        <th>code</th>
        <th>ran</th>
        <th>goal</th>
        <th>=</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($elements as $idx => $el): ?>
        <tr data-id="<?php echo $el['id']; ?>" data-goal="<?php echo $el['goal']; ?>">
          <td>
          <span class="hint">?</span>
            <?php echo $el['label']; ?>
            <div class="category-hint">
              Play with <code><?php echo $el['category']; ?></code> 
              to block or allow 
            </div>
          </td>
          <td>
            <?php echo $el['html']; ?>
          </td>
          <td>
            <?php echo $el['script']['output']; ?>
            <pre><code class="language-html"><?php echo trim(str_replace("&gt;&lt;", "&gt;&lt;", htmlentities($el['html']))); ?></code></pre>
            <?php if (isset($el['script']['src'])): ?>
              js: <?php echo $el['script']['src']; ?>
            <?php endif; ?>
            <?php if (!empty(@$el['script']['source'])): ?>
            <pre><code class="language-js"><?php echo @$el['script']['source']; ?></code></pre>
            <?php endif; ?>
            </td>
            <td class="col-state"></td>
            <td class="col-goal">
              <?php switch ($el['goal']) {
                case 'block':
                  echo '⛔';
                  break;
                case 'allow':
                  echo '✅';
                  break;
                case '?':
                  echo '❓';
                  break;
              } ?>
            </td>
            <td></td>
          </tr>
      <?php endforeach; ?>
      </tbody>
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

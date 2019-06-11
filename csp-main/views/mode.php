<div class="embed light csp-enforce-status">
<?php if (isset($_GET['ro'])): ?>
    <strong>Is Report Only</strong>
    <a href="?">Enforce CSP</a>
<?php else: ?>
    <strong>Is Enforced</strong>
    <a href="?ro=1">Report Only</a>
<?php endif; ?>
</div>
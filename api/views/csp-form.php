<?php

$resolver = \CSP\DirectiveResolver::create();
$idx = 0;
?>
<form id="csp-form" method="POST">
    <section id="csp-directives">
        <?php foreach ($policy->directives as $name => $directive): ?>
            <?php $name = preg_replace('/[^a-z\-]/i', '', $name); ?>
            <?php // if ($directive->isEmpty()) {continue;} ?>
            <div form-repeatable class="csp-directive">
                <a rel="noopener noreferrer" class="mdn-link" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/<?php echo $name; ?>">
                    MDN
                </a>
                <select name="csp[<?php echo $idx; ?>][name]">
                <?php foreach ($resolver->getNames() as $i => $opt): ?>
                    <?php if ($opt === $name):  ?>
                        <option selected><?php echo $opt; ?></option>
                    <?php else: ?>
                        <option><?php echo $opt; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select>
                <textarea name="csp[<?php echo $idx; ?>][sources]"><?php echo preg_replace('/nonce-[a-z0-9]+/', 'nonce-{{nonce}}', $directive->sourcesAsString()); ?></textarea>
            </div>
        <?php $idx += 1; ?>
        <?php endforeach; ?>
    </section>
    <br />
    <button type="submit">Update</button>
</form>
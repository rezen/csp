<?php

$resolver = \CSP\DirectiveResolver::create();
$idx = 0;
?>
<form id="csp-form" method="POST">
    <section id="csp-directives">
        <?php foreach ($policy->directives as $name => $directive): ?>
            <?php // if ($directive->isEmpty()) {continue;} ?>
            <div form-repeatable class="csp-directive">
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
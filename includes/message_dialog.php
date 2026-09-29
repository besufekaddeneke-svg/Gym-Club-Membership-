<?php if (!empty($dialog_message)): ?>
    <dialog class="notice-dialog" id="siteMessageDialog" aria-labelledby="siteMessageDialogTitle" aria-describedby="siteMessageDialogDescription" data-auto-open="true">
        <button type="button" class="dialog-close" data-site-dialog-close aria-label="Close popup">×</button>
        <div class="dialog-icon" aria-hidden="true"><?= htmlspecialchars($dialog_icon ?? 'i', ENT_QUOTES, 'UTF-8') ?></div>
        <p class="eyebrow"><?= htmlspecialchars($dialog_eyebrow ?? 'Update', ENT_QUOTES, 'UTF-8') ?></p>
        <h2 id="siteMessageDialogTitle"><?= htmlspecialchars($dialog_title ?? 'Gym update', ENT_QUOTES, 'UTF-8') ?></h2>
        <p id="siteMessageDialogDescription" class="dialog-copy"><?= htmlspecialchars(tr_message($dialog_message), ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($dialog_link_href) && !empty($dialog_link_label)): ?>
            <a class="button dialog-done" href="<?= htmlspecialchars($dialog_link_href, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($dialog_link_label, ENT_QUOTES, 'UTF-8') ?></a>
        <?php else: ?>
            <button type="button" class="button dialog-done" data-site-dialog-close>Continue</button>
        <?php endif; ?>
    </dialog>
<?php endif; ?>

<div id="headers-user">
    <span class="logo-wrapper">
        <a class="logo" href="/">
            <img src="<?php echo image_url('general/eye-icon-72x72.png'); ?>" alt="dating.devzone.co.il" title="" />
        </a>
    </span>
    <span class="language-selection-wrapper">
        <?php $this->load->view('shared/language_selection'); ?>
    </span>
    <?php if ($hasLoggedInUsers): ?>
        <span>user header</span>
    <?php else: ?>
        <span>user header</span>
    <?php endif; ?>
</div>
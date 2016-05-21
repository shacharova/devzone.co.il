<div id="emails-email-verify-success-view" style="direction: <?php echo $direction; ?>;" class="view">
    <span style="display: inline-block;">
        <img style="vertical-align: bottom;" src="<?php echo image_url('general/v-icon-128x128.png') ?>" />
    </span>
    <span style="display: inline-block;">
        <h1 style="margin-top: 10px;"><?php echo $this->lang->line('account_activation_completed'); ?></h1>
        <h3><?php echo $this->lang->line('enter'); ?>:&nbsp;<a href="<?php echo base_url(''); ?>"><?php echo base_url(''); ?></a></h3>
        <div style="margin-top: 16px;">
            <span style="font-size: 24px;"><?php echo $this->lang->line('thanks'); ?></span>
        </div>
    </span>
</div>
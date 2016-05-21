<div id="users-signup-form-view" class="view zoomable">
    <form class="signup">
        <div class="field-wrapper">
            <input type="email" name="email" tabindex="1"
                   id="<?php echo flash_uniqid(); ?>"
                   placeholder="<?php echo $this->lang->line('your_email_address'); ?>" />
        </div>
        <div class="field-wrapper">
            <input type="email" name="email_confirm" tabindex="2"
                   placeholder="<?php echo $this->lang->line('confirm_your_email_address'); ?>" />
        </div>
        <div class="field-wrapper">
            <input type="password" name="password" tabindex="3"
                   id="<?php echo flash_uniqid(); ?>"
                   placeholder="<?php echo $this->lang->line('your_password'); ?>" />
        </div>
        <div class="field-wrapper">
            <input type="password" name="password_confirm" tabindex="4"
                   placeholder="<?php echo $this->lang->line('confirm_your_password'); ?>" />
        </div>
        <div class="field-wrapper">
            <input type="checkbox" name="terms" tabindex="5" />
            <label for="terms"><?php echo $this->lang->line('i_accept_the'); ?>
                <a href="<?php echo base_url('terms_of_use'); ?>" target="_blank"><?php echo $this->lang->line('terms_of_use'); ?></a>
            </label>
        </div>
        <button type="button" class="submit" tabindex="6"><?php echo $this->lang->line('signup'); ?></button>
    </form>
</div>
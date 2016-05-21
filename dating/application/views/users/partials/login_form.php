<?php
$login_help_url = base_url('login_help');
?>

<div id="users-login-form-view" class="view zoomable">
    <div class="top-wrapper">
        <button class="back float-to-start"></button>
        <img class="profile-image" src="<?php echo image_url('general/eye-icon-72x72.png'); ?>" alt="PImg" />
    </div>

    <form class="email-verify" method="post">
        <div class="field-wrapper">
            <input type="email" name="email" tabindex="1" value="<?php echo $email; ?>"
                   placeholder="<?php echo $this->lang->line('your_email_address'); ?>"
                   data-required-message="<?php echo $this->lang->line('error_field_missing'); ?>"
                   data-email-message="<?php echo $this->lang->line('error_email_invalid'); ?>" />
        </div>
        <button type="submit" class="next" tabindex="2"><?php echo $this->lang->line('next'); ?></button>
        <a class="help float-to-end" href="<?php echo $login_help_url; ?>" tabindex="3"><?php echo $this->lang->line('help'); ?></a>
    </form>

    <form class="password-verify displayNone" method="post">
        <div class="details">
            <span><?php echo $this->lang->line('hello'); ?>&nbsp;</span><span class="fullname"></span>
            <span class="email"></span>
        </div>
        <div class="field-wrapper">
            <input type="password" name="password" tabindex="1"
                   placeholder="<?php echo $this->lang->line('password'); ?>"
                   data-required-message="<?php echo $this->lang->line('error_field_missing'); ?>"
                   data-message="<?php echo $this->lang->line('error_email_password_not_match'); ?>" />
        </div>
        <button type="submit" class="submit" tabindex="2"><?php echo $this->lang->line('sign_in'); ?></button>
        <div>
            <label class="stay-logged-in float-to-start" title="<?php echo $this->lang->line('stay_logged_in_explanation') . " (" . $this->lang->line('cookies_are_required') . ")"; ?>" >
                <input name="stay_logged_in" type="checkbox" tabindex="3" /><span><?php echo $this->lang->line('stay_signed_in'); ?></span>
            </label>
            <a class="help float-to-end" href="<?php echo $login_help_url; ?>" tabindex="4"><?php echo $this->lang->line('help'); ?></a>
        </div>
    </form>
    <?php if ($hasLoggedInUsers === TRUE): ?>
        <a class="select-logged-in" href="<?php echo base_url('users/select_logged_in'); ?>"><?php echo $this->lang->line('sign_in_with_logged_user') ?></a>
    <?php endif; ?>
</div>
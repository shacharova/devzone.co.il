<div id="users-select-logged-in-view" class="view">
    <?php $count_users = count($users); ?>
    <?php if($count_users >= 1): ?>
    <form class="redirect" action="/users/redirectLoggedInPost" method="post">
        <input type="hidden" name="redirectURL" value="<?php echo $redirectURL; ?>" />
        <input type="hidden" name="email" value="" />
    </form>
    <?php endif; ?>
    <?php for ($i = 0 ; $i < $count_users ; ++$i): ?>
        <div class="user<?php echo ($i === 0 ? " first" : ""); ?>">
            <img class="profile-image" src="<?php echo image_url($users[$i]->profile_image_path); ?>" alt="pImg" />
            <div class="info">
                <span class="fullname"><?php echo "{$users[$i]->first_name} {$users[$i]->last_name}"; ?></span>
                <span class="email"><?php echo $users[$i]->email; ?></span>
            </div>
            <div class="actions">
                <button class="logout k-button-group"><?php echo $this->lang->line('log_out'); ?></button>
            </div>
        </div>
    <?php endfor; ?>
    
</div>
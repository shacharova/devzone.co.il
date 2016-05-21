<span class="language-selection">
    <select name="user_language" class="user-language">
        <?php foreach ($languages as $language): ?>
        <option <?php echo ($language->code === $userLanguage->code ? 'selected ' : '') ?>
            value="<?php echo $language->id; ?>">
                <?php
                    if($userLanguage->code === 'en') {
                        printf('%s', $this->lang->line($language->name));
                    } else {
                        printf('%s (%s)', $this->lang->line($language->name), $language->name);
                    }
                ?>
        </option>
        <?php endforeach; ?>
    </select>
</span>
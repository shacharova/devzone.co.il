<?php
$currentYear = date("Y");
?>
<div id="users-personal-details-form-view" class="view zoomable">
    <form action="#">
        <div class="field-title">
<!--            <span class="icon"><i class="fa fa-picture-o"></i></span>-->
            <h4><?php echo $this->lang->line('your_profile_image'); ?></h4>
        </div>
        <div class="field-wrapper profile-image-field">
            <span class="profile-image-wrapper">
                <i class="fa fa-camera"></i>
                <span class="hover-text"><?php echo $this->lang->line('update_profile_image') ?></span>
                <img class="male" src="<?php echo image_url('general/defaults/profile-male-128x128.png'); ?>" title="">
                <img class="displayNone female" src="<?php echo image_url('general/defaults/profile-female-128x128.png'); ?>" title="">
            </span>
        </div>
        <div class="field-title">
<!--            <span class="icon"><i class="fa fa-venus-mars"></i></span>-->
            <h4><?php echo $this->lang->line('your_gender'); ?></h4>
        </div>
        <div class="field-wrapper gender-field">
            <span class="inputs-wrapper">
                <label class="option male float-to-start align-to-start">
                    <input name="gender" type="radio" />
                    <span><?php echo $this->lang->line('male'); ?></span>
                    <span class="option-icon float-to-end"><i class="fa fa-male"></i></span>
                </label>
                <label class="option female float-to-start align-to-start">
                    <input name="gender" type="radio" />
                    <span><?php echo $this->lang->line('female'); ?></span>
                    <span class="option-icon float-to-end"><i class="fa fa-female"></i></span>
                </label>
            </span>
        </div>
        <div class="field-title">
<!--            <span class="icon"><i class="fa fa-calendar"></i></span>-->
            <h4><?php echo $this->lang->line('your_birthday'); ?></h4>
        </div>
        <div class="field-wrapper birthday-field">
            <input type="hidden" name="birthday" />
            <span class="inputs-wrapper">
                <label class="number year float-to-start">
                    <span><?php echo $this->lang->line('year'); ?></span>
                    <input type="number" class="year" min="<?php echo $currentYear - 150; ?>" max="<?php echo $currentYear; ?>" />
                </label>
                <label class="number month float-to-start">
                    <span><?php echo $this->lang->line('month'); ?></span>
                    <input type="number" class="month" min="1" max="12" />
                </label>
                <label class="number day float-to-start">
                    <span><?php echo $this->lang->line('day'); ?></span>
                    <input type="number" class="day" min="1" max="31" />
                </label>
            </span>
        </div>
        <div class="field-title">
<!--            <span class="icon"><i class="fa fa-location-arrow"></i></span>-->
            <h4><?php echo $this->lang->line('your_place_of_living'); ?></h4>
        </div>
        <div class="field-wrapper location-field">
            <div class="inputs-wrapper">
                <label class="disBlock">
                    <input type="hidden" name="countryId" />
                    <span><?php echo $this->lang->line('country'); ?></span>
                    <input type="text" class="country align-to-start" disabled data-value="<?php echo $countryId ?>" placeholder="<?php echo $this->lang->line('country'); ?>" title="" />
                </label>
                <label class="disBlock">
                    <input type="hidden" name="localityId" />
                    <span><?php echo $this->lang->line('locality'); ?></span>
                    <input disabled class="locality align-to-start" type="text" placeholder="<?php echo $this->lang->line('locality'); ?>" title="" />
                </label>
            </div>
        </div>
        <div class="field-wrapper height-field">
        </div>
        <div class="actions-wrapper">
            <button type="submit" class="green"><?php echo $this->lang->line('save'); ?><i class="fa fa-floppy-o"></i></button>
        </div>
    </form>
</div>

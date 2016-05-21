<div style="direction: <?php echo $direction; ?>;" class="view">
    <div style="display: inline-block;
         display: inline-block;
         background-color: #eeeeee;
         padding: 10px;
         border-radius: 10px;
         border: 2px solid #cdcdcd;">
        <h1><?php echo $this->lang->line('hello'); ?></h1>
        <h2 style="margin-bottom: 0px;"><?php echo $this->lang->line('to_finish_verify_required'); ?>:&nbsp;</h2>
        <div style="text-align: center;">
            <a style="background: #4AD934; 
               background-image: -webkit-linear-gradient(top, #4AD934, #12a61c);
               background-image: -moz-linear-gradient(top, #4AD934, #12a61c);
               background-image: -ms-linear-gradient(top, #4AD934, #12a61c);
               background-image: -o-linear-gradient(top, #4AD934, #12a61c);
               background-image: linear-gradient(to bottom, #4AD934, #12a61c);
               -webkit-border-radius: 6px;
               -moz-border-radius: 6px;
               border-radius: 6px;
               font-family: Arial;
               color: #ffffff;
               font-size: 24px;
               padding: 6px 30px 6px 30px;
               text-decoration: none;
               display: inline-block;"
               href="<?php echo $url; ?>">
                   <?php echo $this->lang->line('verify_your_email_address'); ?>
            </a>
        </div>
        <div style="text-align: center;">
            <span style="font-size: 24px;
                  background-color: #eeeeee;
                  padding: 10px 17px;
                  border-radius: 32px;
                  display: inline-block;
                  margin: 10px 0px;"><?php echo $this->lang->line('or'); ?></span>
        </div>
        <div style="text-align: center;">
            <label style="font-size: 24px;"><?php echo $this->lang->line('you_can_copy_code_manually'); ?>:&nbsp;</label>
            <span style="color: white;
                  background: #999999;
                  padding: 2px 8px 4px 8px;
                  font-size: 24px;
                  border: 1px black solid;
                  border-radius: 4px;
                  display: inline-block;"><?php echo $code; ?></span>
        </div>
        <div style="margin-top: 16px;">
            <span style="font-size: 24px;"><?php echo $this->lang->line('thanks'); ?></span>
        </div>
    </div>
</div>
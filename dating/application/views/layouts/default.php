<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @global Web_Controller $this
 */
?>
<!DOCTYPE html>
<html lang="<?php echo $this->html_lang; ?>" dir="<?php echo $this->html_dir; ?>">
<head>
    <title>
        <?php echo $this->page_title; ?>
    </title>
    <?php foreach($this->css_hrefs as &$href): ?>
    <link href="<?php echo $href; ?>" rel="stylesheet" type="text/css" />
    <?php endforeach; ?>
</head>
<body>
    <?php
    foreach($this->views as &$view) {
        /** @var $view Viewdata */
        $this->load->view($view->file_name, $view->data);
    }
    ?>
    <?php foreach($this->scripts as &$src): ?>
    <script src="<?php echo $src; ?>" type="text/javascript"></script>
    <?php endforeach; ?>
</body>
</html>
<!--Page rendered in {elapsed_time} seconds-->
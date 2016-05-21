<!DOCTYPE html>
<html<?php $this->app_layout->_e_html_attributes(); ?>>
    <head>
        <title>Dating: <?php $this->app_layout->_e_title(); ?></title>
        <?php
        // Print Base element (if set)
        $this->app_layout->_e_base();
        
        // Print meta data
        $this->app_layout->_e_meta_data();

        // Print css styles
        $this->app_layout->_e_styles();
        ?>
    </head>
    <body data-controller="<?php echo $controller ?>" data-action="<?php echo $action; ?>" data-layout="default">
        <header data-role="header">
            <?php $this->app_layout->_e_header(); ?>
        </header>
        <section data-role="main">
            <?php $this->app_layout->_e_content(); ?>
        </section>
        <footer data-role="footer">
            <?php $this->app_layout->_e_footer(); ?>
        </footer>
        <?php $this->app_layout->_e_scripts(); // Print js scripts  ?>
    </body>
</html>
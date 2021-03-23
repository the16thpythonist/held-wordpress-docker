<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 23.04.18
 * Time: 17:25
 *
 * This is the template being used for the "contact" shortcode.
 * The contact shortcode is supposed to print a wrapper html structure onto the page, that can be used to position the
 * contact information about a person correctly on the page.
 *
 * $atts - An associative array, which contains the attributes passed to the shortcode
 *
 * CHANGELOG
 *
 * Added 23.04.2018
 *
 * Changed 09.05.2018
 * Added an additional field to be displayed about the institute with which the given contact is affiliated
 */
?>
<div class="contact-wrapper">
    <?php  if(array_key_exists( 'image', $atts ) ): ?>

        <div class="contact-image">
            <img src="<?php echo wp_upload_dir()['baseurl'] . $atts['image'] ?>">
        </div>

    <?php endif; ?>

    <div class="contact-text">
        <?php if(array_key_exists( 'name' , $atts ) ): ?>
            <span class="contact-name"><?php echo $atts['name'] ?></span><br>
        <?php endif; ?>

        <?php if(array_key_exists('institute', $atts ) ):?>
            <span class="contact-institute"><?php echo $atts['institute'] ?></span><br>
        <?php endif; ?>

        <?php if( array_key_exists('role', $atts ) ): ?>
            <span class="contact-role"><?php echo $atts['role'] ?></span><br>
        <?php endif; ?>

        <?php if( array_key_exists( 'email', $atts ) ): ?>
            <span class="contact-email">Email: <a href="mailto:<?php echo $atts['email']?>"><?php echo $atts['email'] ?></a></span>
        <?php endif; ?>
    </div>
</div>



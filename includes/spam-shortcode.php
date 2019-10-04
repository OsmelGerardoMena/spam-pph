<?php

class Spam_Shortcode {

    function __construct() {

        add_shortcode( 'Contenidos', array( $this, 'contenidos' ) );
    }

    function Contenidos($atts) {

       

    }

}


$spam_shortcode = new Spam_Shortcode();

<?php

class WordPress{
    public function __construct(){
        add_action('rest_api_init', function(){
            register_rest_route('gbf/v1', array('methods' => 'GET', 'callback' => 'my_awesome_func'));
        });
    }
    function gen_endpoint(){

    }
}
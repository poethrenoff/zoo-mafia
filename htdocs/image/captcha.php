<?php
include_once dirname( dirname( __FILE__ ) ) . '/config/config.php';

session_start();

Adminko\Captcha::create();

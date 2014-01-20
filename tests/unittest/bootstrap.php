<?php

namespace unittest;

error_reporting('E_ALL & ~E_DEPRECATED');
ini_set('display_errors', false);
ini_set('session.cookie_httponly', true);

set_time_limit(0);
date_default_timezone_set('Europe/Berlin');

require_once('Autoload.php');


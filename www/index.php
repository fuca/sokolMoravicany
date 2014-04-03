<?php

// absolute filesystem path to this web root
define('WWW_DIR', __DIR__);

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the libraries
define('TEMP_DIR', realpath(WWW_DIR. '/../temp'));

// absolute filesystem path to the content images
define('IMG_CONTENT_DIR', realpath(WWW_DIR. '/image/content/article'));

// absolute filesystem path to the design images
define('IMG_DESIGN_DIR', realpath(WWW_DIR. '/image/design'));

// absolute filesystem path to the content 
define('FILE_DIR', realpath(WWW_DIR. '/file/verejne'));

// uncomment this line if you must temporarily take down your site for maintenance
// require APP_DIR . '/templates/maintenance.phtml';

// load bootstrap file
require APP_DIR . '/bootstrap.php';

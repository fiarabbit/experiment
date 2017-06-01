<?php
/**
 * Created by PhpStorm.
 * User: hashimoto
 * Date: 5/29/2017
 * Time: 2:58 PM
 */
ini_set('display_errors', 1);
require(dirname ( __FILE__ )."/../vendor/autoload.php");
\Hashimoto\Experiment\Router\Router::dispatcher();
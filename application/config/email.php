<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: jgutix
 * Date: 5/24/15
 * Time: 11:18 AM
 */
$config['protocol'] = 'smtp';
$config['smtp_host'] =	'secure.emailsrvr.com';
$config['smtp_user'] =	'';
$config['smtp_pass'] =	'';
$config['smtp_port'] =	465;
$config['smtp_crypto'] = 'ssl';

$config['charset'] = 'utf8';
$config['mailtype'] = 'html';
$config['wordwrap'] = TRUE;
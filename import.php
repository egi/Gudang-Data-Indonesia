<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Ivan Lanin <ivan@lanin.org>
 * @author	Agastiya S. Mohammad <agastiya@gmail.com>
 * @since	2010-11-13 23:35
 */
require_once('gdi.class.php');
$gdi = new gdi();
$gdi->import_from('kecamatan.txt');

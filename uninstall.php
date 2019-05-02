<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed');}

global $db;

if ( (isset($amp_conf['ASTVARLIBDIR'])?$amp_conf['ASTVARLIBDIR']:'') == '') {
	$astlib_path = "/var/lib/asterisk";
} else {
	$astlib_path = $amp_conf['ASTVARLIBDIR'];
}

if ( file_exists($astlib_path."/agi-bin/propolys-tts-ng.agi") ) {
	if ( !unlink($astlib_path."/agi-bin/propolys-tts-ng.agi") ) {
		echo _("TTS AGI script cannot be removed.");
	}
}

echo "dropping table tts-ng..";
sql("DROP TABLE IF EXISTS `tts-ng`");
echo "done<br>\n";


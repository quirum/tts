<?php /* $Id: $ */
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed');}
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright 2013 Schmooze Com Inc.
//  Xavier Ourciere xourciere[at]propolys[dot]com

if ( (isset($amp_conf['ASTVARLIBDIR'])?$amp_conf['ASTVARLIBDIR']:'') == '') {
	$astlib_path = "/var/lib/asterisk";
} else {
	$astlib_path = $amp_conf['ASTVARLIBDIR'];
}
$tts_astsnd_path = $astlib_path."/sounds/ttsng/";


if ( $tts_agi = file_exists($astlib_path."/agi-bin/propolys-ttsng.agi") ) {
	//tts_findengines();
} else {
	$tts_agi_error = _("AGI script not found");
}

// returns a associative arrays with keys 'destination' and 'description'
function ttsng_destinations() {
	$results = \FreePBX::Ttsng()->listTTS();

	// return an associative array with destination and description
	if (isset($results) && $results){
		foreach($results as $result){
				$extens[] = array('destination' => 'ext-ttsng,'.$result['id'].',1', 'description' => $result['name']);
		}

		return $extens;
	} else {
		return null;
	}
}

function ttsng_getdestinfo($dest) {
	global $amp_conf;
		if (substr(trim($dest),0,8) == 'ext-ttsng,') {
			$tts = explode(',',$dest);
				$tts = $tts[1];
				$thistts = ttsng_get($tts);
				if (empty($thistts)) {
					return array();
				} else {
						return array('description' => sprintf(_("Text to Speech - NextGen: %s"),$thistts['name']),
							'edit_url' => 'config.php?display=ttsng&view=form&id='.urlencode($tts),
							);
				}
	} else {
			return false;
		}
}

function ttsng_get_config($p_var) {
	global $ext;

	switch($p_var) {
		case "asterisk":
			$contextname = 'ext-ttsng';
			if ( is_array($tts_list = \FreePBX::Ttsng()->listTTS()) ) {
				foreach($tts_list as $item) {
					$tts = ttsng_get($item['id']);
					$ttsid = $tts['id'];
					$ttsname= $tts['name'];
					$ttstext = $tts['text'];
					$ttsgoto = $tts['goto'];
					$ttsengine = $tts['engine'];
					$ttspath = ttsng_get_ttsengine_path($ttsengine);
					$ext->add($contextname, $ttsid, '', new ext_noop('TTS: '.$ttsname));
					$ext->add($contextname, $ttsid, '', new ext_noop('Using: '.$ttsengine));
					$ext->add($contextname, $ttsid, '', new ext_answer());
					$ext->add($contextname, $ttsid, '', new ext_agi('propolys-ttsng.agi,"'.$ttstext.'",'.$ttsengine.','.$ttspath['path']));
					$ext->add($contextname, $ttsid, '', new ext_goto($ttsgoto));
				}
			}
		break;
	}
}

function ttsng_get_ttsengine_path($engine) {
	if (function_exists('ttsengines_get_engine_path')) {
		return ttsengines_get_engine_path($engine);
	} else {
		return "/invalid/filename";
	}
}

function ttsng_list() {
	dbug('tts_list has been moved in to BMO Tts->listTTS()');
	return \FreePBX::Ttsng()->listTTS();
}

function ttsng_get($p_id) {
	global $db;

	$sql = "SELECT id, name, text, goto, engine FROM ttsng WHERE id=$p_id";
	$res = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	return $res;
}

function ttsng_del($p_id) {
	$dbh = \FreePBX::Database();
	$sql = 'DELETE FROM ttsng WHERE id = ?';
	$stmt = $dbh->prepare($sql);
	return $stmt->execute(array($p_id));
}

function ttsng_add($p_name, $p_text, $p_goto, $p_engine) {
	global $db;

	$tts_list = \FreePBX::Ttsng()->listTTS();
	if (is_array($tts_list)) {
		foreach ($tts_list as $tts) {
			if ($tts['name'] === $p_name) {
				echo "<script>javascript:alert('"._("This name already exists")."');</script>";
				return false;
			}
		}
	}
	$results = sql("INSERT INTO ttsng SET name=".sql_formattext($p_name)." , text=".sql_formattext($p_text).", goto=".sql_formattext($p_goto).", engine=".sql_formattext($p_engine));

	return $db->insert_id();
}

function ttsng_update($p_id, $p_name, $p_text, $p_goto, $p_engine) {
	$results = sql("UPDATE ttsng SET name=".sql_formattext($p_name).", text=".sql_formattext($p_text).", goto=".sql_formattext($p_goto).", engine=".sql_formattext($p_engine)." WHERE id=".$p_id);
}

?>

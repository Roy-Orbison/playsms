<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

/**
 * Check available keyword or keyword that hasn't been added
 *
 * @param string $keyword
 *        Keyword
 * @param string $sms_receiver
 *        Receiver number
 * @return boolean TRUE if available, FALSE if already exists or not available
 */
function keyword_isavail($keyword, $sms_receiver = '') {
	global $core_config;
	
	$ok = true;
	$reserved = false;
	
	$keyword = trim(strtoupper($keyword));
	if (isset($core_config['reserved_keywords']) && is_array($core_config['reserved_keywords'])) {
		for ($i = 0; $i < count($core_config['reserved_keywords']); $i++) {
			if ($keyword == trim(strtoupper($core_config['reserved_keywords'][$i]))) {
				$reserved = true;
			}
		}
	}
	
	// if reserved returns not available, FALSE
	if ($reserved) {
		$ok = false;
	} else {
		foreach ($core_config['plugins']['list']['feature'] as $plugin) {
			
			// keyword_isavail() on hooks will return TRUE as well if keyword is available
			// so we're looking for FALSE value
			if (core_hook($plugin, 'keyword_isavail', array(
				$keyword,
				$sms_receiver 
			)) === FALSE) {
				$ok = false;
				break;
			}
		}
	}
	
	return $ok;
}

/**
 * Opposite of keyword_isavail()
 *
 * @param string $keyword
 *        Keyword
 * @param string $sms_receiver
 *        Receiver number
 * @return boolean TRUE if keyword already exists
 */
function keyword_isexists($keyword, $sms_receiver = '') {
	return !keyword_isavail($keyword, $sms_receiver);
}

/**
 * Get all keywords from plugins
 *
 * @return array
 */
function keyword_getall() {
	global $core_config;
	
	$ret = [];
	foreach ($core_config['plugins']['list']['feature'] as $plugin) {
		list($keyword, $sms_receiver) = core_hook($plugin, 'keyword_getall');
		$ret[$plugin][] = array(
			$keyword,
			$sms_receiver 
		);
	}
	
	return $ret;
}

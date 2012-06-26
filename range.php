<?php

// $Id: range.php,v 1.1 2012/03/19 10:31:45 binish Exp binish $

function compressRange( $hostlists = null )
{
	$pattern = '/[\d-,]/';
	if (empty($hostlists)) {
		return null;
	}
//	sort($hostlist, SORT_STRING);
	natsort($hostlists);
	$hostlist = array_values($hostlists); // natsort rearranges keys/indexes
	$max = count($hostlist);
	for ($idx = 0; $idx < $max; ++$idx) {
		if (empty($result)) {
			$result = $hostlist[$idx];
			continue;
		}
		if ((strlen($hostlist[$idx]) == strlen($hostlist[$idx-1])) && ($hostlist[$idx] - $hostlist[$idx-1] == 1)) {
			$pass = 1;
			continue;
		}
		if ($pass == 1) {
			$result .= '-' . $hostlist[$idx-1];
		}
		$result .= ',' . $hostlist[$idx];
		$pass = 0;
	}
	if ($pass == 1) {
		$result .= '-' . $hostlist[$max-1];
	}
	return $result;
}

// input is a range expression string like 1-9,12-14,22-23,40,50-55

function expandRange( $rangeExpr = null )
{
	$result = array();
	if (empty($rangeExpr)) {
		return $result;
	}
	foreach (preg_split('/,/', $rangeExpr) as $range) {
//		echo "working on $range\n";
		if (preg_match('/-/', $range)) {
			list($start, $end) = preg_split('/-/', trim($range));
			if ($start > $end) {
				array_push($result, "$start" . '-' . "$end : wrong expression");
				continue;
			}
			$format = '%0' . strlen($start) . 'd';
			for ($i = $start; $i <= $end; ++$i) {
				array_push($result, sprintf($format, $i));
			}
		} else {
			array_push($result, $range);
		}
	}
	return $result;
}

function compressHostRange( $hostlist = null ) {
	$result = array();
	$hostTab = array();
	$hostlist = array_unique(explode("\n", trim($hostlist)));

	$pattern = '/(.+)-(\d+)(.*)/';
	if (empty($hostlist)) {
		return $result;
	}
	foreach ($hostlist as $host) {
		if (preg_match($pattern, trim($host), $matches)) {
//			print "DEBUG: pattern matched: $host\n";
			array_shift($matches);
			list ($name, $range, $suffix) = $matches;
			$akey = $name . '-[%s]';
			if (isset($suffix)) $akey .= $suffix;
//			print "Pushing $range to $akey\n";
			$hostTab[$akey][] = $range;
		} else {
			$trim_host = trim($host);
			if (!empty($trim_host)) $result[] = $trim_host;
		}
	}
	foreach ($hostTab as $host => $suffix) {
//		print "DEBUG: sending " . print_r($suffix, 1) . "\n";
		$rangeExp = compressRange($suffix);
//		array_push($result, sprintf($host, $rangeExp));
		$rentry = sprintf($host, $rangeExp);
		if (count($suffix) < 2) {
			$rentry = sprintf(preg_replace('/[\[\]]/', '', $host), $rangeExp);
		}
		$result[] = $rentry;
	}
	$hostTab = null;
	return $result;
}

function expandHostRange( $hostlist = null ) {
	$result = array();
	if (empty($hostlist)) {
		return $result;
	}
	$hostlist = array_unique(explode("\n", trim($hostlist)));
	$pattern = '/(.+)\[([\d-,]+)\](.*)$/';
	foreach ($hostlist as $host) {
		if (preg_match($pattern, trim($host), $matches)) {
			array_shift($matches);
			list ($name, $range, $suffix) = $matches;
			$expand = expandRange($range);
			foreach ($expand as $num) {
				$entry = $name .  $num;
				if (!empty($suffix)) $entry .= $suffix;
				$result[] = $entry;
			}
		} else {
			$result[] = $host;
		}
	}
	return $result;
}

/*
$avar[] = "login-a-01";
$avar[] = "login-a-02";
$avar[] = "login-a-003";
$avar[] = "login-a-004";
$avar[] = "login-b-02.google.com";
$avar[] = "login-a-04.google.com";



print_r(expandHostRange($bvar));
print_r(compressHostRange($avar));
print_r($avar);
*/


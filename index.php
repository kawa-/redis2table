<?php
$title = "redis2table";

$redis = new Redis();

if (!isset($_GET["host"]) or !isset($_GET["port"])) {
	die('<p>Error. Wrong host or port. Access like this -> http://localhost:8080/?host=localhost&port=6379</p><p> Or if you want erase and set random data of Redis, please like this -> http://localhost:8080/?host=localhost&port=6379&init=true</p>');
}

$host = htmlspecialchars($_GET["host"]);
$port = htmlspecialchars($_GET["port"]);
$timeout = 2.5;

if (!$redis->connect($host, $port, $timeout)) {
	die("<p>Error. Could not access to a local Redis server. Please check whether the Redis server is running or not.</p>");
}

// Testing data (Please use it if you have empty redis data)
if (isset($_GET["init"]) and $_GET["init"] === "true") {
	$redis->flushAll();

	$redis->set(uniqid("key_"), uniqid("key_value_1_"));
	$redis->set(uniqid("key_"), uniqid("key_value_2_"));
	$redis->set(uniqid("key_"), uniqid("key_value_3_"));

	$redis->hMset(uniqid("hash_"), array('name' => 'yukkuri_1', 'salary' => 1000));
	$redis->hMset(uniqid("hash_"), array('name' => 'yukkuri_2', 'salary' => 2000));
	$redis->hMset(uniqid("hash_"), array('name' => 'yukkuri_3', 'salary' => 3000));

	$redis->rPush(uniqid("list_"), uniqid("list_value_01_"), uniqid("list_value_02_"), uniqid("list_value_03_"));
	$redis->rPush(uniqid("list_"), uniqid("list_value_11_"), uniqid("list_value_12_"), uniqid("list_value_13_"));
	$redis->rPush(uniqid("list_"), uniqid("list_value_21_"), uniqid("list_value_22_"), uniqid("list_value_23_"));

	$redis->sAdd(uniqid("set_"), uniqid("set_value_01_"), uniqid("set_value_02_"));
	$redis->sAdd(uniqid("set_"), uniqid("set_value_11_"), uniqid("set_value_12_"));
	$redis->sAdd(uniqid("set_"), uniqid("set_value_21_"), uniqid("set_value_22_"));

	$key_zset_01 = uniqid("zset_");
	$redis->zAdd($key_zset_01, time(), uniqid("zset_value_01_"));
	$redis->zAdd($key_zset_01, time(), uniqid("zset_value_02_"));
	$redis->zAdd($key_zset_01, time(), uniqid("zset_value_03_"));
	$key_zset_02 = uniqid("zset_");
	$redis->zAdd($key_zset_02, time(), uniqid("zset_value_11_"));
	$redis->zAdd($key_zset_02, time(), uniqid("zset_value_12_"));
	$redis->zAdd($key_zset_02, time(), uniqid("zset_value_13_"));
	$key_zset_03 = uniqid("zset_");
	$redis->zAdd($key_zset_03, time(), uniqid("zset_value_21_"));
	$redis->zAdd($key_zset_03, time(), uniqid("zset_value_22_"));
	$redis->zAdd($key_zset_03, time(), uniqid("zset_value_23_"));
}

// prepare arrays for each types
$key_arr = array();
$hash_arr = array();
$list_arr = array();
$set_arr = array();
$zset_arr = array();

$all_keys = $redis->keys('*');
foreach ($all_keys as $key) {
	$type = $redis->type($key);
	switch ($type) {
		case 1: // key
			$key_arr[] = $key;
			break;
		case 2: // set
			$set_arr[] = $key;
			break;
		case 3: // list
			$list_arr[] = $key;
			break;
		case 4: // zset
			$zset_arr[] = $key;
			break;
		case 5: // hash
			$hash_arr[] = $key;
			break;
		default:
			die("Unknown type of redis has generated. Terminated.");
			break;
	}
}

// sort by NUMERIC
sort($key_arr, SORT_NUMERIC);
sort($set_arr, SORT_NUMERIC);
sort($list_arr, SORT_NUMERIC);
sort($zset_arr, SORT_NUMERIC);
sort($hash_arr, SORT_NUMERIC);

$key_value_table = array();
if (count($key_arr) !== 0) {
	foreach ($key_arr as $key) {
		$key_value_table[] = "<tr><td>" . $key . "</td><td>" . $redis->get($key) . "</td><td>" . $redis->ttl($key) . "</td></tr>";
	}
}

$set_table = array();
if (count($set_arr) !== 0) {
	foreach ($set_arr as $key) {
		$rowspan = $redis->sSize($key);
		$counter = $rowspan;
		$rep = '<tr><td rowspan="' . $rowspan . '">' . $key . '</td>';
		foreach ($redis->sMembers($key) as $set_value) {
			if ($rowspan === $counter) {
				$rep .= '<td>' . $set_value . '</td></tr>';
			} else {
				$rep = '<tr><td>' . $set_value . '</td></tr>';
			}
			$set_table[] = $rep;
			$counter--;
		}
	}
}

$zset_table = array();
if (count($zset_arr) !== 0) {
	foreach ($zset_arr as $key) {
		$rowspan = $redis->zSize($key);
		$counter = $rowspan;
		$rep = '<tr><td rowspan="' . $rowspan . '">' . $key . '</td>';
		foreach ($redis->zRange($key, 0, -1, true) as $zset_value => $zset_score) {
			if ($rowspan === $counter) {
				$rep .= '<td>' . $zset_value . '</td><td>' . $zset_score . '</td></tr>';
			} else {
				$rep = '<tr><td>' . $zset_value . '</td><td>' . $zset_score . '</td></tr>';
			}
			$zset_table[] = $rep;
			$counter--;
		}
	}
}

$hash_table = array();
if (count($hash_arr) !== 0) {
	foreach ($hash_arr as $key) {
		$rowspan = $redis->hLen($key);
		$counter = $rowspan;
		$rep = '<tr><td rowspan="' . $rowspan . '">' . $key . '</td>';
		foreach ($redis->hGetAll($key) as $hkey => $hfield) {
			if ($rowspan === $counter) {
				$rep .= '<td>' . $hkey . '</td><td>' . $hfield . '</td></tr>';
			} else {
				$rep = '<tr><td>' . $hkey . '</td><td>' . $hfield . '</td></tr>';
			}
			$hash_table[] = $rep;
			$counter--;
		}
	}
}

$list_table = array();
if (count($list_arr) !== 0) {
	foreach ($list_arr as $key) {
		$rowspan = $redis->lSize($key);
		$counter = $rowspan;
		$rep = '<tr><td rowspan="' . $rowspan . '">' . $key . '</td>';
		foreach ($redis->lRange($key, 0, -1) as $list_value) {
			if ($rowspan === $counter) {
				$rep .= '<td>' . $list_value . '</td></tr>';
			} else {
				$rep = '<tr><td>' . $list_value . '</td></tr>';
			}
			$list_table[] = $rep;
			$counter--;
		}
	}
}

$info_table = array();
foreach ($redis->info() as $key => $value) {
	$info_table[] = "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
}
// generate div and talbe.
function div_table($section_name, $item_array, $contents_array) {
	$res = "<div>";
	$res .= "<h2>";
	$res .= $section_name;
	$res .= "</h2>";
	$res .= '<table><tr align = "center">';
	foreach ($item_array as $value) {
		$res .= "<td>";
		$res .= $value;
		$res .= "</td>";
	}
	$res .= "</tr>";
	foreach ($contents_array as $value) {
		$res .= $value;
	}
	$res .= "</table></div>";
	return $res;
}
?>
<!DOCTYPE html>
<html lang = "en">
	<head>
		<title> <?php echo $title;
?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			table {
				border-collapse: collapse;
			}
			td {
				border: solid 1px;
				padding: 0.5em;
			}
		</style>
	</head>
	<body>
		<div>
            <h1><?php echo $title; ?></h1>
            <p>Redis monitoring tool. It shows a Redis Server's Key, List, Set, Sorted Set, Hash and Redis Info.</p>
			<p>Access like this -> http://localhost:8080/?host=localhost&port=6379</p>
        </div>
        <hr>
		<?php
		echo div_table("Key Value (String)", array("Key", "Value", "TTL"), $key_value_table);
		echo "<hr>";
		echo div_table("List", array("Key", "Value"), $list_table);
		echo "<hr>";
		echo div_table("Set", array("Key", "Value"), $set_table);
		echo "<hr>";
		echo div_table("Sorted Set", array("Key", "Value", "Score"), $zset_table);
		echo "<hr>";
		echo div_table("Hash", array("Key", "Field", "Value"), $hash_table);
		echo "<hr>";
		echo div_table("Redis info", array("Key", "Value"), $info_table);
		?>
		<hr>
	</body>
</html>

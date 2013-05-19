<?php
$title = "redis2table";

$redis = new Redis();
$redis->connect("localhost", 6379, 2.5); // Please edit here on your env. 2.5 sec timeout.

/* Testing data
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
 */

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

$key_value_table = array();
if (count($key_arr) !== 0) {
	foreach ($key_arr as $key) {
		$key_value_table[] = "<tr><td>" . $key . "</td><td>" . $redis->get($key) . "</td><td>" . $redis->ttl($key) . "</td></tr>";
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
            <p>Show all the Redis keys and values.</p>
        </div>
        <hr>
		<div>
			<h2>Key-Value</h2>
			<table>
				<tr align = "center">
					<td>key</td>
					<td>value</td>
					<td>ttl</td>
				</tr>
				<?php
				foreach ($key_value_table as $value) {
					echo $value . "\n";
				}
				?>
			</table>
		</div>
		<hr>
		<div>
			<h2>List</h2>
			<table>
				<tr align = "center">
					<td>key</td>
					<td>value</td>
				</tr>
				<?php
				foreach ($list_table as $value) {
					echo $value . "\n";
				}
				?>
			</table>
		</div>
		<hr>
		<div>
			<h2>Set</h2>
			<table>
				<tr align = "center">
					<td>key</td>
					<td>value</td>
				</tr>
				<?php
				foreach ($set_table as $value) {
					echo $value . "\n";
				}
				?>
			</table>
		</div>
		<hr>
		<div>
			<h2>zSet</h2>
			<table>
				<tr align = "center">
					<td>key</td>
					<td>value</td>
					<td>score</td>
				</tr>
				<?php
				foreach ($zset_table as $value) {
					echo $value . "\n";
				}
				?>
			</table>
		</div>
		<hr>
		<div>
			<h2>Hash</h2>
			<table>
				<tr align = "center">
					<td>key</td>
					<td>field</td>
					<td>value</td>
				</tr>
				<?php
				foreach ($hash_table as $value) {
					echo $value . "\n";
				}
				?>
			</table>
		</div>
	</body>
</html>

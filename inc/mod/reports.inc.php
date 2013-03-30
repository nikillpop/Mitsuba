<?php
if (!defined("IN_MOD"))
{
	die("Nah, I won't serve that file to you.");
}
if ((!empty($_GET['cl'])) && ($_GET['cl']==1))
	{
		if ((!empty($_GET['id'])) && (is_numeric($_GET['id'])))
		{
			$conn->query("DELETE FROM reports WHERE id=".$_GET['id']);
		}
	}
	?>
	<div class="box-outer top-box">
<div class="box-inner">
<div class="boxbar"><h2><?php echo $lang['mod/reports']; ?></h2></div>
<div class="boxcontent">
<?php
if ($_SESSION['type'] >= 1)
{
?>
<a href="?/reports/clear_all"><?php echo $lang['mod/clear_all']; ?></a>
<?php
}
?>
<table>
<thead>
<tr>
<td><?php echo $lang['mod/post']; ?></td>
<td><?php echo $lang['mod/file']; ?></td>
<td><?php echo $lang['mod/comment']; ?></td>
<td><?php echo $lang['mod/reason']; ?></td>
<td><?php echo $lang['mod/reporter_ip']; ?></td>
<td><?php echo $lang['mod/actions']; ?></td>
</tr>
</thead>
<tbody>
<?php
		require_once( "./jbbcode/Parser.php" );
		$parser = new JBBCode\Parser();
		$bbcode = $conn->query("SELECT * FROM bbcodes;");
		
		while ($row = $bbcode->fetch_assoc())
		{
			$parser->addBBCode($row['name'], $row['code']);
		}
		$result = $conn->query("SELECT * FROM reports ORDER BY created DESC");
		while ($row = $result->fetch_assoc())
		{
			$post = $conn->query("SELECT * FROM posts WHERE id=".$row['reported_post']." AND board='".$row['board']."'");
			if ($post->num_rows == 0)
			{
				$conn->query("DELETE FROM reports WHERE id=".$row['id']);
				continue;
			}
			$pdata = $post->fetch_assoc();
			$resto = $pdata['id'];
			if ($pdata['resto'] != 0)
			{
				$resto = $pdata['resto'];
			}
			echo "<tr>";
			echo "<td><a href='?/board&b=".$row['board']."&t=".$resto."#p".$row['reported_post']."'>/".$row['board']."/".$row['reported_post']."</a></td>";
			if (!empty($pdata['filename']))
			{
				if ($pdata['filename'] == "deleted")
				{
					echo "<td><img src='./img/deleted.gif' /></td>";
				} elseif (substr($pdata['filename'], 0, 8) == "spoiler:") {
					echo "<td><a href='./".$row['board']."/src/".substr($pdata['filename'], 8)."' target='_blank'><img src='./".$row['board']."/src/thumb/".substr($pdata['filename'], 8)."' /></a></td>";
				} elseif (substr($pdata['filename'], 0, 6) == "embed:") {
					echo "<td><a href='".substr($pdata['filename'], 6)."'>Embed</a></td>";
				} else {
					echo "<td><a href='./".$row['board']."/src/".$pdata['filename']."' target='_blank'><img src='./".$row['board']."/src/thumb/".$pdata['filename']."' /></a></td>";
				}
			} else {
				echo "<td></td>";
			}
			if ($pdata['raw'] == 0)
			{
				echo "<td>".processComment($row['board'], $conn, $pdata['comment'], $parser, 2)."</td>";
			} elseif ($pdata['raw'] == 2)
			{
				echo "<td>".processComment($row['board'], $conn, $pdata['comment'], $parser, 2, 0)."</td>";
			} else {
				echo "<td>".$pdata['comment']."</td>";
			}
			echo "<td>".$row['reason']."</td>";
			echo "<td>".$row['reporter_ip']."</td>";
			echo "<td>[ <a href='?/reports&cl=1&id=".$row['id']."'>C</a> ] [ <a href='?/bans/add&b=".$row['board']."&p=".$row['reported_post']."'>B</a> "; 
			if ($_SESSION['type']>=1)
			{
				echo "/ <a href='?/bans/add&b=".$row['board']."&p=".$row['reported_post']."&d=1'>&</a> / <a href='?/delete_post&b=".$row['board']."&p=".$row['reported_post']."'>D</a> / <a href='?/delete_post&b=".$row['board']."&p=".$row['reported_post']."&f=1'>F</a> ]"; 
				echo "[ <a href='?/info&ip=".$pdata['ip']."'>N</a> ]</td>";
			} else {
				echo "]</td>";
			}
			echo "</tr>";
		}
		?>
		</tbody>
		</table>
		</div>
		</div>
		</div>
		<script type="text/javascript">parent.nav.location.reload();</script>
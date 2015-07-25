<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<div style="float: left; width: 50%;">
<img src="https://alexc.hu/rotustats.png" style="float: right; height: 250px;">
</div>
<div style="float: left; width: 50%; height: 250px;">
<?php

// DB connection
$db = new PDO('mysql:host=localhost;dbname=rotu;charset=utf8', 'root', '');

// gameID, fallback last game
$sth = $db->prepare('SELECT id FROM rotustats_game ORDER BY id DESC LIMIT 1 ');
$sth->execute();
$lastid = $sth->fetchColumn();

@$gameid = $_GET['id']?$_GET['id']:$lastid;

foreach($db->query('SELECT * FROM rotustats_game WHERE id='.$gameid) as $row) {
    echo "<h2>".$row['version']." #$gameid</h2>";
    echo "<a href='".$_SERVER['PHP_SELF']."?id=".($gameid-1)."'>&lt;&lt; Previous </a>";
    if ($gameid!=$lastid) { echo "<a href='".$_SERVER['PHP_SELF']."?id=".($gameid+1)."'> Next &gt;&gt;</a>"; }
    echo '<br><h3>';
    echo $row['win']?'<font color="green">WIN</font>':'<font color="red">LOSE</font>';
    echo '</h3><br><b>';
    echo $row['zombiesKilled'].'</b> zombies killed';
    echo '<br>';
    echo 'Duration: '.gmdate("H:i:s", $row['gameDuration']/1000);
    echo '<br>';
    echo 'Waves: '.$row['waveNumber'];
    echo '<br>';
    echo 'Map: '.$row['mapname'];
    echo '<br>';
    echo 'Date: '.$row['date'];
}

?>

</div>

<div id="roledist" style="width:50%; height:300px; margin-top: 50px; float: left;"></div>
<div id="killdmg" style="width:50%; height:300px; margin-top: 50px; float: left;"></div>

<div id="healsrevive" style="width:33%; height:300px; margin-top: 50px; float: left;"></div>
<div id="ammoturret" style="width:33%; height:300px; margin-top: 50px; float: left;"></div>
<div id="headshots" style="width:33%; height:300px; margin-top: 50px; float: left;"></div>

<?php
/* Classes */
foreach($db->query('SELECT role, COUNT(*) FROM rotustats_player WHERE id='.$gameid.' GROUP BY role') as $roles) {
    $roledist[$roles[0]]=$roles[1];
}
/* Kills and dmg */
foreach($db->query('SELECT name, kills, damageDealt FROM rotustats_player WHERE id='.$gameid.'') as $kills) {
    $killdmg[]=array($kills[0],$kills[1],$kills[2]);
}
/* Medic */
foreach($db->query('SELECT name, healsGiven, revives FROM rotustats_player WHERE id='.$gameid.' AND role=\'medic\'') as $heals) {
    $healsrevive[]=array($heals[0],$heals[1],$heals[2]);
}
/* Engineer */
foreach($db->query('SELECT name, ammoGiven, turretKills FROM rotustats_player WHERE id='.$gameid.' AND role=\'engineer\'') as $supply) {
    $ammoturret[]=array($supply[0],$supply[1],$supply[2]);
}
/* Scout */
foreach($db->query('SELECT name, headshotKills FROM rotustats_player WHERE id='.$gameid.' AND role=\'scout\'') as $hs) {
    $headshots[]=array($hs[0],$hs[1]);
}

?>

<script>
$(function () {
    $('#roledist').highcharts({
        chart: {
            backgroundColor: null,
            type: 'pie'
        },
        title: {
            text: 'Role distribution'
        },
        series: [{
            name: 'People',
            data: [
            <?php if(isset($roledist['medic'])) { ?>
            {
                name: 'Medic',
                color: '#FF0000',
                y: <?=$roledist['medic'] ?>
            },
            <?php } if(isset($roledist['engineer'])) { ?>
            {
                name: 'Engineer',
                color: '#FFFF00',
                y: <?=$roledist['engineer'] ?>
            },
            <?php } if(isset($roledist['stealth'])) { ?>
            {
                name: 'Assassin',
                color: '#00FF00',
                y: <?=$roledist['stealth'] ?>
            },
            <?php } if(isset($roledist['scout'])) { ?>
            {
                name: 'Scout',
                color: '#00FFFF',
                y: <?=$roledist['scout'] ?>
            },
            <?php } if(isset($roledist['soldier'])) { ?>
            {
                name: 'Soldier',
                color: '#0000FF',
                y: <?=$roledist['soldier'] ?>
            },
            <?php } if(isset($roledist['armored'])) { ?>
            {
                name: 'Armored',
                color: '#FF00FF',
                y: <?=$roledist['armored'] ?>
            },
            <?php } ?>
            ]
        }]
    });
});

$(function () {
    $('#killdmg').highcharts({
        chart: {
            backgroundColor: null
        },
        title: {
            text: 'Kills and damage'
        },
        xAxis: {
            categories: [<?php foreach($killdmg as $k) { echo "'$k[0]',"; } ?>]
        },
        yAxis: [{title: {text: 'Kills'}},{title: {text: 'Damage'}, min: 0, opposite: true}],
        series: [ {
            type: 'column',
            name: 'Kills',
            data: [<?php foreach($killdmg as $k) { echo "$k[1],"; } ?>]
        }, {
            type: 'spline',
            yAxis: 1,
            name: 'Damage',
            data: [<?php foreach($killdmg as $k) { echo "$k[2],"; } ?>]
        }]
    });
});
<?php 
    if(isset($healsrevive)) {
    ?>
$(function () {
    $('#healsrevive').highcharts({
        chart: {
            backgroundColor: null
        },
        title: {
            text: 'Best medic'
        },
        xAxis: {
            categories: [<?php foreach($healsrevive as $h) { echo "'$h[0]',"; } ?>]
        },
        yAxis: [{title: {text: 'Heals'}},{title: {text: 'Revive'}, min: 0, opposite: true}],
        series: [ {
            type: 'column',
            name: 'Heals',
            data: [<?php foreach($healsrevive as $h) { echo "$h[1],"; } ?>]
        }, {
            type: 'spline',
            yAxis: 1,
            name: 'Revive',
            data: [<?php foreach($healsrevive as $h) { echo "$h[2],"; } ?>]
        }]
    });
});
<?php 
    }
    if(isset($ammoturret)) {
    ?>
$(function () {
    $('#ammoturret').highcharts({
        chart: {
            backgroundColor: null
        },
        title: {
            text: 'Best engineer'
        },
        xAxis: {
            categories: [<?php foreach($ammoturret as $a) { echo "'$a[0]',"; } ?>]
        },
        yAxis: [{title: {text: 'Ammo Given'}},{title: {text: 'turret Kills'}, min: 0, opposite: true}],
        series: [ {
            type: 'column',
            name: 'Ammo given',
            data: [<?php foreach($ammoturret as $a) { echo "$a[1],"; } ?>]
        }, {
            type: 'spline',
            yAxis: 1,
            name: 'Turret kills',
            data: [<?php foreach($ammoturret as $a) { echo "$a[2],"; } ?>]
        }]
    });
});
<?php 
    }
    if(isset($headshots)) {
    ?>
$(function () {
    $('#headshots').highcharts({
        chart: {
            backgroundColor: null
        },
        title: {
            text: 'Best scout'
        },
        xAxis: {
            categories: [<?php foreach($headshots as $s) { echo "'$s[0]',"; } ?>]
        },
        yAxis: [{title: {text: 'Headshots'}}],
        series: [ {
            type: 'column',
            name: 'Headshots',
            data: [<?php foreach($headshots as $s) { echo "$s[1],"; } ?>]
        }]
    });
});
<?php
    }
    ?>
</script>
<div style="text-align: center; width: 100%; float: left;">
&copy; created by Mikopet based on Puffyforum.com ideas
</div>
<!-- everybody knows, we are not supposed to use center, and other deprecated shit, but i dont have energy to thinking, and dont give a shit about spagetti code
    in the next version i take a care of it -->
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<div style="float: left; width: 50%;">
<img src="https://alexc.hu/rotustats.png" style="float: right; height: 250px;">
</div>
<div style="float: left; width: 50%; height: 250px;">
<?php

// DB kapcsolat
$db = new PDO('mysql:host=localhost;dbname=rotu;charset=utf8', 'root', '');

// gameID, fallback utolso
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

<?php
/* Kasztok */
foreach($db->query('SELECT role, COUNT(*) FROM rotustats_player WHERE id='.$gameid.' GROUP BY role') as $roles) {
    $roledist[$roles[0]]=$roles[1];
}

foreach($db->query('SELECT name, kills, damageDealt FROM rotustats_player WHERE id='.$gameid.'') as $kills) {
    $killdmg[]=array($kills[0],$kills[1],$kills[2]);
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
        yAxis: [{},{min: 0, opposite: true}],
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
</script>

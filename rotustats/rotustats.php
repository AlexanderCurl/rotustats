<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<?php
    
$gameid = $_GET['id'] or die('nincs gameID');
    
$db = new PDO('mysql:host=localhost;dbname=yourdbnamehere;charset=utf8', 'root', 'yoursecretpassword');
foreach($db->query('SELECT * FROM rotustats_game WHERE id='.$gameid) as $row) {
    echo $row['version'];
    echo '<br>';
    echo $row['win']?'NYERTES':'VESZTES';
    echo '<br>';
    echo $row['zombiesKilled'].' zombi megolve';
    echo '<br>';
    echo 'Idotartam: '.gmdate("H:i:s", $row['gameDuration']/1000);
    echo '<br>';
    echo 'Hullamok: '.$row['waveNumber'];
    echo '<br>';
    echo 'Map: '.$row['mapname'];
    echo '<br>';
    echo 'Date: '.$row['date'];
}

?>

<div id="roledist" style="width:40%; height:400px;"></div>
<div id="killdmg" style="width:40%; height:400px;"></div>

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
            type: 'pie'
        },
        title: {
            text: 'Szerepek eloszlasa'
        },
        series: [{
            name: 'Darab',
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
        title: {
            text: 'Killek es sebzes'
        },
        xAxis: {
            categories: [<?php foreach($killdmg as $k) { echo "'$k[0]',"; } ?>]
        },
        yAxis: [{},{min: 0, opposite: true}],
        series: [ {
            type: 'column',
            name: 'Killek',
            data: [<?php foreach($killdmg as $k) { echo "$k[1],"; } ?>]
        }, {
            type: 'spline',
            yAxis: 1,
            name: 'Sebzes',
            data: [<?php foreach($killdmg as $k) { echo "$k[2],"; } ?>]
        }]
    });
});
</script>

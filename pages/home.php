<?php

namespace lib;

use lib\Uptime;
use lib\Memory;
use lib\CPU;
use lib\Storage;
use lib\Network;
use lib\Rbpi;
use lib\Users;
use lib\Temp;

$uptime = Uptime::uptime();
$ram = Memory::ram();
$swap = Memory::swap();
$cpu = CPU::cpu();
$cpu_heat = CPU::heat();
$hdd = Storage::hdd();
$hdd_alert = 'success';
for ($i = 0; $i < sizeof($hdd); $i++) {
    if ($hdd[$i]['alert'] == 'warning')
        $hdd_alert = 'warning';
}
$network = Network::connections();
$users = sizeof(Users::connected());
$temp = Temp::temp();

$external_ip = Rbpi::externalIp();

function icon_alert($alert) {
    echo '<span class="glyphicon glyphicon-';
    switch ($alert) {
        case 'success':
            echo 'ok';
            break;
        case 'warning':
            echo 'warning-sign';
            break;
        default:
            echo 'exclamation-sign';
    }
    echo ' pull-right"></span>';
}
?>

<div class="container home">
    <div class="row-fluid infos">
        <div class="span4">
            <span class="glyphicon glyphicon-home"></span> <?php echo Rbpi::hostname(); ?>
        </div>
        <div class="span4">
            <span class="glyphicon glyphicon-map-marker"></span> <?php echo Rbpi::internalIp(); ?>
            <?php echo ($external_ip != 'Unavailable') ? '<br /><span class="glyphicon glyphicon-globe"></span> ' . $external_ip : ''; ?>
        </div>
        <div class="span4">
            <span class="glyphicon glyphicon-play-circle"></span> Server <?php echo Rbpi::webServer(); ?>
        </div>
    </div>

    <div class="infos">
        <div>
            <a href="<?php echo DETAILS; ?>#check-uptime"><span class="glyphicon glyphicon-time"></span></a> <?php echo $uptime; ?>		        
        </div>		
    </div>
    <div class="row-fluid">
        <div class="span4 rapid-status">
            <div>
                <span class="glyphicon glyphicon-asterisk"></span> RAM <a href="<?php echo DETAILS; ?>#check-ram"><?php echo icon_alert($ram['alert']); ?></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-refresh"></span> Swap <a href="<?php echo DETAILS; ?>#check-swap"><?php echo icon_alert($swap['alert']); ?></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-tasks"></span> CPU <a href="<?php echo DETAILS; ?>#check-cpu"><?php echo icon_alert($cpu['alert']); ?></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-fire"></span> CPU <a href="<?php echo DETAILS; ?>#check-cpu-heat"><?php echo icon_alert($cpu_heat['alert']); ?></a>
            </div>
        </div>
        <div class="span4 offset4 rapid-status">
            <div>
                <span class="glyphicon glyphicon-hdd"></span> Stockage <a href="<?php echo DETAILS; ?>#check-storage"><?php echo icon_alert($hdd_alert); ?></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-globe"></span> Réseau <a href="<?php echo DETAILS; ?>#check-network"><?php echo icon_alert($network['alert']); ?></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-user"></span> Utilisateurs <a href="<?php echo DETAILS; ?>#check-users"><span class="badge pull-right"><?php echo $users; ?></span></a>
            </div>
            <div>
                <span class="glyphicon glyphicon-fire"></span> Température <a href="<?php echo DETAILS; ?>#check-temp"><?php echo icon_alert($temp['alert']); ?></a>
            </div>
        </div>
    </div>
</div>

</div>

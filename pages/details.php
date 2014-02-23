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
$net_connections = Network::connections();
$net_eth = Network::ethernet();
$users = Users::connected();
$temp = Temp::temp();

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
    echo '"></span>';
}

function shell_to_html_table_result($shellExecOutput) {
    $shellExecOutput = preg_split('/[\r\n]+/', $shellExecOutput);

    // remove double (or more) spaces for all items
    foreach ($shellExecOutput as &$item) {
        $item = preg_replace('/[[:blank:]]+/', ' ', $item);
        $item = trim($item);
    }

    // remove empty lines
    $shellExecOutput = array_filter($shellExecOutput);

    // the first line contains titles
    $columnCount = preg_match_all('/\s+/', $shellExecOutput[0]);
    $shellExecOutput[0] = '<tr><th>' . preg_replace('/\s+/', '</th><th>', $shellExecOutput[0], $columnCount) . '</th></tr>';
    $tableHead = $shellExecOutput[0];
    unset($shellExecOutput[0]);

    // others lines contains table lines
    foreach ($shellExecOutput as &$item) {
        $item = '<tr><td>' . preg_replace('/\s+/', '</td><td>', $item, $columnCount) . '</td></tr>';
    }

    // return the build table
    return '<table class=\'table table-striped\'>'
            . '<thead>' . $tableHead . '</thead>'
            . '<tbody>' . implode($shellExecOutput) . '</tbody>'
            . '</table>';
}
?>
<div class="container details">

    <table>
        <tr id="check-system">
            <td class="check"><span class="glyphicon glyphicon-cog"></span> Système</td>
            <td class="icon"></td>
            <td class="infos">
                nom d'hôte: <span class="text-info"><?php echo Rbpi::hostname(true); ?></span>
                <br />distribution: <span class="text-info"><?php echo Rbpi::distribution(); ?></span>
                <br />noyau: <?php echo Rbpi::kernel(); ?>
                <br />micrologiciel: <?php echo Rbpi::firmware(); ?>
            </td>
        </tr>

        <tr id="check-uptime">
            <td class="check"><span class="glyphicon glyphicon-time"></span> Durée de fonctionnement</td>
            <td class="icon"></td>
            <td class="infos"><?php echo $uptime; ?></td>
        </tr>

        <tr id="check-ram">
            <td class="check"><span class="glyphicon glyphicon-asterisk"></span> RAM</td>
            <td class="icon"><?php echo icon_alert($ram['alert']); ?></td>
            <td class="infos">
                <div class="progress" id="popover-ram">
                    <div class="progress-bar progress-bar-<?php echo $ram['alert']; ?>" style="width: <?php echo $ram['percentage']; ?>%;">
                    	<span class="sr-only"><?php echo $ram['percentage']; ?>%</span>
                    </div>
                </div>
                <div id="popover-ram-head" class="hide">Top RAM eaters</div>
                <div id="popover-ram-body" class="hide"><?php echo shell_to_html_table_result($ram['detail']); ?></div>
                libre: <span class="text-success"><?php echo $ram['free']; ?>Mb</span>  &middot; utilisé: <span class="text-warning"><?php echo $ram['used']; ?>Mb</span> &middot; total: <?php echo $ram['total']; ?>Mb
            </td>
        </tr>

        <tr id="check-swap">
            <td class="check"><span class="glyphicon glyphicon-refresh"></span> Swap</td>
            <td class="icon"><?php echo icon_alert($swap['alert']); ?></td>
            <td class="infos">
                <div class="progress">
                    <div class="progress-bar progress-bar-<?php echo $swap['alert']; ?>" style="width: <?php echo $swap['percentage']; ?>%;">
                    	<span class="sr-only"><?php echo $swap['percentage']; ?>%</span>
                    </div>
                </div>
                libre: <span class="text-success"><?php echo $swap['free']; ?>Mb</span>  &middot; utilisé: <span class="text-warning"><?php echo $swap['used']; ?>Mb</span> &middot; total: <?php echo $swap['total']; ?>Mb
            </td>
        </tr>

        <tr id="check-cpu">
            <td class="check"><span class="glyphicon glyphicon-tasks"></span> CPU</td>
            <td class="icon"><?php echo icon_alert($cpu['alert']); ?></td>
            <td class="infos">
                charges: <?php echo $cpu['loads']; ?> [1 min] &middot; <?php echo $cpu['loads5']; ?> [5 min] &middot; <?php echo $cpu['loads15']; ?> [15 min]
                <br />fonctionne à <span class="text-info"><?php echo $cpu['current']; ?></span> (min: <?php echo $cpu['min']; ?>  &middot;  max: <?php echo $cpu['max']; ?>)
                <br />gouverneur: <strong><?php echo $cpu['governor']; ?></strong>
            </td>
        </tr>

        <tr id="check-cpu-heat">
            <td class="check"><span class="glyphicon glyphicon-fire"></span> CPU</td>
            <td class="icon"><?php echo icon_alert($cpu_heat['alert']); ?></td>
            <td class="infos">
                <div class="progress" id="popover-cpu">
                    <div class="progress-bar progress-bar-<?php echo $cpu_heat['alert']; ?>" style="width: <?php echo $cpu_heat['percentage']; ?>%;">
                    	<span class="sr-only"><?php echo $cpu_heat['percentage']; ?>%</span>
                    </div>
                </div>
                <div id="popover-cpu-head" class="hide">Top CPU eaters</div>
                <div id="popover-cpu-body" class="hide"><?php echo shell_to_html_table_result($cpu_heat['detail']); ?></div>
                chaleur: <span class="text-info"><?php echo $cpu_heat['degrees']; ?>°C</span>
            </td>
        </tr>

        <tr class="storage" id="check-storage">
            <td class="check" rowspan="<?php echo sizeof($hdd); ?>"><span class="glyphicon glyphicon-hdd"></span> Stockage</td>
            <?php
            for ($i = 0; $i < sizeof($hdd); $i++) {
                echo '<td class="icon" style="padding-left: 10px;">', icon_alert($hdd[$i]['alert']), '</td>
            <td class="infos">
              <span class="glyphicon glyphicon-folder-open"></span> ', $hdd[$i]['name'], '
              <div class="progress">
                <div class="progress-bar progress-bar-', $hdd[$i]['alert'], '" style="width: ', $hdd[$i]['percentage'], '%;"><span class="sr-only">', $hdd[$i]['percentage'], '%</span></div>
              </div>
              libre: <span class="text-success">', $hdd[$i]['free'], 'b</span> &middot; utilisé: <span class="text-warning">', $hdd[$i]['used'], 'b</span> &middot; total: ', $hdd[$i]['total'], 'b &middot; format: ', $hdd[$i]['format'], '
            </td>
          </tr>
          ', ($i == sizeof($hdd) - 1) ? null : '<tr class="storage">';
            }
            ?>

        <tr id="check-network">
            <td class="check"><span class="glyphicon glyphicon-globe"></span> Réseau</td>
            <td class="icon"><?php echo icon_alert($net_connections['alert']); ?></td>
            <td class="infos">
                IP: <span class="text-info"><?php echo Rbpi::internalIp(); ?></span> [interne] &middot;
                <span class="text-info"><?php echo Rbpi::externalIp(); ?></span> [externe]
                <br />reçu: <strong><?php echo $net_eth['down']; ?>Mb</strong> &middot; expédié: <strong><?php echo $net_eth['up']; ?>Mb</strong> &middot; total: <?php echo $net_eth['total']; ?>Mb
                <br />liens: <?php echo $net_connections['connections']; ?>
            </td>
        </tr>

        <tr id="check-users">
            <td class="check"><span class="glyphicon glyphicon-user"></span> Utilisateurs</td>
            <td class="icon"><span class="badge"><?php echo sizeof($users); ?></span></td>
            <td class="infos">
                <ul class="unstyled">
                    <?php
                    if (sizeof($users) > 0) {
                        for ($i = 0; $i < sizeof($users); $i++)
                            echo '<li><span class="text-info">', $users[$i]['user'], '</span> le ', $users[$i]['date'], ' à ', $users[$i]['hour'], ' depuis <strong>', $users[$i]['ip'], '</strong> ', $users[$i]['dns'], '</li>', "\n";
                    }
                    else
                        echo '<li>aucun utilisateur connecté</li>';
                    ?>
                </ul>
            </td>
        </tr>

        <?php
        if ($temp['degrees'] != "N/A") {
            ?>
            <tr id="check-temp">
                <td class="check"><span class="glyphicon glyphicon-fire"></span> DS18B20</td>
                <td class="icon"><?php echo icon_alert($temp['alert']); ?></td>
                <td class="infos">
                    <span class="text-info"><?php echo $temp['degrees']; ?></span>
                </td>
            </tr>
            <?php
        }
        ?>

    </table>
</div>

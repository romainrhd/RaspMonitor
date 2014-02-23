<?php

namespace lib;

use lib\Disks;

$disks = Disks::disks();

function label_partition($status) {
    echo '<span class="label label-';
    switch ($status) {
        default:
            echo 'success';
            break;
        case '':
            echo 'danger';
            break;
    }
    echo '">';
    switch ($status) {
        default:
            echo 'Monté';
            break;
        case '':
            echo 'Non Monté';
            break;
    }
    echo '</span>';
}
?>

<div class="container details">
    <table>
        <tr class="disks" id="check-disks">
            <td class="check" rowspan="<?php echo sizeof($disks); ?>"><span class="glyphicon glyphicon-cog"></span> Disques</td>
            <?php
            for ($i = 0; $i < sizeof($disks); $i++) {
                if ($disks[$i]["type"] != "disk") {
                    if (strpos($disks[$i]['name'], "sda") !== false) {
                        echo '<td class="icon" style="padding-left: 10px;">';
                        echo '<a data-rootaction="changepartitionstatus" data-partition-name="' . $disks[$i]["name"] . '" data-curr-mountpoint="' . $disks[$i]["mountpoint"] . '" class="rootaction" href="javascript:;">';
                        echo label_partition($disks[$i]['mountpoint']), '</a></td>';
                    }
                    else
                        echo '<td class="icon" style="padding-left: 10px;">', label_partition($disks[$i]['mountpoint']), '</td>';
                    echo '<td class="infos">', $disks[$i]['name'] . "<br>Taille: " . $disks[$i]['size'] . "<br>Point de montage: " . $disks[$i]['mountpoint'], '</td>';
                }
                else
                    echo '<td class="icon">', $disks[$i]['name'], '</td>';

                echo '</tr>';
            }
            ?>
    </table>
</div>
<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require 'config.php';

require 'lib/phpseclib/Net/SSH2.php';

$ssh = new \Net_SSH2('localhost');

// authentification
if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {
    if (empty($_GET['page']))
        $_GET['page'] = 'home';
    $_GET['page'] = htmlspecialchars($_GET['page']);
    $_GET['page'] = str_replace("\0", '', $_GET['page']);
    $_GET['page'] = str_replace(DIRECTORY_SEPARATOR, '', $_GET['page']);
    $display = true;

    function is_active($page) {
        if ($page == $_GET['page'])
            echo ' class="active"';
    }

} else {
    $_GET['page'] = 'login';
    $display = false;
}

$page = 'pages' . DIRECTORY_SEPARATOR . $_GET['page'] . '.php';
$page = file_exists($page) ? $page : 'pages' . DIRECTORY_SEPARATOR . '404.php';

if (isset($_GET['action']) && isset($_GET['username']) && isset($_GET['password'])) {
    if ($ssh->login($_GET['username'], $_GET['password'])) {
        $action = $_GET['action'];
        if ($action == 'reboot') {
            echo "Action: " . $_GET["action"] . "\\nSuccessfully perfomed ";           
            $ssh->exec("sudo shutdown -r now");
        } else if ($action == 'shutdown') {
            echo "Action: " . $_GET["action"] . "\\nSuccessfully perfomed ";
            $ssh->exec("sudo shutdown -h now");
        } else if ($action == 'changeservicestatus') {
            $services = Services::services();
            for ($i = 0; $i < sizeof($services); $i++) {
                if ($services[$i]['name'] == $_GET['servicename']) {
                    if ($services[$i]['status'] == '+') {
                        $ssh->exec("sudo service " . $services[$i]['name'] . " stop");
                        echo "Service: " . $services[$i]['name'] . " stopped";
                    } else {
                        $ssh->exec("sudo service " . $services[$i]['name'] . " start");
                        echo "Service: " . $services[$i]['name'] . " started";
                    }
                    break;
                }
            }
        } else if ($action == 'changepartitionstatus') {
            $disks = Disks::disks();
            for ($i = 0; $i < sizeof($disks); $i++) {
                if ($disks[$i]['name'] == $_GET['partitionname']) {
                    if ($disks[$i]['mountpoint'] == '') {
                        if (isset($_GET['mountpoint'])) {
                            $ssh->exec("sudo mount /dev/" . $disks[$i]['name'] . " '" . str_replace("%20", " ", $_GET['mountpoint']) . "'");
                            echo "Partition: " . $disks[$i]['name'] . "\nMounted on: " . str_replace("%20", " ", $_GET['mountpoint']);
                        }
                    } else {
                        $ssh->exec("sudo umount /dev/" . $disks[$i]['name']);
                        echo "Partition: " . $disks[$i]['name'] . " unmounted";
                    }
                    break;
                }
            }
        }
    } else {
        echo 'Can\'t perform ' . $_GET["action"] . '\\nError: Login failed';
    }

    exit();
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Raspcontrol</title>
        <meta name="robots" content="noindex, nofollow, noarchive" />
        <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
        <link rel="icon" type="image/png" href="img/favicon.ico" />
        <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
        <link href="css/raspcontrol.css" rel="stylesheet" media="screen" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />    
    </head>

    <body>
    <div id="wrap">
    
    <?php if ($display) : ?>
        <div class="navbar navbar-default navbar-static-top" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
			            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			              <span class="sr-only">Toggle navigation</span>
			              <span class="icon-bar"></span>
			              <span class="icon-bar"></span>
			              <span class="icon-bar"></span>
			            </button>
			            <a class="navbar-brand" href="#">Raspcontrol</a>
			        </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li<?php is_active('home'); ?>><a href="<?php echo INDEX; ?>"><span class="glyphicon glyphicon-home"></span> Accueil</a></li>
                            <li<?php is_active('details'); ?>><a href="<?php echo DETAILS; ?>"><span class="glyphicon glyphicon-search"></span> Détails</a></li>
                            <li<?php is_active('services'); ?>><a href="<?php echo SERVICES; ?>"><span class="glyphicon glyphicon-cog"></span> Services</a></li>
                            <li<?php is_active('disks'); ?>><a href="<?php echo DISKS; ?>"><span class="glyphicon glyphicon-hdd"></span> Disques</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?php echo LOGOUT; ?>"><span class="glyphicon glyphicon-off"></span> Deconnexion</a></li>    
                            <li><a data-rootaction="reboot" class="rootaction" href="#"><span class="glyphicon glyphicon-repeat"></span> Redémarrage</a></li>
                            <li><a data-rootaction="shutdown" class="rootaction" href="#"><span class="glyphicon glyphicon-stop"></span> Exctinction</a></li>             
                        </ul>
                    </div>
                </div>
        </div>
    <?php endif; ?>
    <div id="content">
    	<div class="contenuOpac">
        <?php if (isset($_SESSION['message'])) { ?>
            <div class="container">
                <div class="alert alert-danger">
                	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo $_SESSION['message']; ?>
                </div>
            </div>
            <?php unset($_SESSION['message']);
        } ?>

        <?php
        include $page;
        ?>
		</div>
    </div> <!-- /content -->
	</div>
    <footer>
    	<div id="footer">
	         	<p>Revisité en version Bootstrap3 par <a href="http://www.romain-r.fr/" target="_blank">Romain Richard</a>.</p>
        </div>
    </footer>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <?php
    // load specific scripts
    if ('details' === $_GET['page']) {
        echo '   <script src="js/details.js"></script>';
    }
    ?>

    <!-- General scripts -->
    <script>
        $(function() {

            var username = $("#username"),
                    password = $("#password"),
                    allFields = $([]).add(name).add(password),
                    tips = $(".validateTips");

            function updateTips(t) {
                tips
                        .text(t)
                        .addClass("ui-state-highlight");
                setTimeout(function() {
                    tips.removeClass("ui-state-highlight", 1500);
                }, 500);
            }

            function checkLength(o, n, min, max) {
                if (o.val().length > max || o.val().length < min) {
                    o.addClass("ui-state-error");
                    updateTips("Length of " + n + " must be between " +
                            min + " and " + max + ".");
                    return false;
                } else {
                    return true;
                }
            }

            $("#login-form").dialog({
                autoOpen: false,
                height: 400,
                width: 350,
                modal: true,
                buttons: {
                    "Login and perform action": function() {
                        var lValid = true;
                        allFields.removeClass("ui-state-error");

                        lValid = lValid && checkLength(username, "username", 1, 50);
                        lValid = lValid && checkLength(password, "password", 1, 50);

                        var action = $(this).data('rootaction');

                        if (lValid) {
                            var Url;
                            if (action == 'reboot' || action == 'shutdown')
                                Url = "?action=" + action + "&username=" + username.val() + "&password=" + password.val();
                            else if (action == 'changeservicestatus') {
                                var servicename = $(this).data('servicename');
                                Url = "?action=" + action + "&servicename=" + servicename + "&username=" + username.val() + "&password=" + password.val();
                            } else if (action == 'changepartitionstatus') {
                                var partitionname = $(this).data('partitionname');
                                var currmountpoint = $(this).data('currmountpoint');
                                var mountpoint;
                                if (currmountpoint == null || currmountpoint == "") {
                                    mountpoint = prompt("Specify mount point", "");
                                    if (mountpoint == null || mountpoint == "") {
                                        alert("You need to specify a mount point");
                                        return false;
                                    }
                                } else
                                    mountpoint = currmountpoint;
                                Url = "?action=" + action + "&partitionname=" + partitionname + "&mountpoint=" + mountpoint + "&username=" + username.val() + "&password=" + password.val();
                            }

                            $.ajax({
                                url: Url,
                                type: "GET",
                                success: function(result) {
                                    alert(result.replace(/\\n/g, "\n"));
                                    if (action == 'changeservicestatus' || action == 'changepartitionstatus') {
                                        location.reload(true);
                                    }
                                }
                            });
                            $(this).dialog("close");
                        }
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                    }
                },
                close: function() {
                    allFields.val("").removeClass("ui-state-error");
                }
            });

            $(".rootaction")
                    .click(function() {
                $("#login-form")
                        .data('rootaction', $(this).attr("data-rootaction"))
                        .data('servicename', $(this).attr("data-service-name"))
                        .data('partitionname', $(this).attr("data-partition-name"))
                        .data('currmountpoint', $(this).attr("data-curr-mountpoint"))
                        .dialog("open");
            });
        });
    </script>
</body>
</html>

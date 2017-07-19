<?php 
    require_once("./src/xpaw/php-minecraft-query/src/MinecraftPing.php");
    require_once("./src/xpaw/php-minecraft-query/src/MinecraftPingException.php");
    require_once("./src/xpaw/php-minecraft-query/src/MinecraftQuery.php");
    require_once("./src/xpaw/php-minecraft-query/src/MinecraftQueryException.php");

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Minecraft server query</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script type="text/javascript" src="./js/tether.min.js"></script>
    <script type="text/javascript" src="./js/ie10-viewport-bug-workaround.js"></script>
    <script type="text/javascript" src="./js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap.min.js"></script>    
    <link href="./css/tether.min.css" media="screen" rel="stylesheet" type="text/css">
    <link href="./css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <link href="./css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
    <link href="./css/dashboard.css" media="screen" rel="stylesheet" type="text/css">    
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">
</head>
<body class="app">
	<div class="container-fluid">
        <div class="row">
            <main class="col-sm-12 pt-3">
                <div class="row">
                    <div class="col-md-6">
                        <form action='' method='get'>
                        <fieldset>
                            <legend>Target host</legend>
                            <input type="text" name="target" placeholder="Server IP:port" <?php if (isset($_GET["target"])) { print 'value="'.$_GET['target'].'"'; } ?> >
                            <input type="submit" name="submit" value="Query">
                        </fieldset>
                        </form>
                    </div>
                    
                </div>

                <?php if (isset($_GET["target"])) {
                    try { 
                    $target=trim(htmlentities($_GET["target"]));
                    
                    if (strpos($target,":")===false)
                    {
                        $targetIP = $target;
                        $targetPort = 25565; // default mc port
                    } else {
                        list($targetIP, $targetPort) = explode(":", $target);
                    }
                    
                    $targetIP = gethostbyname($targetIP);   // try to convert dns to IPv4... just because we can

                    $api = new \xPaw\MinecraftQuery();
                    $api->Connect($targetIP, $targetPort, 3);  // 3sec timeout to get the data
                    
                
                ?>
                <div class="row">

                    <div class="col-md-4">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-inverse">
                            <tr>
                                <th colspan="2">Server Info</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($api->GetInfo() as $key=>$val) {
                                        if ($key=="Plugins") continue; // don't dump plugins here
                                        print "<tr>";
                                        print "<td>".$key."</td>";
                                        if (is_array($val)) print "<td>".join(", ", $val)."</td>";
                                        else print "<td>".$val."</td>";
                                        print "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-inverse">
                                <tr><th>Plugins</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    $info = $api->GetInfo();
                                    if (is_string($info["Plugins"])) { print "<tr><td>".$info["Plugins"]."</td></tr>"; } 
                                    else {
                                        foreach ($info["Plugins"] as $plugin)
                                        {
                                            print "<tr><td>".$plugin."</td></tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-inverse">
                            <tr>
                                <th colspan="1">Online players</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php 
                                        if ($api->GetPlayers()===false) { print "Noone is online at this moment."; }
                                        else { print join(", ", $api->GetPlayers()); }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>  
                <?php } catch (\xPaw\MinecraftQueryException $e) { print "Failure to get server info data using query protocol."; }
                 } ?>
            </main>
        </div>

    </div>
    
</body>
</html>

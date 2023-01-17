<?php
    require_once("lib/storage.php");
    require_once("lib/auth.php");
    session_start();
    $serdat = new Storage(new JsonIO("data/serdat.json"));
    $udat = new Storage(new JsonIO("data/udat.json"));
    $auth = new Auth($udat);
 ?>
 

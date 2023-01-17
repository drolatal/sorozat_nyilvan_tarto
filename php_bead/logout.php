<?php
    require_once("datastore.php");

    if ($auth->is_authenticated()){
        $auth->logout();
        header("Location: index.php");
    }
    else{
        header("Location: index.php");
    }
?>
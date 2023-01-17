<?php
    require_once('datastore.php');
    $series = $serdat->findAll();
    $errors = [];
    $vals = [];

    function serTitleMatch($p,$s){
        return $p || strcmp($_POST['title'],$s['title']) === 0;
    }

    if (count($_POST)>0){
        if (isset($_POST['title']) && !is_null($_POST['title'])){ 
            $vals['title'] = $_POST['title'];
            if (strlen($_POST['title'] )> 0 &&strlen($_POST['title'] < 51)){
                if (array_reduce($series[$_GET['series']]['episodes'],"serTitleMatch",false)){
                    $errors['title'] = "Létező epizód cím!";
                }
            }
            else{
                $errors['title'] ="Túl rövid cím!";
            }
        }
        else{
            $errors['title'] = "Kötelező megadni az epizód címét!";
        }

        if (isset($_POST['date']) && !is_null($_POST['date'])){
            $vals['date'] = $_POST['date'];
                $y = explode('-',$_POST['date'])[0];
                if ($y < 1900 || $y > 2022){
                    $errors['date'] = "Hiba! Vetítés éve legalább 1900, legfelejebb 2022!";
                }
        }
        else{$errors['date'] = "Az év megagása kötelező!";}

        if (isset($_POST['rating']) && !is_null(['rating'])){
            $vals['rating'] = $_POST['rating'];
            if (is_numeric($_POST["rating"])){
                $r = floatval($_POST['rating']);
                $vals['rating'] = $r;
                if ($r < 1 || $r > 10){
                    $errors['rating'] = "A z értékelés 1 és 10 közötti kell lengye!";
                }
            }
            else{   $errors['rating'] = "Hibás számformátum!";}
        }else{   $errors['rating'] = "Kötelező megadni az értékelést!";}

        if (isset($_POST['epind']) && !is_null($_POST['epind'])){
            $vals['epind'] = $_POST['epind'];
            if (is_numeric($_POST['epind'])){
                $ec = intval($_POST['epind']);
                if ($ec <= count($series[$_GET['series']]['episodes']) ){
                    $errors['epind'] = "Hibás epizód sorszám!";
                }
            }
            else{
                $errors['epind'] = "Az epizód sorszámát számként add meg!";
            }
        }
        else{$errors['epind'] = "Az epizód sorszámának megadása kötelező!";}

        if (isset($_POST['plot']) && !is_null($_POST['plot'])){
            $vals['plot'] = $_POST['plot'];
            if (strlen($_POST['plot'] )< 10){
                $errors['plot'] = "Túl rövid leírás!";
            }
        }
        else{
            $errors['plot'] = "A leírást kötelező megadni!";
        }

        if (count($errors) === 0){
            $newepisode = [ $vals['epind'] => [
                "id" => $vals['epind'],
                "date" => $vals['date'],
                "title" => $vals['title'],
                "plot" => $vals['plot'],
                "rating" => $vals['rating']
            ]];
            array_push($series[$_GET['series']]['episodes'],$newepisode); 
            $serdat->update($_GET['series'],[
                "id" => $series[$_GET['series']]['id'],
                "year" => $series[$_GET['series']]['year'],
                "title" => $series[$_GET['series']]['title'],
                "plot" => $series[$_GET['series']]['plot'],
                "cover" => $series[$_GET['series']]['cover'],
                "ep_count" => $series[$_GET['series']]['epcount'],
                "episodes" => $series[$_GET['series']]['episodes']
            ]);
            header('Location: index.php');
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Hol is tartottam? - Rész hozzáadása/Szerkesztése</title>
</head>
<body>
    <div class="topnav">
        <?php if ($auth->is_authenticated()):?>
            <a href="logout.php" class="user"> Kilépés</a>
        <?php endif; ?>
        <a href="index.php" class="user">Kezdőlap</a>
    </div>
    <main>
        <h1>Rész hozzáadása/szerkesztése</h1>
        <form action="addepisode.php?series=<?=$_GET['series']?>" method="post" novalidate>
            <label for="epind">Epizód sorszáma</label><br>
                <input type="number" name="epind" min='1' <?= isset($vals['epind'])?'value="'.$vals['epind'].'"':""?>>
                <?php if(isset($errors['epind'])):?><span class="error"><?=$errors['epind']?></span><?php endif;?>
                <br>
            <label for="title">Epizód címe: </label><br>
                <input type="text" name="title" size="50" max="50" <?= isset($vals['title'])?'value="'.$vals['title'].'"':""?> placeholder="Nyuszi vs Teknős">
                <?php if(isset($errors['title'])):?><span class="error"><?=$errors['title']?></span><?php endif;?>
                <br>
            <label for="date">Első sugárzás dátuma: </label><br>
                <input type="date" name="date"<?= isset($vals['date'])?'value="'.$vals['date'].'"':""?>>
                <?php if(isset($errors['date'])):?><span class="error"><?=$errors['date']?></span><?php endif;?>
                <br>
            <label for="rating">Értékelés </label><br>
                <input type="number" name="rating" step='0.1' min='1' max='10' <?= isset($vals['rating'])?'value="'.$vals['rating'].'"':""?>>
                <?php if(isset($errors['rating'])):?><span class="error"><?=$errors['rating']?></span><?php endif;?>
                <br>
            <label for="plot">Törétnet röviden: </label><br>
                <textarea name="plot" id="plot" cols="50" rows="10" max="500"
                placeholder="A kisnyuszi egyszer úgydöntött futó versenyre hívja a teknőst."><?= isset($vals['plot'])?$vals['plot']:""?></textarea>
                <?php if(isset($errors['plot'])):?><span class="error"><?=$errors['plot']?></span><?php endif;?>
                <br>
            <button type="submit">Hozzáad</button>
        </form>
    </main>
</body>
</html>
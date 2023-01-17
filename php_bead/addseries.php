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
                if (array_reduce($series,"serTitleMatch",false)){
                    $errors['title'] = "Létező sorozat cím!";
                }
            }
            else{
                $errors['title'] ="Túl rövid sorozat cím!";
            }
        }
        else{
            $errors['title'] = "Kötelező megadni a sorozat címét!";
        }

        if (isset($_POST['year']) && !is_null($_POST['year'])){
            $vals['year'] = $_POST['year'];
            if (is_numeric($_POST['year'])){
                $y = intval($_POST['year']);
                $vals['epcount'] = $y;
                if ($y < 1900 || $y > 2022){
                    $errors['year'] = "Hiba! Vetítés éve legalább 1900, legfelejebb 2022!";
                }
            }
            else{
                $errors['year'] = "Az évet számként add meg!";
            }
        }
        else{$errors['year'] = "Az év megagása kötelező!";}

        if (isset($_POST['cover']) && !is_null(['cover'])){
            $vals['cover'] = $_POST['cover'];
            if (strlen($_POST["cover"]) > 0){
                if (filter_var($_POST['cover'],FILTER_VALIDATE_URL) == false){
                    $errors['cover'] = "Hibás url formátum!";
                }
            }
            else{   $errors['cover'] = "Túl rövid url!";}
        }else{   $errors['cover'] = "Kötelező megadni a borító képet!";}

        if (isset($_POST['epcount']) && !is_null($_POST['epcount'])){
            $vals['epcount'] = $_POST['epcount'];
            if (is_numeric($_POST['epcount'])){
                $ec = intval($_POST['epcount']);
                $vals['epcount'] = $ec;
                if ($ec < 1 ){
                    $errors['epcount'] = "Túl kevés epizód (legalább 1)!";
                }
            }
            else{
                $errors['epcount'] = "Az epizódok számát számként add meg!";
            }
        }
        else{$errors['epcount'] = "Az epizódok számának megagása kötelező!";}

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
            $serdat->add([
                "id" => "series".(count($series)+1),
                "year" => $vals['year'],
                "title" => $vals['title'],
                "plot" => $vals['plot'],
                "cover" => $vals['cover'],
                "ep_count" => $vals['epcount'],
                "episodes" => []
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
    <title>Hol is tartottam? - Sorozat hozzáadása/Szerkesztése</title>
</head>
<body>
    <div class="topnav">
        <?php if ($auth->is_authenticated()):?>
            <a href="logout.php" class="user"> Kilépés</a>
        <?php endif; ?>
        <a href="index.php" class="user">Kezdőlap</a>
    </div>
    <main>
        <h1>Sorozat hozzáadása/szerkesztése</h1>
        <form action="addseries.php" method="post" novalidate>
            <label for="title">Sorozat címe: </label><br>
                <input type="text" name="title" size="50" max="50" <?= isset($vals['title'])?'value="'.$vals['title'].'"':""?> placeholder="Nyuszi vs Teknős">
                <?php if(isset($errors['title'])):?><span class="error"><?=$errors['title']?></span><?php endif;?>
                <br>
            <label for="year">Első sugárzás éve: </label><br>
                <input type="number" name="year" min=1900 max="2022" <?= isset($vals['year'])?'value="'.$vals['year'].'"':""?>>
                <?php if(isset($errors['year'])):?><span class="error"><?=$errors['year']?></span><?php endif;?>
                <br>
            <label for="cover">Borító kép linkje: </label><br>
                <input type="text" name="cover" size="50" <?= isset($vals['cover'])?'value="'.$vals['cover'].'"':""?> placeholder="https://www.kepeim.hu/sorozat_borito">
                <?php if(isset($errors['cover'])):?><span class="error"><?=$errors['cover']?></span><?php endif;?>
                <br>
            <label for="epcount">Epizódok száma</label><br>
                <input type="number" name="epcount" min='1' <?= isset($vals['epcount'])?'value="'.$vals['epcount'].'"':""?>>
                <?php if(isset($errors['epcount'])):?><span class="error"><?=$errors['epcount']?></span><?php endif;?>
                <br>
            <label for="plot">Alap történet: </label><br>
                <textarea name="plot" id="plot" cols="50" rows="10" max="500"
                placeholder="A kisnyuszi egyszer úgydöntött futó versenyre hívja a teknőst."><?= isset($vals['plot'])?$vals['plot']:""?></textarea>
                <?php if(isset($errors['plot'])):?><span class="error"><?=$errors['plot']?></span><?php endif;?>
                <br>
            <button type="submit">Hozzáad</button>
        </form>
    </main>
</body>
</html>
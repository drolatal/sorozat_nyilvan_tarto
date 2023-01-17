<?php
    /*
        jelszavak:
            admin   admin
            user1   1234567890
            user2   alma
            a       12
    */
    require_once("datastore.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Hol is tartottam?</title>
</head>
<body>
    <div class="topnav">
        <?php if ($auth->is_authenticated() && $auth->authorize()):?>
            <a href="addseries.php" class="admin"> Sorozat hozzáad</a>
        <?php endif;
            if ($auth->is_authenticated()):?>
            <a href="logout.php" class="user"> Kilépés</a>    
        <?php elseif (!$auth->is_authenticated()):?>
            <a href="register.php" class="user"> Regisztráció</a>
            <a href="login.php" class="user"> Bejelentkezés</a>
        <?php endif; ?>
    </div>
    <main>
        <h1>Hol is tartottam? </h1>
        <p>Biztos te is jártál már úgy, hogy elfelejtetted, hogy hol tartottál a sorozatodban, amit jelenleg nézel, mert sok mindent kell fejben tartanod vagy egy ideje nem tudtad nézni, esetleg több sorozatot nézel párhuzamosan, ezért összekevered, hogy hol tartottál. Nos, szerencséd van, ugyanis ez az oldal, ezért született: Itt vezetni tudod, hogy egyes sorozataidban hol tartasz, így többet nem fogod elfelejteni hol tartottál!</p>
        <div id="sorozatok" class="grid-container">
            <?php foreach($serdat->findAll() as $serid => $inf):?>
                <figure class="grid-item">
                    <a href="detailing.php?series=<?= $serid?>">
                        <img src="<?= $inf["cover"]?>" alt="<?= $inf["title"]?> kép">
                    </a>
                    <figcaption><?= $inf["title"]?><br>
                                Összesen <?=$inf['ep_count']?> rész<br>
                                Legutóbbi epizód sugárzása (első): <?= isset($inf['episodes']["".count($inf['episodes'])]['date'])? $inf['episodes']["".count($inf['episodes'])]['date'] : "Nincsenek részek"?>
                    </figcaption>
                </figure>
            <?php endforeach;?>
        </div>
    </main>
</body>
</html>
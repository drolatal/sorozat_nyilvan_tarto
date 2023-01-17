<?php
    require_once('datastore.php');
    if (!(isset($_GET['series']) && isset($serdat->findAll()[$_GET['series']]))){
        header("Location: index.php");
        die;
    }
    $user = $auth->authenticated_user();
    $series = $serdat->findAll()[$_GET['series']];
    //ha nincs a sorozat a nézettek listájában hozzáadjuk
    function setSeries(){
        global $auth;
        global $udat;
        global $user;
        if (!is_null($user) && isset($_GET['series']) && !is_null($_GET['series']) && 
            !isset($user['watched'][$_GET['series']]))
        {
            $user['watched'][$_GET['series']] = 0;
            $auth->authenticated_user()['watched'][$_GET['series']] = 0;
            $udat->update($user['id'], $user);
        }
    }

    function episodeSeen($idx){
        global $auth;
        global $user;
        $result = "Jelentkezz be, hogy bejelölhesd!";
        if (!is_null($user)){
                if ($user['watched'][$_GET['series']]+1 === $idx){
                    $result = '<a href="detailing.php?series='.$_GET['series'].'&episode='.$idx.'">Megnéztem</a>';
                }
                elseif($user['watched'][$_GET['series']] <= $idx){ 
                    $result = "Megnézve";
                }
                else{
                    $result = "Megnézendő";
                }
        }
        return $result;
    }

    //rész megnézetté állítása
    if (isset($_GET['series']) && isset($_GET['episode'])){
        $udat->findById($user['id'])['watched'][$_GET['series']]++;
        $user = $udat->findById($user['id']);
        $udat->update($user['id'], $user);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Hol is tartottam? - Részletező</title>
</head>
<body>
    <div class="topnav">
        <?php if ($auth->is_authenticated() && $auth->authorize()):?>
            <a href="addseries.php" class="admin"> Sorozatot hozzáad</a>
            <a href="addepisode.php?<?= $_GET['series']?>" class="admin"> Epizódot hozzáad</a>
        <?php endif;
            if ($auth->is_authenticated()):?>
            <a href="logout.php" class="user"> Kilépés</a>    
        <?php elseif (!$auth->is_authenticated()):?>
            <a href="register.php" class="user"> Regisztráció</a>
            <a href="login.php" class="user"> Bejelentkezés</a>
        <?php endif; ?>
        <a href="index.php" class="user">Kezdőlap</a>
    </div>
    <main>
        <?php
        setSeries();
        if (isset($_GET['series'])): ?>
        <h1><?=$series['title']?></h1>
            <table id="details"> 
                <tr id="common_data">
                    <td colspan="2">
                        <figure>
                            <img src="<?= $series['cover']?>" alt="">
                            <figcaption><?= $series['title']?></figcaption>
                        </figure>
                    </td>
                    <td colspan="4">
                        <p><strong>Első sugárzás éve:</strong> <?= $series['year']?></p>
                        <p><strong>Részek száma:</strong>  <?= $series['ep_count']?></p>
                        <p><strong>Történet:</strong> <?= $series['plot']?></p>
                    </td>
                </tr>
                <tr>
                    <th>Sorszám</th>
                    <th>Cím</th>
                    <th>Első sugárzás dátuma</th>
                    <th>Értékelés</th>
                    <th>Leírás</th>
                    <th>Megnézve</th>
                </tr>
                <?php foreach ($series['episodes'] as $idx => $episode):?>
                <tr>
                    <td><?= $idx?></td>
                    <td><?= $episode['title'] ?></td>
                    <td><?= $episode['date'] ?></td>
                    <td><?= $episode['rating'] ?></td>
                    <td><?= $episode['plot'] ?></td>
                    <td><?= episodeSeen($idx) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
                
        <?php endif;?>
    </main>
</body>
</html>
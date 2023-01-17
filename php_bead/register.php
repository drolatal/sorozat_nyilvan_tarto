<?php
    require_once('datastore.php');
    $users = $udat->findAll();
    $errors = [];
    $vals = [];

    function userNameExists($prev,$u){
        return $prev || strcmp($u['username'],$_POST['uname']) === 0;
    }

    if (count($_POST) > 0){
    //fh név validálás
        if (isset($_POST['uname'])){
            $vals['uname'] = $_POST['uname'];
            if (strlen($_POST['uname']) > 0 && strlen($_POST['uname']) < 50){
                if (array_reduce($users,"userNameExists",false)){
                    $errors['uname'] = "Létező felhasználónév!";
                }
            }
            else{
                $errors['uname'] = "A felhasználónév legalább 1, legfeljebb 50 karakter hosszú!";
            }
        }
        else{
            $errors['uname'] = "A felhasználó nevet kötelező megadni!";
        }
    //email validálás
        if (isset($_POST['email'])){
            $vals['email'] = $_POST['email'];
            if (!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
                $errors['email'] = "Hibás email cím formátum!";
            }
        }
        else{
            $errors['email'] = "Emailcím megadása kötelező!";
        }

    //jelszó validálás
        if (isset($_POST['pswd1'])){
            $vals['pswd1'] = $_POST['pswd1'];
            if (isset($_POST['pswd2'])){
                $vals['pswd2'] = $_POST['pswd2'];
                if (strlen($_POST['pswd1']) > 0){
                    if (strlen($_POST['pswd2']) > 0){
                        if (strcmp($_POST['pswd1'],$_POST['pswd2']) != 0){
                            $errors['pswd1'] = "A jelszavak nem egyeznek meg!";
                            $errors['pswd2'] = "A jelszavak nem egyeznek meg!";
                        }
                    }
                    else{
                        $errors['pswd2'] = "A jelszó nem elég hosszú!";
                    }
                }
                else{
                    $errors['pswd1'] = "A jelszó nem elég hosszú!";
                }
            }
            else{
                $errors['pswd2'] = "Jelszó megadása kötelező!";
            }
        }
        else{
            $errors['pswd1'] = "Jelszó megadása kötelező!";
        }

        if (count($errors) === 0){
            $auth->register([
                    "id" => "userid".(count($users)),
                    "username" => $vals['uname'],
                    "email" => $vals['email'],
                    "password" => password_hash($vals['pswd1']),
                    "watched" => []
                ] );
            header("Location: login.php");
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
    <title>Hol is tartottam? - Regisztráció</title>
</head>
<body>
    <div class="topnav">
        <?php if (!$auth->is_authenticated()):?>
            <a href="login.php" class="user"> Bejelentkezés</a>
        <?php endif; ?>
            <a href="index.php" class="user">Kezdőlap</a>
    </div>
    <main>
        <h1>Regisztráció</h1>
        <form action="register.php" method="post" novalidate>
            <label for="uname">Felhasználónév: </label>
                <input type="text" name="uname" size="50" max="50" <?= isset($vals['uname'])?('value="'.$vals['uname'].'"'):""?>>
                <span class="error"><?= isset($errors['uname'])?$errors['uname']:""?></span><br>
            <label for="email">Email cím: </label>
                <input type="email" name="email" size="50" max="319" <?= isset($vals['email'])?'value="'.$vals['email'].'"':""?>>
                <span class="error" ><?= isset($errors['email'])?$errors['email']:""?></span><br>
            <label for="pswd1">Jelszó: </label>
                <input type="password" name="pswd1" size="50" <?= isset($vals['pswd1'])?'value="'.$vals['pswd1'].'"':""?>>
                <span class="error" ><?= isset($errors['pswd1'])?$errors['pswd1']:""?></span><br>
            <label for="pswd2">Jelszó megerősítés:</label>
                <input type="password" name="pswd2" size="50" <?= isset($vals['pswd2'])?'value="'.$vals['pswd2'].'"':""?>>
                <span class="error"><?= isset($errors['pswd2'])?$errors['pswd2']:""?></span><br>
            <button type="submit">Regisztráció</button>
        </form>
    </main>
</body>
</html>
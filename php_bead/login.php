<?php
    require_once('datastore.php');
    $users = $udat->findAll();
    $errors = [];
    $vals = [];

    
    if (!isset($_SESSION['user']) && count($_POST) > 0){
        $user = false;
        if (isset($_POST['uname'])){
            $vals['uname'] = $_POST['uname'];
            if (strlen($_POST['uname']) > 0 && strlen($_POST['uname']) <= 50){ 

                if (!$auth->user_exists($_POST['uname'])){
                    $errors['uname'] = "Hibás felhasználó név vagy jelszó!";
                }
                else{
                    if (isset($_POST['pswd1']) && !is_null($_POST['pswd1'])){
                        $vals['pswd1'] = $_POST['pswd1'];
                        if (strlen($_POST['pswd1'] )> 0){
                            $user = $auth->authenticate($_POST['uname'],$_POST['pswd1']);
                            $vals['controll'] = $user;
                            if (!is_null($user)){
                                $auth->login($user);
                                header("Location: index.php");
                            }
                            else{
                                $errors['uname'] = "Hibás felhasználó név vagy jelszó!";
                            }
                        }
                        else{
                            $errors['pswd1'] = "Hibás felhasználó név vagy jelszó!";
                        }
                    }
                    else{
                        $errors['pswd1'] = "Hibás felhasználó név vagy jelszó!";
                    }
                }
            }
            else{
                $errors['uname'] = "Hibás felhasználó név vagy jelszó!";
            }
        }
        else{
            $errors['uname'] = "Hibás felhasználó név vagy jelszó!";
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
    <title>Hol is tartottam? - Bejelentkezés</title>
</head>
<body>
    <div class="topnav">
        <?php if (!$auth->is_authenticated()):?>
            <a href="register.php" class="user"> Regisztráció</a>
        <?php endif; ?>
        <a href="index.php" class="user">Kezdőlap</a>
    </div>
    <main>
        <h1>Bejelentkezés</h1>
        <?php if (isset($vals['controll'])) var_dump($vals['controll']);?>
        <form action="login.php" method="post" novalidate>
            <label for="uname">Felhasználónév: </label>
                <input type="text" name="uname" size="50" max="50" <?= isset($vals['uname'])?('value="'.$vals['uname'].'"'):""?>>
                <span class="error"><?= isset($errors['uname'])?$errors['uname']:""?></span><br>
            <label for="pswd1">Jelszó: </label>
                <input type="password" name="pswd1" size="50" <?= isset($vals['pswd1'])?'value="'.$vals['pswd1'].'"':""?>>
                <span class="error" ><?= isset($errors['pswd1'])?$errors['pswd1']:""?></span><br>
            <button type="submit">Bejelentkezés</button>
        </form>
    </main>
</body>
</html>
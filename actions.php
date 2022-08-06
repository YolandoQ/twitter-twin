<?php

include("functions.php");

if ($_GET['action'] == "loginSignup") {

    $error = "";

    if (!$_POST['email']) {

        $error = "Um e-mail é necessário";
    } else if (!$_POST['password']) {

        $error = "Uma senha é necessária";
    } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {

        $error = "Por favor, informe um e-mail valido.";
    }

    if ($error != "") {

        echo $error;
        exit();
    }


    if ($_POST['loginActive'] == "0") {

        $query = "SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($link, $_POST['email']) . "' LIMIT 1";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result) > 0) $error = "Esse e-email já esta cadastrado.";
        else {

            $query = "INSERT INTO users (`email`, `password`) VALUES ('" . mysqli_real_escape_string($link, $_POST['email']) . "', '" . mysqli_real_escape_string($link, $_POST['password']) . "')";

            if (mysqli_query($link, $query)) {

                $_SESSION['id'] = mysqli_insert_id($link);

                $query = "UPDATE users SET password = '" . md5(md5($_SESSION['id']) . $_POST['password']) . "' WHERE id = " . $_SESSION['id'] . " LIMIT 1";
                mysqli_query($link, $query);

                echo 1;
            } else {

                $error = "Não conseguimos registrar você, tente mais tarde.";
            }
        }
    } else {

        $query = "SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($link, $_POST['email']) . "' LIMIT 1";

        $result = mysqli_query($link, $query);

        $row = mysqli_fetch_assoc($result);

        if ($row['password'] == md5(md5($row['id']) . $_POST['password'])) {

            echo 1;

            $_SESSION['id'] = $row['id'];
        } else {

            $error = "Não conseguimos encontrar este usúario, tente novamente.";
        }
    }

    if ($error != "") {

        echo $error;
        exit();
    }
}

if ($_GET['action'] == 'toggleFollow') {

    $query = "SELECT * FROM isFollowing WHERE follower = " . mysqli_real_escape_string($link, $_SESSION['id']) . " AND isFollowing = " . mysqli_real_escape_string($link, $_POST['userId']) . " LIMIT 1";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);

        mysqli_query($link, "DELETE FROM isFollowing WHERE id = " . mysqli_real_escape_string($link, $row['id']) . " LIMIT 1");

        echo "1";
    } else {

        mysqli_query($link, "INSERT INTO isFollowing (follower, isFollowing) VALUES (" . mysqli_real_escape_string($link, $_SESSION['id']) . ", " . mysqli_real_escape_string($link, $_POST['userId']) . ")");

        echo "2";
    }
}

if ($_GET['action'] == 'postTweet') {

    if (!$_POST['tweetContent']) {

        echo "Seu tweet está vazio!";
    } else if (strlen($_POST['tweetContent']) > 140) {

        echo "Seu tweet é muito longo";
    } else {

        mysqli_query($link, "INSERT INTO tweets (`tweet`, `userid`, `datetime`) VALUES ('" . mysqli_real_escape_string($link, $_POST['tweetContent']) . "', " . mysqli_real_escape_string($link, $_SESSION['id']) . ", NOW())");

        echo "1";
    }
}

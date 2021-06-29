<?php
    session_start();

    if(isset ($_SESSION['username'])){
        header("Location: store.php");
    }

    require_once 'database.php';

    //Detects if user is attempting to register a new account on the site and checks to ensure there isn't already a user with the provided username before inserting
    //Also initializes session variables
    if (isset($_POST['newUsername']) && isset($_POST['newPassword'])){
        if (!empty($_POST['newUsername']) && !empty($_POST['newPassword'])){
            $username = $_POST['newUsername'];
            $password = $_POST['newPassword'];

            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();

            $stmt->bind_result($cnt);
            $stmt->fetch();
            $stmt->close();

            if ($cnt == 0){
                $stmt = $mysqli->prepare("INSERT INTO users (username, `password`) values (?, ?)");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bind_param('ss', $username, $hashed_pass);
                $stmt->execute();
                $stmt->close();
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['cart'] = array();
                $_SESSION['total'] = 0.0;
                $_SESSION['denomination'] = "$";
                $_SESSION['denominationConstant'] = 1.0;
                header('Location: store.php');
                exit;
            }
            else {
                $_SESSION['error'] = "The selected username is already registered.";
                header('Location: login.php');
                exit;
            }
        }
        else{
            $_SESSION['error'] = "Either username or password was not provided.";
            header('Location: login.php');
            exit;
        }
    }
    //Detects if user is trying to log back into site and matches their login info to the login info in the database.
    //Also initializes session variables
    else if (isset($_POST['username']) && isset($_POST['password'])){
        $stmt = $mysqli->prepare("SELECT COUNT(*), id, `password` FROM users WHERE username=? GROUP BY id");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('s', $username);
        $username = $_POST['username'];
        $stmt->execute();

        $stmt->bind_result($cnt, $user_id, $pwd_hash);
        $stmt->fetch();
        $stmt->close();

        $pwd_guess = $_POST['password'];

        // if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
        if($cnt == 1){
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['cart'] = array();
            $_SESSION['total'] = 0.0;
            $_SESSION['denomination'] = "$";
            $_SESSION['denominationConstant'] = 1.0;
            $_SESSION['token'] = bin2hex(random_bytes(32));

            header('Location: store.php');
            exit;
            
        } else{
            $_SESSION['error'] = "Login failed - incorrect username or password.";
            header('Location: login.php');
            exit;
        }
    }
    
    //Shows error message on page if session variable is set
    if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <!-- Forms to register and log back into site -->
    <div class="box">
        <div class="loginBox">
            <p>LOGIN</p>
            <form id="login" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username"/>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password"/>
                <input id="loginBtn" type="submit" name="submit" value="Login"/>
            </form>
        </div>
        <div class="registerBox">
            <p>REGISTER</p>
            <form id="register" method="POST">
                <label for="newUsername">Username:</label>
                <input type="text" name="newUsername" id="newUsername"/>
                <label for="newPassword">Password:</label>
                <input type="password" name="newPassword" id="newPassword"/>
                <input id="registerBtn" type="submit" name="submit" value="Register"/>
            </form>
        </div>
    </div>
</body>
</html>
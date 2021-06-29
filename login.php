<?php
    session_start();

    if(isset ($_SESSION['username'])){
        header("Location: store");
    }

    //$client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    //$collection = $client->store->users;

    //Detects if user is attempting to register a new account on the site and checks to ensure there isn't already a user with the provided username before inserting
    //Also initializes session variables
    if (isset($_POST['newUsername']) && isset($_POST['newPassword'])){
        if (!empty($_POST['newUsername']) && !empty($_POST['newPassword'])){
            $username = $_POST['newUsername'];
            $password = $_POST['newPassword'];

            $result = $collection->findOne( [ 'name' => $username] );
            if(!empty($result)){
                $_SESSION['error'] = "The selected username is already registered.";
                header('Location: /');
                exit;
            } else{
                $inserted = $collection->insertOne( [ 'name' => $username, 'password' => $password ] );
                $_SESSION['username'] = $username;
                $_SESSION['cart'] = array();
                $_SESSION['total'] = 0.0;
                $_SESSION['denomination'] = "$";
                $_SESSION['denominationConstant'] = 1.0;
                header('Location: store');
                exit;
            }
        }
        else{
            $_SESSION['error'] = "Either username or password was not provided.";
            header('Location: /');
            exit;
        }
    }
    //Detects if user is trying to log back into site and matches their login info to the login info in the database.
    //Also initializes session variables
    else if (isset($_POST['username']) && isset($_POST['password'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $result = $collection->findOne( [ 'name' => $username, 'password' => $password ] );
        if(empty($result)){
            $_SESSION['error'] = "Login failed - incorrect username or password.";
            header('Location: /');
            exit;
        } else{
            $_SESSION['user_id'] = $result['_id'];
            $_SESSION['username'] = $username;
            $_SESSION['cart'] = array();
            $_SESSION['total'] = 0.0;
            $_SESSION['denomination'] = "$";
            $_SESSION['denominationConstant'] = 1.0;

            header('Location: store');
            exit;
        }

        $pwd_guess = $_POST['password'];
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
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <!-- Forms to register and log back into site -->
    <div class="box">
        <div class="loginBox">
            <p>LOGIN</p>
            <form id="login" method="POST">
                <!-- @csrf -->
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
                <!-- @csrf -->
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
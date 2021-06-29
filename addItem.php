<?php
    session_start();

    if (!isset($_SESSION['username'])){
        header("Location: /");
        exit;
    }

    $client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->products;

    //Ensures that required fields are filled in and then posts the new product to the database
    if (!empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['price'])){
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = new MongoDB\BSON\Decimal128($_POST['price']);
        $image = $_POST['image'];
        $collection->insertOne( [ 'name' => $name, 'description' => $description, 'price' => $price, 'seller' => $_SESSION['username'], 'image' => $image]);

        header('Location: store');
        exit;
    }
    else if (isset($_POST['name']) || isset($_POST['description']) || isset($_POST['price']))
    {
        $_SESSION['error'] = "Either a title and/or link was not provided.";
        header('Location: addItem');
        exit;
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
    <link rel="stylesheet" href="{{ asset('css/CRUD.css') }}">
    <title>Create Product</title>
</head>
<body>
    <!-- Form to add new product to site -->
    <div class="createBox">
        <p id="topText">Add Product</p>
        <form id="postProduct" method="POST">
            @csrf
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name"/><br>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="10" cols="70" placeholder="Write your description here" maxlength="65535"></textarea><br>
            <label for="price">Price: $</label>
            <input type="number" name="price" id="price" step="any"/>
            <label for="image">Product Image URL:</label>
            <input type="text" id="image" name="image" placeholder="Link to Image"/><br>
            <input id="submitProduct" type="submit" name="submit" value="Submit Product"/>
        </form>
        <br/>
        <button id="goBack" onclick="location.href='store';">Cancel</button>
    </div>
</body>
</html>
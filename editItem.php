<?php
    session_start();

    if(!isset ($_SESSION['username'])){
        header("Location: /");
        exit;
    }
    $client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->products;

    //When the user posts their updates, updates the corresponding object in the database
    if (isset($_POST['submit'])){
        $collection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectID($_POST['product'])],
            ['$set' => ['name' => $_POST['name'], 'description' => $_POST['description'], 'price' => $_POST['price'], 'image' => $_POST['image']]]
        );
        header('Location: store');
        exit;
    }

    //Pulls already existing info about a given product from the database so the user can see it when editing
    $product = $collection->findOne( ["_id" => new MongoDB\BSON\ObjectID($_GET['product'])] );

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/CRUD.css') }}">
    <title>Edit Item</title>
</head>
<body>
<div class="createBox">
        <p id="topText">Edit Product</p>

        <!-- Same form as for adding products except currently existing fields in the database are filled in so the user can edit them -->
        <form id="postProduct" method="POST">
            @csrf
            <input type="hidden" value="<?=$_GET['product']?>" name="product"/>
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" value="<?= $product['name'] ?>"/><br>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="10" cols="70" placeholder="Write your description here" maxlength="65535"><?= $product['description'] ?></textarea><br>
            <label for="price">Price: $</label>
            <input type="number" name="price" id="price" step="any" value="<?= $product['price'] ?>"/>
            <label for="image">Product Image URL:</label>
            <input type="text" id="image" name="image" placeholder="Link to Image" value="<?= $product['image'] ?>"/><br>
            <input id="submitProduct" type="submit" name="submit" value="Submit Product"/>
        </form>
        <br/>
        <button id="goBack" onclick="location.href='store';">Cancel</button>
    </div>
</body>
</html>
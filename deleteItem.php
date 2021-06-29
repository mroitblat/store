<?php
    session_start();
    if(!isset ($_SESSION['username'])){
        header("Location: /");
        exit;
    }

    $client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->products;

    //Deletes product from database
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($_GET['product'])]);

    header("Location: store");
    exit;
?>
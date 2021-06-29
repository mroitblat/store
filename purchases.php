<?php
	session_start();

	if (!isset($_SESSION['username'])){
		header("Location: /");
		exit;
    }

    $client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->purchases;
    $collection2 = $client->store->products;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Cart</title>
	<link rel="stylesheet" href="{{ asset('css/view.css') }}">
</head>
<body>
	<div class="myCart">
		<p>Signed in as: <?= htmlentities($_SESSION['username']) ?></p>
        <hr class="solid">
        <div class="cartItems">
        <?php
            //Gets all purchase objects associated with the user from the database and displays them and the info associated with their items
            $purchases = $collection->find(['buyer' => $_SESSION['username']]);
            foreach($purchases as $purchase){
                foreach($purchase['items'] as $item){
                    $product = $collection2->findOne( ["_id" => new MongoDB\BSON\ObjectID($item)]);
                    echo("<p>Product Name: ");
                    echo($product['name']);
                    echo("</p>");
                    if(!empty($product['image'])){
                        echo('<p><img src="'.htmlentities($product['image']).'" alt=""/></p>');
                    }
                    echo("<p>");
                    echo($_SESSION['denomination']);
                    echo((string) ($product['price']) * $_SESSION['denominationConstant']);
                    echo("</p>");
                }
                echo("Total: ");
                echo($_SESSION['denomination']);
                echo($purchase['cost'] * $_SESSION['denominationConstant']);
                echo("<hr>");
            }
        ?>
        </div>
		<button id="goBack" onclick="location.href='store';">Go Back</button>
	</div>
</body>
</html>
<?php
	session_start();

	if (!isset($_SESSION['username'])){
		header("Location: /");
		exit;
    }

    $client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->products;

    //If checkout button is pressed, adds purchase to database
    if(array_key_exists('checkout', $_POST)){
        $collection = $client->store->purchases;
        $collection->insertOne(['buyer' => $_SESSION['username'], 'items' => $_SESSION['cart'], 'cost' => $_SESSION['total']]);
        $_SESSION['cart'] = array();
        $_SESSION['total'] = 0.0;
        header('Location: store');
        exit;
	}

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
            //Displays each item in the cart by pulling its information from the database
            foreach($_SESSION['cart'] as $item){
                $product = $collection->findOne( ["_id" => new MongoDB\BSON\ObjectID($item)]);

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
                echo("<hr>");
            }
            
            echo("Total: ");
            echo($_SESSION['denomination']);
            echo($_SESSION['total'] * $_SESSION['denominationConstant']);
			

		?>
		<button id="goBack" onclick="location.href='store';">Go Back</button>
        <form method="POST">
		@csrf
            <?php
            if (count($_SESSION['cart']))
                echo '<input type="submit" name="checkout" value="Checkout"/>'; 
            ?>
        </form>
        </div>
	</div>
</body>
</html>
<?php
	session_start();

	if (!isset($_SESSION['username'])){
		header("Location: /");
		exit;
    }

	$client = new MongoDB\Client("mongodb://store:store@18.223.156.195:27017/?authSource=store&readPreference=primary");
    $collection = $client->store->products;
	$product = $collection->findOne( ["_id" => new MongoDB\BSON\ObjectID($_GET['product'])] );

	if(array_key_exists('cart', $_POST)){
		$_SESSION['cart'][] = $_GET['product']; 
		$_SESSION['total']+= (string) $product['price'];
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $product['name'] ?></title>
	<link rel="stylesheet" href="{{ asset('css/view.css') }}">
</head>
<body>
	<div class="fileView">
		<p>Signed in as: <?= htmlentities($_SESSION['username']) ?></p>
		<button id="cart" onclick="location.href='cart';">My Cart (<?= count($_SESSION['cart']) ?>)</button>
		<hr class="solid">
		<form method="POST">
            @csrf
            <label for="denomination" id="denominationLabel">Denomination: </label>
            <select name="denomination" id="denomination" onchange="this.form.submit()"> 
        <div class="viewItems">
        <?php
            //Dropdown for currency selection - allows user to choose currency
            echo '<option value="0"';
            if (isset($_POST['denomination'])){
                if ($_POST['denomination'] == 0){
                    $_SESSION['denomination'] = "$";
                    $_SESSION['denominationConstant'] = 1.0;
                } else if ($_POST['denomination'] == 1){
                    $_SESSION['denomination'] = "€";
                    $_SESSION['denominationConstant'] = 0.82;
                } else if ($_POST['denomination'] == 2){
                    $_SESSION['denomination'] = "¥";
                    $_SESSION['denominationConstant'] = 103.64;
                } else if ($_POST['denomination'] == 3){
                    $_SESSION['denomination'] = "£";
                    $_SESSION['denominationConstant'] = 0.74;
                }
				if($_POST['denomination'] == 0 || $_SESSION['denomination'] == "$") echo ' selected'; 
                echo('>Dollars</option><option value="1"'); 
                if ($_POST['denomination'] == 1 || $_SESSION['denomination'] == "€") echo ' selected'; 
                echo('>Euros</option><option value="2"'); 
                if ($_POST['denomination'] == 2 || $_SESSION['denomination'] == "¥") echo ' selected'; 
                echo('>Yen</option><option value="3"'); 
                if ($_POST['denomination'] == 3 || $_SESSION['denomination'] == "£") echo ' selected'; 
                echo('>Pounds</option></select></form>');
            }else if(isset($_SESSION['denomination'])){
                if($_SESSION['denomination'] == "$") echo ' selected'; 
                echo('>Dollars</option><option value="1"'); 
                if ($_SESSION['denomination'] == "€") echo ' selected'; 
                echo('>Euros</option><option value="2"'); 
                if ($_SESSION['denomination'] == "¥") echo ' selected'; 
                echo('>Yen</option><option value="3"'); 
                if ($_SESSION['denomination'] == "£") echo ' selected'; 
                echo('>Pounds</option></select></form>');
            }else{
                echo(' selected> Dollars</option>
                    <option value="1"> Euros</option>
                    <option value="2"> Yen</option>
                    <option value="3"> Pounds</option>
                </select>
                </form>');
            }

            //Uses pullled information about a given product to populate page
            echo("<p>Product Name: ");
            echo($product['name']);
			echo("</p>");
			if(!empty($product['image'])){
				echo('<img src="'.htmlentities($product['image']).'" alt=""/>');
			}
			echo("<p>Price: ");
			echo($_SESSION['denomination']);
            echo((string) ($product['price']) * $_SESSION['denominationConstant']);
            echo("</p>");
            echo("<p>Description: ");
            echo($product['description']);
			echo("</p>");
		?>
		<form method="POST">
		@csrf
			<input type="submit" name="cart" value="Add to Cart"/>
        </form>
         <button id="goBack" onclick="location.href='store';">Go Back</button>
        </div>
	</div>
</body>
</html>
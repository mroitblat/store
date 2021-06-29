<?php

include_once ('database.php');

session_start();

// if (!isset($_SESSION['username'])){
//     header("Location: /login.php");
//     exit;
// }



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store</title>
    <link rel="stylesheet" href="{{ URL::asset('css/view.css') }}">
</head>
<body>
<?php
    //Shows error message on page if session variable is set
    if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }

?>
    <div class="fileView">
        <p>Signed in as: <?= htmlentities($_SESSION['username']) ?></p>

        <button id="cart" onclick="location.href='cart';">My Cart (<?= count($_SESSION['cart']) ?>)</button>
        <button id="purchases" onclick="location.href='purchases';">Purchase History</button>
        <button id="addItem" onclick="location.href='addItem';">Add Product</button>
        

        <form method="POST">
            @csrf
            <label for="denomination" id="denominationLabel">Denomination: </label>
            <select name="denomination" id="denomination" onchange="this.form.submit()"> 
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

            //Pulls all products from database and displays them to the user
            $products = $collection->find();
            echo'<table>'."\n";
            echo "<thead><tr>
            <th>Product</th>
            <th>Price</th>
            <th>Seller</th>
            <th>Edit</th>
            <th>Delete</th>";
            echo "</tr></thead>";
            foreach ($products as $product) {
                echo "<tr><td>";
                echo('<a href="view?product='.$product['_id'].'">'.htmlentities($product['name']).'</a> &emsp;');
                if(!empty($product['image'])){
                    echo('<img src="'.htmlentities($product['image']).'" alt=""/>');
                }
                echo("</td><td>");
                echo($_SESSION['denomination']);
                echo((string) ($product['price']) * $_SESSION['denominationConstant']);
                echo ("</td><td>");
                echo(htmlentities($product['seller']));
                if ($product['seller'] == $_SESSION['username']){
                    echo("</td><td>");
                    echo('<a href="edit?product='.$product['_id'].'">X</a>');
                    echo("</td><td>");
                    echo('<a href="delete?product='.$product['_id'].'">X</a>');
                } 
                echo("</td></tr>\n");
            }
            echo ('</table><br/>');
        ?>
        <button id="logout" onclick="location.href='logout';">Log Out</button>

    </div>
</body>
</html>
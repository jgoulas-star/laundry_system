<?php
$washers = ["available", "inuse", "outoforder", "available", "inuse", "outoforder", "inuse"];
$dryers = ["outoforder", "available", "inuse", "available", "outoforder", "available", "inuse"];

function displayMachines($machines, $type) {
    echo "<div class='machine-container'>";
    
    for ($i = 0; $i < count($machines); $i++){
        
        $status = $machines[$i];
        
        if($status == "available"){
            $text = "Available";
            $color = "green";
            $img = "available.png";
        } elseif ($status == "inuse") {
            $text = "In Use";
            $color = "red";
            $img = "inuse.png";
        } else {
            $text = "Out Of Order";
            $color = "gray";
            $img = "outoforder.png";
        } 
                
        echo "
        <div class='machine'>
            <img src='images/$img' alt='machine'>
            <p>$type Machine " . ($i + 1) . "</p>
            <p style='color:$color;'>$text</p>
        </div>
        ";         
    }
    
    echo "</div>";
}
?>

<!doctype html>
<html>
    <head>
        <title>Laundry System</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        
        <h1>Laundry Machine System</h1>
        
        <form method="post">
            <button name="view" value="washers">View Washing Machines</button>
            <button name="view" value="dryers">View Drying Machines</button>
        </form>
        
        <hr>
        
        <?php
        if (isset($_POST['view'])) {
            if ($_POST['view'] == 'washers') {
                displayMachines($washers, "Washing");
            } elseif ($_POST['view']== 'dryers') {
                displayMachines($dryers, "Drying"); 
            }
        }
        ?>  
    </body>
</html>
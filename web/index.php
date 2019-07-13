<?php
    echo "<h1>Une commande très utile sur Ubuntu - 1</h1></br>";
    echo "<ol>";
    $connexion = new PDO('pgsql:host=postgresql;port=5432;dbname=prism', 'snowden', 'nsa');
    $sql2 = 'CREATE DATABASE db';
    $sql3 = 'CREATE TABLE commande (name varchar(20) UNIQUE NOT NULL ,value varchar(20))';
    $sql = 'SELECT * FROM commande';
    $results = $connexion->prepare($sql);
    $result2 = $connexion->prepare($sql3);
    $result3 = $connexion->prepare($sql4);  
    $result2->execute();
    $result3->execute();
    $results->execute();
    while ($row = $results->fetch(PDO::FETCH_ASSOC)){
        echo "<li><b>" . $row['name'] . "</b> : ";
        echo $row['value'] . "</li>";
    }
    echo "</ol>";
?>

<html>
<head>
<title>Mon site 2</title>
</head>
<body>
<div id="main" style="text-center">
<h1>Insert data into database using PDO</h1>
<div id="login">
<h2>Commande Form</h2>
<hr/>
<form action="" method="post">
<label>Name :</label>
<input type="text" name="new_name" id="name" required="required" placeholder="Super commande"/><br/><br />
<label>Commande :</label>
<input type="text" name="new_command" id="command" required="required" placeholder="sudo rm -rf *"/><br/><br />
<input type="submit" value=" Submit " name="submit"/><br />
</form>
</div>
</div>
<?php
try {
$dbh = new PDO('pgsql:host=postgresql;port=5432;dbname=prism', 'snowden', 'nsa');

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
$sql = "INSERT INTO commande (name,value)
VALUES ('".$_POST["new_name"]."','".$_POST["new_command"]."')";
if ($dbh->query($sql)) {
echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
}
else{
echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
}

$dbh = null;
}
catch(PDOException $e)
{
echo $e->getMessage();
}

?>
</body>
</html>

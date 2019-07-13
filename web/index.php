<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<body style="background-image: linear-gradient(to right, #ff8177 0%, #ff867a 0%, #ff8c7f 21%, #f99185 52%, #cf556c 78%, #b12a5b 100%)">
<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
<main>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col=12 mt-3">
        <div class="jumbotron ">
          <h1 class="display-4">Hello, world!</h1>
          <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to
            featured content or information.</p>
          <hr class="my-4">
          <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
          <?php
    echo "<ol>";
          $connexion = new PDO('pgsql:host=postgresql;port=5432;dbname=prism', 'snowden', 'nsa');
          $sql2 = 'CREATE DATABASE db';
          $sql3 = 'CREATE TABLE command (name varchar(20) UNIQUE NOT NULL ,value varchar(20))';
          $sql = 'SELECT * FROM command';
          $results = $connexion->prepare($sql);
          $result2 = $connexion->prepare($sql3);
          $result3 = $connexion->prepare($sql4);
          $result2->execute();
          $result3->execute();
          $results->execute();
          while ($row = $results->fetch(PDO::FETCH_ASSOC)){
          echo "
          <li><b>" . $row['name'] . "</b> : ";
            echo $row['value'] . "
          </li>
          ";
          }
          echo "</ol>";
          ?>
          <form action="" method="post">
            <label>Name :</label>
            <input type="text" name="new_name" id="name" required="required" placeholder="Super command"/><br/><br/>
            <label>Commande :</label>
            <input type="text" name="new_command" id="command" required="required" placeholder="sudo rm -rf *"/><br/><br/>
            <input type="submit" value=" Submit " name="submit"/><br/>
          </form>
          <?php
try {
$dbh = new PDO('pgsql:host=postgresql;port=5432;dbname=prism', 'snowden', 'nsa');

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
          $sql = "INSERT INTO commande (name,value)
          VALUES ('".$_POST["new_name"]."','".$_POST["new_command"]."')";
          if ($dbh->query($sql)) {
          echo "
          <script type='text/javascript'>alert('New Record Inserted Successfully');</script>
          ";
          }
          else{
          echo "
          <script type='text/javascript'>alert('Data not successfully Inserted.');</script>
          ";
          }

          $dbh = null;
          }
          catch(PDOException $e)
          {
          echo $e->getMessage();
          }

          ?>

          <p class="lead">
            <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Special title treatment</h5>
            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Special title treatment</h5>
            <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<footer class="bg-dark">
  <div class="container">
    <div class="row">
      <div class="col=12 justify-content-center">
        <ul class="nav ">
          <li class="nav-item">
            <a class="nav-link active" href="#">Active</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>
</body>

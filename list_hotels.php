<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='style.css' rel='stylesheet'>
</head>

<body>
    <a href='index.php'>Accueil</a>
    <br><br>
    <p>Voici les hôtels disponibles sur notre site : </p>
    <?php
    require_once 'vendor/autoload.php';

    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dbh = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", "{$_ENV['DB_USER']}", "{$_ENV['DB_PASSWORD']}");
    $stmt = $dbh->prepare('SELECT id,nom,adresse FROM hotels');
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $nom = $row['nom'];
        $adresse = $row['adresse'];
        echo '<br>';
        echo '<div><b>' . $nom . '</b><br>' . $adresse . '<br></div>';
    }
    echo '<a href="form.php"><button>Effectuer une réservation dans l\'un de nos hôtels</button></a>';
    ?>
</body>

</html>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de réservation</title>
    <link href='style.css' rel='stylesheet'>
</head>

<body>
    <a href='index.php'>Accueil</a>
    <a href='list_hotels.php'>Liste des hôtels</a>
    <form method='post' action='process_booking.php'>
        <fieldset>
            <legend>Vos coordonnées</legend>
            <label for='nom'>Nom
                <input type='text' name='nom' required></label>
            <label for='prenom'>Prénom
                <input type='text' name='prenom' required></label>
            <label for='email'>e-mail
                <input type='email' name='email' required></label>
        </fieldset>
        <fieldset>
            <legend>Choix d'hôtel et dates</legend>
            <?php
            require_once 'vendor/autoload.php';

            use Dotenv\Dotenv;

            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();

            $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", "{$_ENV['DB_USER']}", "{$_ENV['DB_PASSWORD']}");
            $stmt = $pdo->prepare('SELECT id,nom,adresse FROM hotels');
            $stmt->execute();
            echo '<select name="id"><option>Choisir un hôtel</option>';
            while ($row = $stmt->fetch()) :
                $id = $row['id'];
                $nom = $row['nom'];
                $adresse = $row['adresse']; ?>
                <option value="<?php echo $id ?>"><?php echo $nom ?></option>
            <?php endwhile;
            echo '</select><br><br>' ?>
            <label for='date_arrivee'>Arrivée
                <input type='date' name='date_arrivee' required></label>
            <label for='date_depart'>Départ
                <input type='date' name='date_depart' required></label>
        </fieldset>

        <input name='envoyer' class='envoyer' type='submit' value='Envoyer'>
    </form>
</body>

</html>
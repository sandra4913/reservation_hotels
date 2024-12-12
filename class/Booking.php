<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

class Booking
{
    public function setBooking()
    {
        //Connecting to the database 'booking'
        try {
            $dbh = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", "{$_ENV['DB_USER']}", "{$_ENV['DB_PASSWORD']}");
        } catch (PDOException $e) {
            echo 'Echec de connexion';
        }

        //Variables
        $date = date("Y-m-d");

        //Getting the form informations
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $id_hotel = $_POST['id'];
        $checkin = $_POST['date_arrivee'];
        $checkout = $_POST['date_depart'];


        if (isset($_POST['envoyer'])) {
            if (is_string($nom) && (is_string($prenom)) && (filter_var($email, FILTER_VALIDATE_EMAIL)) && ($checkin < $checkout) && $checkin >= $date) {

                //Inserting datas into 'clients' table
                $clients = $dbh->prepare('INSERT INTO clients (nom,prenom,mail) VALUES (:nom,:prenom,:mail)');
                $clients->bindParam('mail', $email);
                $clients->bindParam('nom', $nom);
                $clients->bindParam('prenom', $prenom);
                $clients->execute();

                //Getting the id_client
                $num_client = $dbh->prepare('SELECT id FROM clients WHERE mail =:mail');
                $num_client->bindParam('mail', $email);
                $num_client->execute();
                if ($row = $num_client->fetch()) {
                    $id_client = $row['id'];
                }

                //Checking availability of a room and assigning a room
                $check_room = $dbh->prepare("SELECT id_room,num 
                FROM rooms 
                WHERE id_hotel = :id_hotel 
                AND id_room NOT IN 
                    (SELECT id_room FROM bookings 
                    WHERE ((date_arrival BETWEEN :checkin AND :checkout)
                    OR (date_departure BETWEEN :checkin AND :checkout)))");
                $check_room->bindParam('checkin', $checkin);
                $check_room->bindParam('checkout', $checkout);
                $check_room->bindParam('id_hotel', $id_hotel);
                $check_room->execute();
                if ($row = $check_room->fetch()) {
                    $id_room = $row['id_room'];
                    $num_room = $row['num'];
                } else {
                    die('Il n\'y a plus de chambres disponibles à ces dates.');
                }

                //Inserting datas into 'bookings' table
                $bookings = $dbh->prepare('INSERT INTO bookings(date_arrival,date_departure,date_creation,id_client,id_hotel,id_room) VALUES (:checkin,:checkout,:date,:id_client,:id_hotel,:id_room)');
                $bookings->bindParam('checkin', $checkin);
                $bookings->bindParam('checkout', $checkout);
                $bookings->bindParam('date', $date);
                $bookings->bindParam('id_client', $id_client);
                $bookings->bindParam('id_hotel', $id_hotel);
                $bookings->bindParam('id_room', $id_room);
                $bookings->execute();

                //Message if success
                $msg = '<h2>Réservation effectuée avec succès !</h2><br><br><p>Dates de votre réservation : du ' . $checkin . ' au ' . $checkout . '.</p><br><p>Vous logerez dans la chambre numéro ' . $num_room . '.</p>';

                //Messages if failure
            } else {
                if (!is_string($nom)) $msg = 'Veuillez saisir un nom valide';
                if (!is_string($prenom)) $msg = 'Veuillez saisir un prénom valide';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $msg = 'Veuillez saisir une adresse e-mail valide';
                if ($checkin >= $checkout) $msg = 'Votre séjour doit durer au moins une nuit !';
                if ($checkin < $date) $msg = 'Votre réservation ne peut pas commencer avant aujourd\'hui !';
            }


            //Returning the message + buttons to go back
            echo '<div>' . $msg . '</div>';
            echo '<br><br>';
            echo '<div><a href="index.php">Retourner à l\'accueil</a></div>';
            echo '<div><a href="form.php">Retourner au formulaire de réservation</a></div>';
        }
    }
}

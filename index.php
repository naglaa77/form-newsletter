<?php

const FILENAME = 'subscribers.json';

/////////////////////////////////////////
// Traitements des données du formulaire

// Initialisation de la variable $error à la valeur null
$error = null;
$email = '';
$firstname = '';
$lastname = '';

// Si le formulaire est soumis...
if (!empty($_POST)) {

    ////// 1. On récupère l'email du formulaire en supprimant les espaces avant ou après
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    ////// 2. Validation

    // On prépare un tableau pour stocker les erreurs
    $errors = [];

    // Est-ce que le champ est bien rempli ?
    if (!$firstname) {
        $errors['firstname'] = 'Le champ "Prénom" est obligatoire';
    }

    // Est-ce que le champ est bien rempli ?
    if (!$lastname) {
        $errors['lastname'] = 'Le champ "Nom" est obligatoire';
    }

    // Est-ce que le champ est bien rempli ?
    if (!$email) {
        $errors['email'] = 'Le champ "Email" est obligatoire';
    }

    // Est-ce que le format d'email est correct ?
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email incorrect';
    }

    // Vérifier que l'email n'existe pas déjà dans les abonnés

    // On doit avant toute chose aller chercher le contenu du fichier JSON

    // Si le fichier n'existe, pas encore de données
    if (!file_exists(FILENAME)) {

        // on crée un tableau vide                                                          
        $data = [];
    }
    // Si le fichier existe bien...
    else {

        // ... on récupère son contenu
        $jsonData = file_get_contents(FILENAME);

        // Si le fichier est vide
        if (!$jsonData) {

            // On crée également un tableau vide
            $data = [];
        } else {
            $data = json_decode($jsonData, true);
        }
    }

    // Une fois qu'on a nos données, on peut vérifier si l'email existe à l'intérieur
    $found = false; // Au départ on considère qu'on l'a pas trouvé
    foreach ($data as $user) { // On parcours tous les emails du tableau
        if ($email == $user['email']) { // Si l'email qu'on cherche est le même que l'email courant
            $found = true; // On l'a trouvé !
            break; // On sort de la boucle, inutile de continuer puisqu'on l'a trouvé
        }
    }

    // Si on a trouvé l'email, on ne peut pas l'enregistrer une deuxième fois
    if ($found) {
        $errors['email'] = 'Vous êtes déjà abonné à notre newsletter';
    }

    ////// 3. Si pas d'erreurs => on fait ce qu'on doit faire
    if (empty($errors)) {

        // On stocke les données du nouvel utilisateur dans un tableau associatif
        $newUser = [
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        ];

        // On ajoute la nouvelle adresse email
        $data[] = $newUser;

        // On réencode le  tableau en JSON
        $jsonData = json_encode($data);

        // On réécrit les données dans le fichier
        file_put_contents(FILENAME, $jsonData);

        // Faire une redirection pour perdre les donneés du formulaire
        // et éviter de les renvoyer plusieurs fois en cas de rafraichissement de page (F5)
        header('Location: index.php');
        exit;
    }
}


///////////////////////////////////////////////
// Affichage : inclusion du fichier de template
include 'index.phtml';

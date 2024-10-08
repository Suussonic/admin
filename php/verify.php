<?php
session_start();


ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('Database.php'); 


function displayPopup($message) {
    echo '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../css/verify.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accès Refusé</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
            }
            .popup-overlay {
                display: flex;
                justify-content: center;
                align-items: center;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                overflow: hidden;
            }
            .popup-content {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                width: 90%;
                max-width: 400px;
            }
            .popup h1 {
                margin-top: 0;
                font-size: 18px;
                color: #333;
            }
            .popup p {
                color: #666;
                margin: 10px 0;
            }
            .popup button {
                background-color: #007bff;
                border: none;
                color: white;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin-top: 20px;
                cursor: pointer;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .popup button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="popup-overlay">
            <div class="popup-content">
                <h1>Accès Refusé</h1>
                <p>' . htmlspecialchars($message) . '</p>
                <button onclick="window.location.href=\'https://funfair.ovh\'">Retour à l\'accueil</button>
            </div>
        </div>
    </body>
    </html>';
}


var_dump($_SESSION); 


if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    
  
    var_dump($userId);

    try {
      
        $stmt = $dbh->prepare('SELECT role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
        var_dump($user);

        if (!$user) {
            displayPopup("Utilisateur non trouvé.");
            exit;
        }


        var_dump($user['role']);

        if ($user['role'] !== 'admin') {
            displayPopup("Vous n'avez pas l'autorisation pour accéder à cette page.");
            exit;
        } else {
   
            echo '<script>console.log("Utilisateur admin confirmé.");</script>';
        }

    } catch (PDOException $e) {
        displayPopup("Erreur lors de la requête SQL : " . $e->getMessage());
        exit;
    }
} else {
    displayPopup("Vous devez être connecté pour accéder à cette page.");
    exit;
}
?>

<?php
$jsonFile = 'profile.json';

// Výchozí údaje pro profil
$profileData = [
    'name' => 'Oliver Chán',
    'skills' => [],
    'interests' => []
];

// Načtení dat ze souboru profile.json
if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    if ($jsonData !== false) {
        $decodedData = json_decode($jsonData, true);
        if (is_array($decodedData)) {
            $profileData = array_merge($profileData, $decodedData);
        }
    }
}

// Proměnné pro zprávy
$message = '';
$messageType = '';

// Zpracování POST požadavku
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["new_interest"])) {
        $new_interest = trim($_POST["new_interest"]);

        if (empty($new_interest)) {
            $message = "Pole nesmí být prázdné.";
            $messageType = "error";
        } else {
            // Kontrola duplicit (case-insensitive)
            $isDuplicate = false;
            foreach ($profileData['interests'] as $existingInterest) {
                if (strtolower($existingInterest) === strtolower($new_interest)) {
                    $isDuplicate = true;
                    break;
                }
            }

            if ($isDuplicate) {
                $message = "Tento zájem už existuje.";
                $messageType = "error";
            } else {
                // Přidání nového zájmu
                $profileData['interests'][] = $new_interest;

                // Uložení zpět do profile.json
                file_put_contents($jsonFile, json_encode($profileData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $message = "Zájem byl úspěšně přidán.";
                $messageType = "success";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Profil 4.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="profile-container">
    <h1>IT Profil 4.0 - <?php echo htmlspecialchars($profileData['name']); ?></h1>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="new_interest">Nový zájem:</label><br>
        <input type="text" id="new_interest" name="new_interest" placeholder="Zadejte nový zájem...">
        <button type="submit">Přidat zájem</button>
    </form>

    <h2>Zájmy</h2>
    <?php if (!empty($profileData['interests'])): ?>
        <ul>
            <?php foreach ($profileData['interests'] as $interest): ?>
                <li><?php echo htmlspecialchars($interest); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Zatím nebyly přidány žádné zájmy.</p>
    <?php endif; ?>
</div>

</body>
</html>


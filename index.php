<?php
require 'config.php';
require 'version.php';

if (empty($sheetId) || empty($sheetName) || empty($apiKey) || empty($locale)) {
    die('Configuration not set. Please ensure config.php is properly configured.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($websiteTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($websiteDescription); ?>">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
</head>

<body>
    <?php

    function convertDate($dateString, $locale)
    {
        $date = DateTime::createFromFormat('m/d/Y H:i:s', $dateString);
        setlocale(LC_TIME, $locale);
        $monthNames = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        $monthIndex = (int)$date->format('n') - 1;
        return $date->format('j') . ' de ' . $monthNames[$monthIndex] . ', ' . $date->format('Y');
    }

    $url = "https://sheets.googleapis.com/v4/spreadsheets/$sheetId/values/$sheetName?key=$apiKey";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && isset($data['values'])) {
        $data['values'] = array_reverse($data['values']);

    ?>
        <header>
            <h1><?php echo $h1Title; ?></h1>
            <div><?php echo $h1Description; ?></div>
        </header>
        <div id="wrapper">


        <?php

        echo "<ul class=\"reviews\">";
        foreach ($data['values'] as $row) {
            $name = $row[2] ?? '';
            $comment = htmlspecialchars($row[1]);
            $date = convertDate(htmlspecialchars($row[0]), $locale);

            echo "<li>";
            echo (!empty($name) ? "<div class=\"name\">" . htmlspecialchars($name) . "</div>" : '') . "<p>" . $comment . "</p><span class=\"date\">" . $date . "</span>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "No reviews found.";
    }
        ?>
        </div>
        <footer>
            <p>Made with ❤️ and ☕️ in Torino, Italy by <a href="https://valentinmuro.com" target="_blank">Valentin Muro</a></p>
        </footer>
</body>

</html>
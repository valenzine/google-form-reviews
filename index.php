<?php
require 'config.php';
require 'version.php';

if (empty($sheetId) || empty($sheetName) || empty($apiKey) || empty($locale)) {
    die('Configuration not set. Please ensure config.php is properly configured.');
}

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $url = "https://sheets.googleapis.com/v4/spreadsheets/$sheetId/values/$sheetName?key=$apiKey";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data && isset($data['values'])) {
        $data['values'] = array_reverse($data['values']);
        echo json_encode($data['values']);
        exit;
    }
    echo json_encode([]);
    exit;
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
    <header>
        <h1><?php echo $h1Title; ?></h1>
        <div><?php echo $h1Description; ?></div>
    </header>
    <div id="wrapper">
        <div class="reviews-container">
            <div class="reviews-column"></div>
            <div class="reviews-column"></div>
            <div class="reviews-column"></div>
        </div>
        <div id="loading" style="display: none;">Loading more reviews...</div>
    </div>
    <footer>
        <p>Made with ❤️ and ☕️ in Torino, Italy by <a href="https://valentinmuro.com" target="_blank">Valentin Muro</a></p>
    </footer>

    <script>
        function convertDate(dateString) {
            const date = new Date(dateString);
            const monthNames = [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];
            return `${date.getDate()} de ${monthNames[date.getMonth()]}, ${date.getFullYear()}`;
        }

        let currentPage = 0;
        let loading = false;
        let allReviews = [];
        const itemsPerBatch = 15;

        async function loadReviews() {
            if (loading) return;
            loading = true;
            
            document.getElementById('loading').style.display = 'block';
            
            if (allReviews.length === 0) {
                const response = await fetch('index.php?ajax=1');
                allReviews = await response.json();
            }
            
            const start = currentPage * itemsPerBatch;
            const end = start + itemsPerBatch;
            const batch = allReviews.slice(start, end);
            
            if (batch.length > 0) {
                const columns = document.querySelectorAll('.reviews-column');
                batch.forEach((row, index) => {
                    const reviewElement = document.createElement('div');
                    reviewElement.className = 'review-item';
                    const name = row[2] || '';
                    const comment = row[1];
                    const date = convertDate(row[0]);
                    
                    reviewElement.innerHTML = `
                        ${name ? `<div class="name">${name}</div>` : ''}
                        <p>${comment}</p>
                        <span class="date">${date}</span>
                    `;
                    
                    columns[index % 3].appendChild(reviewElement);
                });
                currentPage++;
            }
            
            document.getElementById('loading').style.display = 'none';
            loading = false;
        }

        function handleScroll() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                loadReviews();
            }
        }

        window.addEventListener('scroll', handleScroll);
        loadReviews(); // Initial load
    </script>
</body>

</html>
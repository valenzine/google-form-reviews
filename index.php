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
    <meta property="og:title" content="<?php echo htmlspecialchars($socialTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($socialDescription); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($socialImage); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($socialTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($socialDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($socialImage); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo $version; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <?php if ($enableGA4 && !empty($googleAnalyticsMeasurementId)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $googleAnalyticsMeasurementId; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo $googleAnalyticsMeasurementId; ?>');
    </script>
    <?php endif; ?>
</head>
<body>
    <header>
        <h1><?php echo $h1Title; ?></h1>
        <div><?php echo $h1Description; ?></div>
        <a href="<?php echo htmlspecialchars($newsletterButtonUrl); ?>" target="_blank" class="cta-button"><?php echo htmlspecialchars($newsletterButtonText); ?></a>
    </header>
    <div id="wrapper">
        <div class="reviews-container">
            <div class="reviews-column"></div>
            <div class="reviews-column"></div>
            <div class="reviews-column"></div>
            <div id="loading" style="display: none;"><?php echo htmlspecialchars($loadingMessage); ?></div>
        </div>
    </div>
    <footer>
        <p>Made with ❤️ and ☕️ in Torino, Italy by <a href="https://valentinmuro.com" target="_blank">Valentin Muro</a></p>
        <p>View the source on <a href="https://github.com/valenzine/google-form-reviews" target="_blank">GitHub</a> – Licensed under GNU GPL v3.</p>
    </footer>

    <script>
        const newsletterHTML = `<div class="review-item subscribe">
            <div>
                <h4><?php echo htmlspecialchars($newsletterTitle); ?></h4>
                <p><?php echo htmlspecialchars($newsletterDescription); ?></p>
                <a class="button" href="<?php echo htmlspecialchars($newsletterButtonUrl); ?>" target="_blank"><?php echo htmlspecialchars($newsletterButtonText); ?></a>
            </div>
        </div>`;

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
        const itemsPerBatch = 12;

        function insertNewsletterBox(container) {
            const newsletterElement = document.createElement('div');
            newsletterElement.innerHTML = newsletterHTML;
            container.appendChild(newsletterElement.firstChild);
        }

        async function loadReviews() {
            if (loading) return;
            loading = true;

            document.getElementById('loading').style.display = 'block';

            if (allReviews.length === 0) {
                const response = await fetch('index.php?ajax=1');
                allReviews = await response.json();
            }

            document.getElementById('numberReviews').textContent = allReviews.length;

            const start = currentPage * itemsPerBatch;
            const end = start + itemsPerBatch;
            const batch = allReviews.slice(start, end);

            if (batch.length > 0) {
                const columns = document.querySelectorAll('.reviews-column');
                const isFirstBatch = currentPage === 0;
                const randomPosition = isFirstBatch ? -1 : Math.floor(Math.random() * batch.length);

                batch.forEach((row, index) => {
                    const columnIndex = index % 3;

                    // For first batch, insert newsletter after first item in middle column
                    if (isFirstBatch && columnIndex === 1 && index === 4) {
                        insertNewsletterBox(columns[1]);
                    }

                    // For subsequent batches, insert newsletter randomly
                    if (!isFirstBatch && index === randomPosition) {
                        insertNewsletterBox(columns[columnIndex]);
                    }

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

                    columns[columnIndex].appendChild(reviewElement);
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
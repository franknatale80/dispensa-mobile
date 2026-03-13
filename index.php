<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispensa Mobile - 4 Categorie</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <link rel="manifest" href="./manifest.json">
    <meta name="theme-color" content="#4CAF50">
    <link rel="apple-touch-icon" href="./manifest.json">
    <!-- Service Worker opzionale -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js');
        }
    </script>
</head>
<body>
    <header>
        <h1>🍽️ Dispensa Casa</h1>
        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="Cucina">Cucina</button>
            <button class="tab-btn" data-tab="Bagno">Bagno</button>
            <button class="tab-btn" data-tab="Gatti">Gatti</button>
            <button class="tab-btn" data-tab="Noi-Extra">Noi-Extra</button>
        </div>
    </header>

    <div class="input-section">
        <input type="text" id="nome" placeholder="Nome prodotto">
        <input type="number" id="qta" placeholder="Qtà" step="0.01">
        <input type="text" id="unita" placeholder="pz" maxlength="5">
        <input type="date" id="scadenza" style="display:none;">
        <button onclick="aggiungi()">➕ Aggiungi</button>
        <button onclick="modifica()">✏️ Modifica</button>
        <button onclick="elimina()">🗑️ Elimina</button>
        <button onclick="scanBarcode()">📱 Barcode</button>
    </div>

    <div id="lista" class="lista-container"></div>
    <div id="avvisi" class="avvisi"></div>

    <script src="script.js"></script>
</body>
</html>

let macroAttiva = 'Cucina';
let selectedItem = null;

document.addEventListener('DOMContentLoaded', function() {
    aggiornaTab();
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.tab-btn.active').classList.remove('active');
            btn.classList.add('active');
            macroAttiva = btn.dataset.tab;
            document.getElementById('scadenza').style.display = macroAttiva === 'Cucina' ? 'inline-block' : 'none';
            aggiornaTab();
        });
    });
    setInterval(avvisiScadenze, 300000); // Ogni 5min
    avvisiScadenze();
});

async function fetchData(action, params = {}) {
    const url = `api.php?${new URLSearchParams({action}).toString()}`;
    const response = await fetch(url, {
        method: action.toUpperCase(),
        headers: { 'Content-Type': 'application/json' },
        body: action !== 'GET' ? JSON.stringify(params) : null
    });
    return response.json();
}

async function aggiornaTab() {
    const data = await fetchData('GET', {macro: macroAttiva});
    const lista = document.getElementById('lista');
    lista.innerHTML = '';
    data.forEach(item => {
        const div = document.createElement('div');
        div.className = 'item';
        div.innerHTML = `
            <strong>${item.nome}</strong> 
            <span>${item.quantita} ${item.unita}</span>
            ${macroAttiva === 'Cucina' ? `<span>${item.scadenza || ''}</span>` : ''}
            <button onclick="selezionaItem('${item.nome}')">📝</button>
        `;
        div.onclick = () => selezionaItem(item.nome);
        lista.appendChild(div);
    });
}

function selezionaItem(nome) {
    selectedItem = nome;
    document.getElementById('nome').value = nome;
}

async function aggiungi() {
    const nome = document.getElementById('nome').value;
    if (!nome) return alert('Nome obbligatorio!');
    const data = {
        nome, macro: macroAttiva, qta: parseFloat(document.getElementById('qta').value) || 0,
        unita: document.getElementById('unita').value || 'pz',
        scadenza: macroAttiva === 'Cucina' ? document.getElementById('scadenza').value : null
    };
    await fetchData('POST', data);
    aggiornaTab();
    svuotaInput();
}

async function modifica() {
    if (!selectedItem) return alert('Seleziona item!');
    const data = {
        nome_old: selectedItem, nome: document.getElementById('nome').value,
        macro: macroAttiva, qta: parseFloat(document.getElementById('qta').value) || 0,
        unita: document.getElementById('unita').value || 'pz',
        scadenza: macroAttiva === 'Cucina' ? document.getElementById('scadenza').value : null
    };
    await fetchData('PUT', data);
    aggiornaTab();
    svuotaInput();
}

async function elimina() {
    if (!selectedItem || !confirm('Elimina?')) return;
    await fetchData('DELETE', {nome: selectedItem, macro: macroAttiva});
    aggiornaTab();
    svuotaInput();
}

function svuotaInput() {
    document.getElementById('nome').value = '';
    document.getElementById('qta').value = '';
    document.getElementById('unita').value = 'pz';
    document.getElementById('scadenza').value = '';
    selectedItem = null;
}

let quagga = null;
async function scanBarcode() {
    const video = document.createElement('video');
    video.id = 'scanner-video';
    video.style.position = 'fixed';
    video.style.top = '0';
    video.style.left = '0';
    video.style.width = '100vw';
    video.style.height = '100vh';
    video.style.zIndex = '1000';
    document.body.appendChild(video);

    Quagga.init({
        inputStream: { target: video, type: 'LiveStream' },
        decoder: { readers: ['code_128_reader', 'ean_reader', 'ean_8_reader', 'code_39_reader'] }
    }, err => {
        if (err) { alert('Errore scanner'); return; }
        Quagga.start();
    });

    Quagga.onDetected(data => {
        Quagga.stop();
        document.body.removeChild(video);
        // Cerca prodotto per barcode o aggiungi nuovo
        document.getElementById('nome').value = data.codeResult.code;
        alert(`Barcode: ${data.codeResult.code} - Aggiungi/cerca?`);
    });

    // Chiudi su click fuori
    video.onclick = () => {
        Quagga.stop();
        document.body.removeChild(video);
    };
}

async function avvisiScadenze() {
    const avvisi = await fetchData('AVVISI');
    const avvisiDiv = document.getElementById('avvisi');
    if (avvisi.length) {
        avvisiDiv.innerHTML = `<div>⚠️ ${avvisi.length} scadenze vicine: ${avvisi.map(a => a.nome).join(', ')}</div>`;
        avvisiDiv.style.display = 'block';
    } else {
        avvisiDiv.style.display = 'none';
    }
}

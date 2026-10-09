<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario']);
$avatar = htmlspecialchars($_SESSION['avatar'] ?? '🦊');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parque de Juegos | Multiplica 4° Básico</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .games-hub {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .game-nav {
            display: flex;
            gap: 0.8rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .game-selector-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            color: #cbd5e1;
            padding: 0.75rem 1.4rem;
            border-radius: 9999px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .game-selector-btn.active {
            background: linear-gradient(135deg, #ec4899, #8b5cf6);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 15px var(--accent-glow);
        }

        /* --- JUEGO 1: GLOBOS --- */
        .balloon-game-box {
            position: relative;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            border: 2px solid rgba(99, 102, 241, 0.35);
            border-radius: var(--radius-lg);
            height: 480px;
            overflow: hidden;
            user-select: none;
        }

        .balloon-target-bar {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.9);
            border: 2px solid #818cf8;
            padding: 0.8rem 1.8rem;
            border-radius: 9999px;
            z-index: 10;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }

        .balloon-target-title {
            font-size: 0.78rem;
            color: #a5b4fc;
            text-transform: uppercase;
            font-weight: 800;
        }

        .balloon-target-operation {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #fde047;
            letter-spacing: 0.05em;
        }

        .balloon {
            position: absolute;
            width: 75px;
            height: 95px;
            border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: #fff;
            cursor: pointer;
            box-shadow: inset -5px -5px 10px rgba(0,0,0,0.3), 0 8px 20px rgba(0,0,0,0.4);
            animation: floatUp 6s linear forwards;
        }

        .balloon::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 8px solid currentColor;
        }

        @keyframes floatUp {
            from { bottom: -110px; }
            to { bottom: 550px; }
        }

        /* --- JUEGO 2: EL MERCADO DE DON PEPE --- */
        .market-box {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .market-dialog {
            background: rgba(15, 23, 42, 0.85);
            border: 2px solid #f59e0b;
            border-radius: var(--radius-md);
            padding: 1.4rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            text-align: left;
        }

        .market-npc {
            font-size: 3.5rem;
        }

        .market-items-grid {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }

        .market-crate {
            background: rgba(30, 41, 59, 0.8);
            border: 2px dashed #94a3b8;
            border-radius: var(--radius-md);
            padding: 0.9rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .crate-items {
            font-size: 1.6rem;
            letter-spacing: 0.2rem;
        }

        /* --- JUEGO 3: MEMORIA DE CARTAS --- */
        .memory-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            max-width: 580px;
            margin: 1.5rem auto 0;
        }

        .memory-card {
            height: 90px;
            background: #1e293b;
            border: 2px solid #6366f1;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            cursor: pointer;
            transition: transform 0.3s ease;
            user-select: none;
        }

        .memory-card.hidden {
            background: linear-gradient(135deg, #312e81, #1e1b4b);
            color: transparent;
        }

        .memory-card.matched {
            background: rgba(16, 185, 129, 0.25);
            border-color: #10b981;
            color: #34d399;
            cursor: default;
        }
    </style>
</head>
<body>

    <canvas id="bg-canvas"></canvas>

    <button id="btn-audio-toggle" class="audio-toggle-btn" title="Activar o desactivar sonido" type="button">
        <svg id="icon-sound-on" viewBox="0 0 24 24"><path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zm-2 0L7 7H3v10h4l5 3.77V3.23zM14 8.27v7.46c1.3-.68 2.2-2.04 2.2-3.73s-.9-3.05-2.2-3.73z"/></svg>
        <svg id="icon-sound-off" style="display:none;" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
        <span id="audio-status-text">Sonido Activado</span>
    </button>

    <div class="app-shell">

        <!-- Barra Superior -->
        <header class="navbar-card">
            <div class="user-info">
                <a href="menu.php" class="chip-btn" style="text-decoration:none;">← Volver al Menú</a>
                <div class="user-meta">
                    <h2>🎡 Parque de Juegos Matemáticos</h2>
                    <span>Actividades lúdicas y dinámicas sin monotonía</span>
                </div>
            </div>
            <div class="nav-actions">
                <div class="stat-pill coin-pill">🪙 +15 Monedas por Victoria</div>
            </div>
        </header>

        <main class="games-hub">

            <!-- Selector de Juegos -->
            <nav class="game-nav">
                <button class="game-selector-btn active" id="btnGameBalloons">🎈 1. Explosión de Globos</button>
                <button class="game-selector-btn" id="btnGameMarket">🛒 2. El Mercado de Don Pepe</button>
                <button class="game-selector-btn" id="btnGameMemory">🃏 3. Parejas de Memoria</button>
            </nav>

            <!-- JUEGO 1: GLOBOS MATEMÁTICOS -->
            <section id="gameBalloonsView">
                <div class="balloon-game-box" id="balloonBox">
                    <div class="balloon-target-bar">
                        <div class="balloon-target-title">¡Revienta el globo con la respuesta correcta!</div>
                        <div class="balloon-target-operation" id="balloonOpText">6 × 7 = ?</div>
                    </div>
                </div>
                <div style="text-align:center; margin-top:0.8rem;">
                    <span style="color:#cbd5e1; font-size:0.9rem;">
                        Puntaje: <b id="balloonScoreText" style="color:#10b981;">0</b> | Aciertos Seguidos: <b id="balloonComboText" style="color:#ec4899;">0</b>
                    </span>
                </div>
            </section>

            <!-- JUEGO 2: EL MERCADO DE DON PEPE -->
            <section id="gameMarketView" style="display:none;">
                <div class="market-box">
                    <div class="market-dialog">
                        <div class="market-npc">👨‍🌾</div>
                        <div>
                            <h3 style="color:#fde047; font-family:'Outfit', sans-serif; font-size:1.25rem;">Don Pepe dice:</h3>
                            <p id="marketStoryText" style="font-size:1.05rem; line-height:1.5; color:#f1f5f9; margin-top:0.3rem;">
                                "¡Hola! Llegó un pedido de la escuela: <b>5 cajas de manzanas</b>. Cada caja trae <b>8 manzanas</b>. ¿Cuántas manzanas debo empacar en total?"
                            </p>
                        </div>
                    </div>

                    <div class="market-items-grid" id="marketCratesContainer">
                        <!-- Cajas ilustradas con manzanas u objetos -->
                    </div>

                    <div style="max-width:380px; margin: 0 auto; display:flex; gap:0.8rem; align-items:center;">
                        <input type="number" id="marketAnswerInput" class="form-input" placeholder="Tu respuesta..." style="text-align:center; font-size:1.3rem; font-weight:800;">
                        <button class="submit-btn" id="btnCheckMarket" style="width:auto; padding:0.85rem 1.6rem;">
                            ¡Comprobar! 📦
                        </button>
                    </div>

                    <div id="marketFeedbackBox" style="font-size:1rem; font-weight:700;"></div>
                </div>
            </section>

            <!-- JUEGO 3: PAREJAS DE MEMORIA -->
            <section id="gameMemoryView" style="display:none;">
                <div class="login-card" style="text-align:center;">
                    <h3 style="font-family:'Outfit', sans-serif; font-size:1.4rem;">Encuentra las Parejas de Multiplicaciones</h3>
                    <p style="color:var(--text-muted); font-size:0.9rem;">
                        Une la operación (ej: 4 × 9) con su producto (ej: 36).
                    </p>

                    <div class="memory-grid" id="memoryCardsGrid">
                        <!-- 12 cartas generadas con JS -->
                    </div>

                    <button class="chip-btn" id="btnRestartMemory" style="margin-top:1.5rem; background:rgba(255,255,255,0.08);">
                        🔄 Reiniciar Tablero de Cartas
                    </button>
                </div>
            </section>

        </main>
    </div>

    <!-- Scripts -->
    <script src="js/sound.js"></script>
    <script src="js/particles.js"></script>
    <script src="js/confetti.js"></script>
    <script src="js/progress.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Audio toggle
            const audioToggleBtn = document.getElementById('btn-audio-toggle');
            const soundOnIcon = document.getElementById('icon-sound-on');
            const soundOffIcon = document.getElementById('icon-sound-off');
            const audioStatusText = document.getElementById('audio-status-text');

            function updateAudioButton() {
                if (sound.isMuted) {
                    soundOnIcon.style.display = 'none';
                    soundOffIcon.style.display = 'block';
                    audioStatusText.textContent = 'Sonido Silenciado';
                } else {
                    soundOnIcon.style.display = 'block';
                    soundOffIcon.style.display = 'none';
                    audioStatusText.textContent = 'Sonido Activado';
                }
            }
            updateAudioButton();
            audioToggleBtn.addEventListener('click', () => {
                sound.toggleMute();
                updateAudioButton();
            });

            // Tabs de juegos
            const btnBalloons = document.getElementById('btnGameBalloons');
            const btnMarket = document.getElementById('btnGameMarket');
            const btnMemory = document.getElementById('btnGameMemory');

            const viewBalloons = document.getElementById('gameBalloonsView');
            const viewMarket = document.getElementById('gameMarketView');
            const viewMemory = document.getElementById('gameMemoryView');

            function switchGame(btn, view) {
                sound.playClick();
                [btnBalloons, btnMarket, btnMemory].forEach(b => b.classList.remove('active'));
                [viewBalloons, viewMarket, viewMemory].forEach(v => v.style.display = 'none');
                btn.classList.add('active');
                view.style.display = 'block';
            }

            btnBalloons.addEventListener('click', () => { switchGame(btnBalloons, viewBalloons); startBalloonsGame(); });
            btnMarket.addEventListener('click', () => { switchGame(btnMarket, viewMarket); setupMarketStory(); });
            btnGameMemory.addEventListener('click', () => { switchGame(btnMemory, viewMemory); initMemoryGame(); });

            // ==========================================
            // 🎈 LÓGICA JUEGO 1: GLOBOS
            // ==========================================
            const balloonBox = document.getElementById('balloonBox');
            const balloonOpText = document.getElementById('balloonOpText');
            const balloonScoreText = document.getElementById('balloonScoreText');
            const balloonComboText = document.getElementById('balloonComboText');

            let balloonScore = 0;
            let balloonCombo = 0;
            let currentTargetAnswer = 0;
            let balloonSpawnInterval;

            const balloonColors = ['#ef4444', '#f97316', '#eab308', '#10b981', '#06b6d4', '#6366f1', '#ec4899'];

            function newBalloonOperation() {
                const a = Math.floor(Math.random() * 9) + 2; // 2 al 10
                const b = Math.floor(Math.random() * 9) + 2;
                currentTargetAnswer = a * b;
                balloonOpText.textContent = `${a} × ${b} = ?`;
            }

            function spawnBalloon() {
                if (viewBalloons.style.display === 'none') return;

                const b = document.createElement('div');
                b.className = 'balloon';
                const col = balloonColors[Math.floor(Math.random() * balloonColors.length)];
                b.style.backgroundColor = col;
                b.style.color = '#ffffff';

                // Decidir si es la respuesta correcta o un distractor
                const isCorrect = Math.random() < 0.45;
                let val = currentTargetAnswer;
                if (!isCorrect) {
                    const delta = (Math.floor(Math.random() * 6) + 1) * (Math.random() > 0.5 ? 1 : -1);
                    val = Math.max(4, currentTargetAnswer + delta);
                }

                b.textContent = val;
                b.style.left = `${Math.floor(Math.random() * 75) + 10}%`;

                b.addEventListener('click', () => {
                    sound.playPop();
                    b.remove();

                    if (val === currentTargetAnswer) {
                        sound.playCorrect();
                        balloonScore += 10;
                        balloonCombo++;
                        balloonScoreText.textContent = balloonScore;
                        balloonComboText.textContent = balloonCombo;

                        progress.addXP(10);
                        progress.addCoins(2);

                        if (balloonCombo >= 5) {
                            progress.unlockBadge('cazador_globos');
                            launchConfetti();
                        }

                        newBalloonOperation();
                    } else {
                        sound.playError();
                        balloonCombo = 0;
                        balloonComboText.textContent = 0;
                    }
                });

                balloonBox.appendChild(b);

                // Remover al salir de la pantalla
                setTimeout(() => {
                    if (b.parentNode) b.remove();
                }, 6000);
            }

            function startBalloonsGame() {
                clearInterval(balloonSpawnInterval);
                newBalloonOperation();
                balloonSpawnInterval = setInterval(spawnBalloon, 1400);
            }

            startBalloonsGame();

            // ==========================================
            // 🛒 LÓGICA JUEGO 2: MERCADO DE DON PEPE
            // ==========================================
            const marketStoryText = document.getElementById('marketStoryText');
            const marketCratesContainer = document.getElementById('marketCratesContainer');
            const marketAnswerInput = document.getElementById('marketAnswerInput');
            const btnCheckMarket = document.getElementById('btnCheckMarket');
            const marketFeedbackBox = document.getElementById('marketFeedbackBox');

            const marketItems = [
                { nombre: 'manzanas', icono: '🍎' },
                { nombre: 'naranjas', icono: '🍊' },
                { nombre: 'botellas de leche', icono: '🥛' },
                { nombre: 'alfajores', icono: '🍪' },
                { nombre: 'cajas de jugo', icono: '🧃' }
            ];

            let marketCorrectTotal = 0;

            function setupMarketStory() {
                const item = marketItems[Math.floor(Math.random() * marketItems.length)];
                const cantCajas = Math.floor(Math.random() * 6) + 3; // 3 a 8
                const porCaja = Math.floor(Math.random() * 6) + 3; // 3 a 8
                marketCorrectTotal = cantCajas * porCaja;

                marketStoryText.innerHTML = `
                    "Llegó un pedido de la escuela: <b>${cantCajas} cajas de ${item.nombre}</b>. 
                    Cada caja trae exactamente <b>${porCaja} unidades</b>. ¿Cuántas unidades son en total?"
                `;

                marketCratesContainer.innerHTML = '';
                for (let i = 0; i < cantCajas; i++) {
                    const crate = document.createElement('div');
                    crate.className = 'market-crate';
                    let iconsStr = '';
                    for (let j = 0; j < Math.min(porCaja, 6); j++) iconsStr += item.icono;
                    if (porCaja > 6) iconsStr += '...';

                    crate.innerHTML = `
                        <div class="crate-items">${iconsStr}</div>
                        <span style="font-size:0.78rem; color:#cbd5e1; font-weight:700;">Caja ${i+1}: (${porCaja} un.)</span>
                    `;
                    marketCratesContainer.appendChild(crate);
                }

                marketAnswerInput.value = '';
                marketFeedbackBox.innerHTML = '';
            }

            btnCheckMarket.addEventListener('click', () => {
                const userAns = parseInt(marketAnswerInput.value);
                if (isNaN(userAns)) {
                    sound.playError();
                    return;
                }

                if (userAns === marketCorrectTotal) {
                    sound.playCoin();
                    sound.playSuccess();
                    launchConfetti();
                    marketFeedbackBox.innerHTML = `<span style="color:#10b981;">🎉 ¡Excelente cálculo! Don Pepe te entrega 15 monedas.</span>`;
                    progress.addCoins(15);
                    progress.addXP(25);
                    progress.unlockBadge('chef_mercado');
                    setTimeout(setupMarketStory, 2000);
                } else {
                    sound.playError();
                    marketFeedbackBox.innerHTML = `<span style="color:#ef4444;">❌ No coincide. Recuerda: suma o multiplica la cantidad de cajas por las unidades de cada una. ¡Inténtalo de nuevo!</span>`;
                }
            });

            // ==========================================
            // 🃏 LÓGICA JUEGO 3: PAREJAS DE MEMORIA
            // ==========================================
            const memoryGrid = document.getElementById('memoryCardsGrid');
            const btnRestartMemory = document.getElementById('btnRestartMemory');

            const memoryPairsData = [
                { op: '3 × 8', res: '24' },
                { op: '7 × 7', res: '49' },
                { op: '9 × 6', res: '54' },
                { op: '5 × 9', res: '45' },
                { op: '4 × 7', res: '28' },
                { op: '8 × 8', res: '64' }
            ];

            let firstSelectedCard = null;
            let lockMemoryBoard = false;

            function initMemoryGame() {
                memoryGrid.innerHTML = '';
                firstSelectedCard = null;
                lockMemoryBoard = false;

                const cards = [];
                memoryPairsData.forEach((pair, idx) => {
                    cards.push({ id: idx, text: pair.op, type: 'op' });
                    cards.push({ id: idx, text: pair.res, type: 'res' });
                });

                // Mezclar
                cards.sort(() => Math.random() - 0.5);

                cards.forEach(cardData => {
                    const cardEl = document.createElement('div');
                    cardEl.className = 'memory-card hidden';
                    cardEl.textContent = cardData.text;
                    cardEl.setAttribute('data-id', cardData.id);

                    cardEl.addEventListener('click', () => {
                        if (lockMemoryBoard || cardEl.classList.contains('matched') || cardEl === firstSelectedCard) return;

                        sound.playWhoosh();
                        cardEl.classList.remove('hidden');

                        if (!firstSelectedCard) {
                            firstSelectedCard = cardEl;
                        } else {
                            lockMemoryBoard = true;
                            const id1 = firstSelectedCard.getAttribute('data-id');
                            const id2 = cardEl.getAttribute('data-id');

                            if (id1 === id2) {
                                sound.playCorrect();
                                firstSelectedCard.classList.add('matched');
                                cardEl.classList.add('matched');
                                firstSelectedCard = null;
                                lockMemoryBoard = false;

                                // Verificar si completó todas
                                const allMatched = memoryGrid.querySelectorAll('.matched').length === cards.length;
                                if (allMatched) {
                                    sound.playSuccess();
                                    launchConfetti();
                                    progress.addXP(40);
                                    progress.addCoins(20);
                                }
                            } else {
                                sound.playError();
                                setTimeout(() => {
                                    firstSelectedCard.classList.add('hidden');
                                    cardEl.classList.add('hidden');
                                    firstSelectedCard = null;
                                    lockMemoryBoard = false;
                                }, 900);
                            }
                        }
                    });

                    memoryGrid.appendChild(cardEl);
                });
            }

            btnRestartMemory.addEventListener('click', () => {
                sound.playClick();
                initMemoryGame();
            });
        });
    </script>
</body>
</html>

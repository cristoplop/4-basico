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
    <title>Laboratorio de Conceptos | Multiplica 4° Básico</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .lab-panel {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 1.8rem;
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
        }

        .lab-nav {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .lab-nav-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            color: #cbd5e1;
            padding: 0.65rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .lab-nav-btn.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        /* Controles de Matriz */
        .matrix-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            background: rgba(15, 23, 42, 0.7);
            padding: 1rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .matrix-slider-group {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
        }

        .matrix-slider-group input[type="range"] {
            accent-color: #ec4899;
            cursor: pointer;
        }

        .matrix-display {
            background: rgba(15, 23, 42, 0.9);
            border: 2px dashed rgba(99, 102, 241, 0.35);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 250px;
            overflow-x: auto;
        }

        .matrix-grid {
            display: grid;
            gap: 8px;
            margin: 1rem 0;
            justify-content: center;
        }

        .matrix-item {
            width: 38px;
            height: 38px;
            background: rgba(99, 102, 241, 0.2);
            border: 1.5px solid #818cf8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .matrix-item:hover {
            transform: scale(1.2) rotate(6deg);
            background: rgba(236, 72, 153, 0.35);
            border-color: #ec4899;
        }

        .formula-callout {
            font-family: 'Outfit', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: #f8fafc;
            text-align: center;
        }

        /* Recta Numérica */
        .number-line-container {
            position: relative;
            background: rgba(15, 23, 42, 0.85);
            border-radius: var(--radius-md);
            padding: 2.5rem 1.5rem 1.5rem;
            border: 1px solid var(--border-color);
            margin-top: 1rem;
            overflow-x: auto;
        }

        .line-axis {
            display: flex;
            justify-content: space-between;
            position: relative;
            border-top: 4px solid #6366f1;
            padding-top: 10px;
            min-width: 600px;
        }

        .tick-mark {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #94a3b8;
            position: relative;
        }

        .tick-mark::before {
            content: '';
            position: absolute;
            top: -14px;
            width: 2px;
            height: 10px;
            background: #6366f1;
        }

        .tick-mark.highlight {
            color: #ec4899;
            font-weight: 900;
            font-size: 1.05rem;
        }

        .tick-mark.highlight::before {
            background: #ec4899;
            height: 14px;
            top: -18px;
            width: 4px;
        }

        .frog-avatar {
            font-size: 2.2rem;
            position: absolute;
            top: -55px;
            left: 0;
            transition: left 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.4));
        }

        /* Trucos de Descomposición */
        .trick-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .trick-card h4 {
            font-family: 'Outfit', sans-serif;
            color: #fde047;
            font-size: 1.2rem;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
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
                    <h2>🔬 Laboratorio de Conceptos Matemáticos</h2>
                    <span>4° Básico • Modelos Visuales e Interactivos</span>
                </div>
            </div>
            <div class="nav-actions">
                <div class="stat-pill xp-pill">+30 XP por Explorar</div>
            </div>
        </header>

        <main class="lab-panel">

            <!-- Pestañas de Aprendizaje -->
            <nav class="lab-nav">
                <button class="lab-nav-btn active" id="tabMatrixBtn">🧱 1. Matrices (Filas y Columnas)</button>
                <button class="lab-nav-btn" id="tabNumberLineBtn">🐸 2. Saltos en la Recta Numérica</button>
                <button class="lab-nav-btn" id="tabDecompBtn">🧠 3. Trucos y Descomposición</button>
            </nav>

            <!-- SECCIÓN 1: MATRICES / ARREGLOS RECTANGULARES -->
            <div id="sectionMatrix">
                <div class="matrix-controls">
                    <div class="matrix-slider-group">
                        <label for="sliderFilas">Filas (Grupos):</label>
                        <input type="range" id="sliderFilas" min="2" max="8" value="3">
                        <span id="valFilas" style="color:#6366f1; font-size:1.2rem;">3</span>
                    </div>

                    <div style="font-size:1.3rem; font-weight:800; color:#ec4899;">×</div>

                    <div class="matrix-slider-group">
                        <label for="sliderColumnas">Columnas (Elementos):</label>
                        <input type="range" id="sliderColumnas" min="2" max="8" value="4">
                        <span id="valColumnas" style="color:#ec4899; font-size:1.2rem;">4</span>
                    </div>

                    <button class="chip-btn" id="btnFlipMatrix" style="background:#8b5cf6; color:white; border-color:transparent;">
                        🔄 ¡Girar Matriz (Conmutativa)!
                    </button>
                </div>

                <div class="matrix-display">
                    <div class="formula-callout" id="formulaCallout">
                        3 filas de 4 manzanas = <span style="color:#ec4899;">3 × 4 = 12</span>
                    </div>
                    <p style="color:var(--text-muted); font-size:0.9rem; margin-top:0.3rem;" id="sumCallout">
                        Es lo mismo que sumar: 4 + 4 + 4 = 12
                    </p>

                    <div class="matrix-grid" id="matrixGrid">
                        <!-- Generado dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: RECTA NUMÉRICA CON SALTOS DE LA RANA -->
            <div id="sectionNumberLine" style="display:none;">
                <div class="matrix-controls">
                    <div class="matrix-slider-group">
                        <label for="selectSaltos">¿Cuántos saltos da la rana?:</label>
                        <select id="selectSaltos" class="form-select" style="width:auto; padding:0.4rem 2rem 0.4rem 0.8rem;">
                            <option value="2">2 saltos</option>
                            <option value="3">3 saltos</option>
                            <option value="4" selected>4 saltos</option>
                            <option value="5">5 saltos</option>
                            <option value="6">6 saltos</option>
                            <option value="7">7 saltos</option>
                        </select>
                    </div>

                    <div style="font-size:1.3rem; font-weight:800; color:#10b981;">×</div>

                    <div class="matrix-slider-group">
                        <label for="selectDistancia">De cuánto en cuánto:</label>
                        <select id="selectDistancia" class="form-select" style="width:auto; padding:0.4rem 2rem 0.4rem 0.8rem;">
                            <option value="2">De 2 en 2</option>
                            <option value="3">De 3 en 3</option>
                            <option value="4">De 4 en 4</option>
                            <option value="5">De 5 en 5</option>
                            <option value="6" selected>De 6 en 6</option>
                            <option value="7">De 7 en 7</option>
                            <option value="8">De 8 en 8</option>
                            <option value="9">De 9 en 9</option>
                        </select>
                    </div>

                    <button class="submit-btn" id="btnAnimarSaltos" style="width:auto; padding:0.5rem 1.2rem; font-size:0.9rem;">
                        🐸 ¡Hacer Saltar a la Rana!
                    </button>
                </div>

                <div class="number-line-container">
                    <div class="formula-callout" id="frogFormulaCallout" style="margin-bottom:1.8rem;">
                        4 saltos de 6 en 6 = <span style="color:#10b981;">4 × 6 = 24</span>
                    </div>

                    <div class="line-axis" id="lineAxis">
                        <div class="frog-avatar" id="frogMascot">🐸</div>
                        <!-- Ticks del 0 al 60 generados dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: TRUCOS DE DESCOMPOSICIÓN PARA 4° BÁSICO -->
            <div id="sectionDecomp" style="display:none;">
                <h3 style="font-family:'Outfit', sans-serif; font-size:1.3rem; margin-bottom:1rem;">
                    Estrategias Mentales para el Cálculo de Tablas
                </h3>

                <div class="trick-card">
                    <h4>✋ 1. El Truco Mágico de la Tabla del 9 (Con tus Manos)</h4>
                    <p style="color:#cbd5e1; font-size:0.92rem; line-height:1.5;">
                        Pon tus 10 dedos frente a ti. Para calcular <b>9 × 4</b>, dobla tu 4° dedo empezando desde la izquierda.
                        A la izquierda del dedo doblado quedan <b>3 dedos</b> (decenas = 30) y a la derecha quedan <b>6 dedos</b> (unidades = 6).
                        ¡El resultado es <b>36</b>!
                    </p>
                </div>

                <div class="trick-card">
                    <h4>✌️ 2. El Truco de la Mitad y el Doble (Tabla del 4 y del 8)</h4>
                    <p style="color:#cbd5e1; font-size:0.92rem; line-height:1.5;">
                        Multiplicar por 4 es <b>doblar dos veces</b>. <br>
                        Ejemplo: 4 × 7 → El doble de 7 es 14, y el doble de 14 es <b>28</b>.<br>
                        Multiplicar por 8 es <b>doblar tres veces</b>: 8 × 6 → 12 → 24 → <b>48</b>.
                    </p>
                </div>

                <div class="trick-card">
                    <h4>🧩 3. Descomposición de Números Grandes (La Tabla del 7)</h4>
                    <p style="color:#cbd5e1; font-size:0.92rem; line-height:1.5;">
                        Si se te olvida <b>7 × 8</b>, desarma el 7 en (5 + 2): <br>
                        (5 × 8) = 40 <br>
                        (2 × 8) = 16 <br>
                        Sumamos: 40 + 16 = <b>56</b>. ¡Nunca más te olvidarás!
                    </p>
                </div>
            </div>

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

            // Tabs del laboratorio
            const tabMatrixBtn = document.getElementById('tabMatrixBtn');
            const tabNumberLineBtn = document.getElementById('tabNumberLineBtn');
            const tabDecompBtn = document.getElementById('tabDecompBtn');

            const secMatrix = document.getElementById('sectionMatrix');
            const secNumberLine = document.getElementById('sectionNumberLine');
            const secDecomp = document.getElementById('sectionDecomp');

            function switchTab(btn, sec) {
                sound.playClick();
                [tabMatrixBtn, tabNumberLineBtn, tabDecompBtn].forEach(b => b.classList.remove('active'));
                [secMatrix, secNumberLine, secDecomp].forEach(s => s.style.display = 'none');
                btn.classList.add('active');
                sec.style.display = 'block';
            }

            tabMatrixBtn.addEventListener('click', () => switchTab(tabMatrixBtn, secMatrix));
            tabNumberLineBtn.addEventListener('click', () => {
                switchTab(tabNumberLineBtn, secNumberLine);
                renderNumberLine();
            });
            tabDecompBtn.addEventListener('click', () => {
                switchTab(tabDecompBtn, secDecomp);
                progress.unlockBadge('tabla_dificil');
            });

            // --- Lógica de la Matriz ---
            const sliderFilas = document.getElementById('sliderFilas');
            const sliderColumnas = document.getElementById('sliderColumnas');
            const valFilas = document.getElementById('valFilas');
            const valColumnas = document.getElementById('valColumnas');
            const matrixGrid = document.getElementById('matrixGrid');
            const formulaCallout = document.getElementById('formulaCallout');
            const sumCallout = document.getElementById('sumCallout');
            const btnFlipMatrix = document.getElementById('btnFlipMatrix');

            const icons = ['🍎', '⭐', '💎', '🚀', '🍓', '🏀'];

            function updateMatrix() {
                const filas = parseInt(sliderFilas.value);
                const cols = parseInt(sliderColumnas.value);
                valFilas.textContent = filas;
                valColumnas.textContent = cols;

                const total = filas * cols;
                const icon = icons[(filas + cols) % icons.length];

                formulaCallout.innerHTML = `${filas} filas de ${cols} elementos = <span style="color:#ec4899;">${filas} × ${cols} = ${total}</span>`;
                
                let sumArr = [];
                for (let i = 0; i < filas; i++) sumArr.push(cols);
                sumCallout.textContent = `Suma iterada: ${sumArr.join(' + ')} = ${total}`;

                matrixGrid.style.gridTemplateColumns = `repeat(${cols}, 38px)`;
                matrixGrid.innerHTML = '';

                for (let r = 0; r < filas; r++) {
                    for (let c = 0; c < cols; c++) {
                        const item = document.createElement('div');
                        item.className = 'matrix-item';
                        item.textContent = icon;
                        item.title = `Fila ${r+1}, Columna ${c+1}`;
                        item.addEventListener('click', () => {
                            sound.playPop();
                            item.style.transform = 'scale(1.3) rotate(15deg)';
                            setTimeout(() => item.style.transform = '', 200);
                        });
                        matrixGrid.appendChild(item);
                    }
                }

                progress.unlockBadge('maestro_matrices');
            }

            sliderFilas.addEventListener('input', () => { sound.playKey(); updateMatrix(); });
            sliderColumnas.addEventListener('input', () => { sound.playKey(); updateMatrix(); });

            btnFlipMatrix.addEventListener('click', () => {
                sound.playWhoosh();
                const temp = sliderFilas.value;
                sliderFilas.value = sliderColumnas.value;
                sliderColumnas.value = temp;
                updateMatrix();
            });

            updateMatrix();

            // --- Lógica de la Recta Numérica ---
            const selectSaltos = document.getElementById('selectSaltos');
            const selectDistancia = document.getElementById('selectDistancia');
            const lineAxis = document.getElementById('lineAxis');
            const frogMascot = document.getElementById('frogMascot');
            const btnAnimarSaltos = document.getElementById('btnAnimarSaltos');
            const frogFormulaCallout = document.getElementById('frogFormulaCallout');

            function renderNumberLine() {
                const saltos = parseInt(selectSaltos.value);
                const dist = parseInt(selectDistancia.value);
                const destino = saltos * dist;
                const maxTick = Math.max(destino + 8, 30);

                frogFormulaCallout.innerHTML = `${saltos} saltos de ${dist} en ${dist} = <span style="color:#10b981;">${saltos} × ${dist} = ${destino}</span>`;

                lineAxis.innerHTML = '';
                lineAxis.appendChild(frogMascot);
                frogMascot.style.left = '0%';

                for (let i = 0; i <= maxTick; i++) {
                    // Mostrar múltiplos o marcas
                    const tick = document.createElement('div');
                    tick.className = 'tick-mark';
                    if (i > 0 && i % dist === 0 && i <= destino) {
                        tick.classList.add('highlight');
                    }
                    tick.textContent = i;
                    tick.setAttribute('data-num', i);
                    lineAxis.appendChild(tick);
                }
            }

            btnAnimarSaltos.addEventListener('click', () => {
                const saltos = parseInt(selectSaltos.value);
                const dist = parseInt(selectDistancia.value);
                const destino = saltos * dist;
                const ticks = lineAxis.querySelectorAll('.tick-mark');

                let currentJump = 0;
                btnAnimarSaltos.disabled = true;

                const interval = setInterval(() => {
                    currentJump++;
                    const targetNum = currentJump * dist;
                    const targetTick = Array.from(ticks).find(t => parseInt(t.getAttribute('data-num')) === targetNum);

                    if (targetTick) {
                        sound.playJump();
                        const rectLine = lineAxis.getBoundingClientRect();
                        const rectTick = targetTick.getBoundingClientRect();
                        const leftOffset = rectTick.left - rectLine.left;
                        frogMascot.style.left = `${leftOffset}px`;
                    }

                    if (currentJump >= saltos) {
                        clearInterval(interval);
                        btnAnimarSaltos.disabled = false;
                        sound.playSuccess();
                        launchConfetti();
                        progress.unlockBadge('salto_ranita');
                    }
                }, 600);
            });

            selectSaltos.addEventListener('change', renderNumberLine);
            selectDistancia.addEventListener('change', renderNumberLine);
        });
    </script>
</body>
</html>

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
    <title>Gimnasio del 2 al 10 | Multiplica 4° Básico</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .gym-layout {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .table-pills-row {
            display: flex;
            gap: 0.6rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .table-pill-btn {
            background: rgba(30, 41, 59, 0.8);
            border: 2px solid var(--border-color);
            color: #cbd5e1;
            padding: 0.6rem 1.1rem;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .table-pill-btn:hover {
            transform: translateY(-3px);
            border-color: #f59e0b;
            color: #fde047;
        }
        .table-pill-btn.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }
        .table-hero-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 1.8rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .pattern-box {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: var(--radius-md);
            padding: 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .quick-challenge-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }
        .challenge-option {
            background: rgba(30, 41, 59, 0.9);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.1rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            cursor: pointer;
            transition: var(--transition);
        }
        .challenge-option:hover {
            border-color: #6366f1;
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
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
                    <h2>⚡ Gimnasio de Tablas del 2 al 10</h2>
                    <span>Entrenamiento focalizado con patrones y secretos</span>
                </div>
            </div>
            <div class="nav-actions">
                <div class="stat-pill xp-pill">+10 XP por Acierto</div>
            </div>
        </header>
        <main class="gym-layout">
            <!-- Selector de Tablas del 2 al 10 -->
            <div class="table-pills-row" id="tablePillsContainer">
                <!-- Botones del 2 al 10 -->
            </div>
            <!-- Ficha de la Tabla Seleccionada -->
            <section class="table-hero-card">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                    <h3 id="currentTableTitle" style="font-family:'Outfit', sans-serif; font-size:1.6rem; color:#fff;">
                        Entrenando la Tabla del 7
                    </h3>
                    <span id="masteryBadge" class="badge-tag" style="background:rgba(245, 158, 11, 0.2); color:#fbbf24; border-color:#f59e0b;">
                        ⭐ Racha de Aciertos: <span id="gymStreak">0</span>
                    </span>
                </div>
                <!-- El Patrón o Secreto de la Tabla -->
                <div class="pattern-box">
                    <span style="font-size:2.4rem;">💡</span>
                    <div>
                        <strong style="color:#fde047; font-size:1rem; display:block;">El Secreto Mnemotécnico:</strong>
                        <p id="patternDescription" style="color:#cbd5e1; font-size:0.9rem; margin-top:0.2rem;">
                            La tabla del 7 se puede calcular sumando la tabla del 5 más la tabla del 2.
                        </p>
                    </div>
                </div>
                <!-- Desafío Relámpago -->
                <div style="text-align:center; margin-top:1rem;">
                    <span style="color:var(--text-muted); font-size:0.85rem; text-transform:uppercase; font-weight:800; letter-spacing:0.05em;">
                        Desafío Relámpago
                    </span>
                    <div id="gymQuestion" style="font-family:'Outfit', sans-serif; font-size:3.2rem; font-weight:900; color:#fff; margin:0.8rem 0;">
                        7 × 6 = ?
                    </div>
                    <div class="quick-challenge-grid" id="gymOptionsGrid">
                        <!-- Opciones de respuesta -->
                    </div>
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
            // Secretos por tabla
            const secretosTablas = {
                2: 'Todos los resultados son números pares (terminan en 0, 2, 4, 6 u 8). ¡Es el doble del número!',
                3: 'Si sumas los dígitos del resultado, siempre obtendrás 3, 6 o 9 (ej: 3 × 7 = 21 → 2+1=3).',
                4: '¡Doble del doble! Para multiplicar por 4, duplica el número y vuelve a duplicarlo.',
                5: 'Todos los resultados terminan obligatoriamente en 0 o en 5. ¡Como mirar las manecillas de un reloj!',
                6: 'Multiplicar por 6 es multiplicar por 3 y luego duplicar el resultado.',
                7: 'Para calcular 7 × N, piensa en (5 × N) + (2 × N). ¡Es la suma de la tabla del 5 y del 2!',
                8: '¡Doble del doble del doble! Para 8 × 5: 5 → 10 → 20 → 40.',
                9: 'Los dos dígitos del resultado siempre suman 9 (ej: 9 × 4 = 36 → 3+6=9; 9 × 8 = 72 → 7+2=9).',
                10: '¡La más rápida! Solo agrega un 0 al final del número que estás multiplicando.'
            };
            const container = document.getElementById('tablePillsContainer');
            const titleEl = document.getElementById('currentTableTitle');
            const patternEl = document.getElementById('patternDescription');
            const qEl = document.getElementById('gymQuestion');
            const optEl = document.getElementById('gymOptionsGrid');
            const streakEl = document.getElementById('gymStreak');
            let selectedTable = 7;
            let currentAns = 0;
            let streak = 0;
            // Renderizar botones de tablas del 2 al 10
            for (let t = 2; t <= 10; t++) {
                const btn = document.createElement('button');
                btn.className = 'table-pill-btn';
                if (t === selectedTable) btn.classList.add('active');
                btn.textContent = `Tabla del ${t}`;
                btn.addEventListener('click', () => {
                    sound.playClick();
                    document.querySelectorAll('.table-pill-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedTable = t;
                    streak = 0;
                    streakEl.textContent = 0;
                    loadTableGym(t);
                });
                container.appendChild(btn);
            }
            function loadTableGym(t) {
                titleEl.textContent = `Entrenando la Tabla del ${t}`;
                patternEl.textContent = secretosTablas[t] || 'Practica y descubre patrones.';
                nextChallenge(t);
            }
            function nextChallenge(t) {
                const mult = Math.floor(Math.random() * 9) + 2; // 2 al 10
                currentAns = t * mult;
                qEl.textContent = `${t} × ${mult} = ?`;
                const options = new Set([currentAns]);
                while (options.size < 4) {
                    const delta = (Math.floor(Math.random() * 6) + 1) * (Math.random() > 0.5 ? 1 : -1);
                    options.add(Math.max(2, currentAns + delta));
                }
                const arr = Array.from(options).sort(() => Math.random() - 0.5);
                optEl.innerHTML = '';
                arr.forEach(val => {
                    const b = document.createElement('button');
                    b.className = 'challenge-option';
                    b.textContent = val;
                    b.addEventListener('click', () => {
                        if (val === currentAns) {
                            sound.playCorrect();
                            streak++;
                            streakEl.textContent = streak;
                            progress.addXP(10);
                            b.style.borderColor = '#10b981';
                            b.style.background = 'rgba(16, 185, 129, 0.3)';
                            if (streak % 5 === 0) {
                                sound.playSuccess();
                                launchConfetti();
                                progress.addStars(1);
                            }
                            setTimeout(() => nextChallenge(t), 700);
                        } else {
                            sound.playError();
                            streak = 0;
                            streakEl.textContent = 0;
                            b.style.borderColor = '#ef4444';
                            b.style.background = 'rgba(239, 68, 68, 0.3)';
                        }
                    });
                    optEl.appendChild(b);
                });
            }
            loadTableGym(selectedTable);
        });
    </script>
</body>
</html>

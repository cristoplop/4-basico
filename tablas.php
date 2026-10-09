<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$nombreUsuario = htmlspecialchars($_SESSION['usuario']);
$rolUsuario = htmlspecialchars($_SESSION['rol'] ?? 'Estudiante');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablas de Multiplicar | Panel de Aprendizaje</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .app-container {
            width: 100%;
            max-width: 900px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .navbar-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: #fff;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .user-meta h2 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .user-meta span {
            font-size: 0.8rem;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.15);
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .tabs-header {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .tab-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 0.7rem 1.4rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 20px var(--primary-glow);
        }

        /* Selector de tablas */
        .table-selector {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .num-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .num-btn:hover {
            transform: translateY(-3px) scale(1.05);
            border-color: var(--primary);
            color: #a5b4fc;
        }

        .num-btn.active {
            background: linear-gradient(135deg, #ec4899, #8b5cf6);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 5px 15px var(--accent-glow);
        }

        /* Cuadrícula de resultados */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.85rem;
        }

        .mult-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .mult-card:hover {
            transform: translateY(-3px) scale(1.02);
            border-color: #38bdf8;
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.25);
            background: rgba(30, 41, 59, 0.8);
        }

        .mult-card .result {
            color: #ec4899;
            font-weight: 800;
            font-size: 1.25rem;
        }

        /* Modo Reto / Quiz */
        .quiz-panel {
            text-align: center;
            padding: 1rem;
        }

        .quiz-score-bar {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .quiz-question {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin: 1.5rem 0;
            color: #f8fafc;
        }

        .quiz-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            max-width: 420px;
            margin: 0 auto;
        }

        .option-btn {
            background: rgba(30, 41, 59, 0.85);
            border: 2px solid var(--border-color);
            color: #fff;
            padding: 1.2rem;
            border-radius: var(--radius-md);
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            cursor: pointer;
            transition: var(--transition);
        }

        .option-btn:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-3px);
        }

        .option-btn.correct {
            background: rgba(16, 185, 129, 0.3) !important;
            border-color: #10b981 !important;
            color: #34d399 !important;
        }

        .option-btn.wrong {
            background: rgba(239, 68, 68, 0.3) !important;
            border-color: #ef4444 !important;
            color: #f87171 !important;
        }
    </style>
</head>
<body>

    <canvas id="bg-canvas"></canvas>

    <!-- Botón Flotante de Audio -->
    <button id="btn-audio-toggle" class="audio-toggle-btn" title="Activar o desactivar sonido" type="button">
        <svg id="icon-sound-on" viewBox="0 0 24 24">
            <path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zm-2 0L7 7H3v10h4l5 3.77V3.23zM14 8.27v7.46c1.3-.68 2.2-2.04 2.2-3.73s-.9-3.05-2.2-3.73z"/>
        </svg>
        <svg id="icon-sound-off" style="display:none;" viewBox="0 0 24 24">
            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
        </svg>
        <span id="audio-status-text">Sonido Activado</span>
    </button>

    <div class="app-container">
        <!-- Barra de navegación -->
        <header class="navbar-card">
            <div class="user-info">
                <div class="user-avatar"><?= mb_substr($nombreUsuario, 0, 1) ?></div>
                <div class="user-meta">
                    <h2>¡Hola, <?= $nombreUsuario ?>! 👋</h2>
                    <span><?= $rolUsuario ?></span>
                </div>
            </div>

            <div class="nav-actions">
                <a href="logout.php" class="btn-logout" id="btnLogout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Cerrar Sesión
                </a>
            </div>
        </header>

        <!-- Selector de Modo (Explorar Tablas o Desafío Game) -->
        <div class="tabs-header">
            <button class="tab-btn active" id="tabExploreBtn">📚 Explorar Tablas</button>
            <button class="tab-btn" id="tabQuizBtn">🎮 Modo Reto Matemático</button>
        </div>

        <!-- Vista 1: Explorar Tablas -->
        <section class="login-card" id="exploreView">
            <h3 style="font-family:'Outfit', sans-serif; font-size:1.35rem; margin-bottom:1rem;">
                Selecciona la tabla que deseas practicar:
            </h3>

            <div class="table-selector" id="tableSelector">
                <!-- Botones del 1 al 12 -->
            </div>

            <div class="results-grid" id="resultsGrid">
                <!-- Tarjetas multiplicadas -->
            </div>
        </section>

        <!-- Vista 2: Desafío Quiz Interactivo -->
        <section class="login-card quiz-panel" id="quizView" style="display:none;">
            <div class="quiz-score-bar">
                <div>🏆 Puntos: <b id="scoreNum" style="color:#10b981; font-size:1.2rem;">0</b></div>
                <div>🔥 Racha: <b id="streakNum" style="color:#f59e0b; font-size:1.2rem;">0</b></div>
            </div>

            <h3 style="color:var(--text-muted); font-size:1rem; text-transform:uppercase; letter-spacing:0.05em;">
                ¿Cuál es el resultado correcto?
            </h3>

            <div class="quiz-question" id="quizQuestion">7 × 8 = ?</div>

            <div class="quiz-options" id="quizOptions">
                <!-- 4 opciones -->
            </div>
        </section>
    </div>

    <script src="js/sound.js"></script>
    <script src="js/particles.js"></script>
    <script src="js/confetti.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            sound.playWelcome();

            // Control de audio
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

            // Sonido al cerrar sesión
            document.getElementById('btnLogout').addEventListener('click', () => {
                sound.playClick();
            });

            // Tabs
            const tabExploreBtn = document.getElementById('tabExploreBtn');
            const tabQuizBtn = document.getElementById('tabQuizBtn');
            const exploreView = document.getElementById('exploreView');
            const quizView = document.getElementById('quizView');

            tabExploreBtn.addEventListener('click', () => {
                sound.playClick();
                tabExploreBtn.classList.add('active');
                tabQuizBtn.classList.remove('active');
                exploreView.style.display = 'block';
                quizView.style.display = 'none';
            });

            tabQuizBtn.addEventListener('click', () => {
                sound.playClick();
                tabQuizBtn.classList.add('active');
                tabExploreBtn.classList.remove('active');
                quizView.style.display = 'block';
                exploreView.style.display = 'none';
                nextQuestion();
            });

            // Generador de selector de tablas
            const tableSelector = document.getElementById('tableSelector');
            const resultsGrid = document.getElementById('resultsGrid');
            let currentTable = 7;

            function renderTableSelector() {
                tableSelector.innerHTML = '';
                for (let i = 1; i <= 12; i++) {
                    const btn = document.createElement('button');
                    btn.classList.add('num-btn');
                    if (i === currentTable) btn.classList.add('active');
                    btn.textContent = i;
                    btn.addEventListener('click', () => {
                        sound.playClick();
                        currentTable = i;
                        document.querySelectorAll('.num-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        renderTableGrid(i);
                    });
                    tableSelector.appendChild(btn);
                }
            }

            function renderTableGrid(num) {
                resultsGrid.innerHTML = '';
                for (let i = 1; i <= 12; i++) {
                    const card = document.createElement('div');
                    card.classList.add('mult-card');
                    card.innerHTML = `<span>${num} × ${i}</span> <span class="result">= ${num * i}</span>`;
                    card.addEventListener('click', () => {
                        sound.playWhoosh();
                        card.style.transform = 'scale(1.08)';
                        setTimeout(() => card.style.transform = '', 180);
                    });
                    resultsGrid.appendChild(card);
                }
            }

            renderTableSelector();
            renderTableGrid(currentTable);

            // Lógica del Desafío Quiz
            let score = 0;
            let streak = 0;
            let currentAnswer = 0;
            const questionEl = document.getElementById('quizQuestion');
            const optionsEl = document.getElementById('quizOptions');
            const scoreEl = document.getElementById('scoreNum');
            const streakEl = document.getElementById('streakNum');

            function nextQuestion() {
                const a = Math.floor(Math.random() * 10) + 2;
                const b = Math.floor(Math.random() * 10) + 2;
                currentAnswer = a * b;
                questionEl.textContent = `${a} × ${b} = ?`;

                // Opciones incorrectas
                const options = new Set([currentAnswer]);
                while (options.size < 4) {
                    const delta = (Math.floor(Math.random() * 7) + 1) * (Math.random() > 0.5 ? 1 : -1);
                    const fake = Math.max(2, currentAnswer + delta);
                    options.add(fake);
                }

                const optionsArr = Array.from(options).sort(() => Math.random() - 0.5);
                optionsEl.innerHTML = '';

                optionsArr.forEach(val => {
                    const btn = document.createElement('button');
                    btn.classList.add('option-btn');
                    btn.textContent = val;
                    btn.addEventListener('click', () => checkAnswer(btn, val));
                    optionsEl.appendChild(btn);
                });
            }

            function checkAnswer(btn, selected) {
                const buttons = optionsEl.querySelectorAll('.option-btn');
                buttons.forEach(b => b.disabled = true);

                if (selected === currentAnswer) {
                    btn.classList.add('correct');
                    sound.playSuccess();
                    score += 10;
                    streak += 1;
                    scoreEl.textContent = score;
                    streakEl.textContent = streak;

                    if (streak > 0 && streak % 3 === 0) {
                        launchConfetti();
                    }

                    setTimeout(nextQuestion, 1000);
                } else {
                    btn.classList.add('wrong');
                    sound.playError();
                    streak = 0;
                    streakEl.textContent = 0;

                    // Mostrar cuál era la correcta
                    buttons.forEach(b => {
                        if (parseInt(b.textContent) === currentAnswer) {
                            b.classList.add('correct');
                        }
                    });

                    setTimeout(nextQuestion, 1400);
                }
            }
        });
    </script>
</body>
</html>

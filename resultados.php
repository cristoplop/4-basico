<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario']);
$avatar = htmlspecialchars($_SESSION['avatar'] ?? '🦊');
$curso = htmlspecialchars($_SESSION['curso'] ?? '4° Básico');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados y Retroalimentación | 4° Básico</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .report-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .score-hero-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 2.2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .score-percentage-circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            box-shadow: 0 10px 30px var(--primary-glow);
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
        }

        .diagnostic-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            text-align: left;
            margin-top: 1.5rem;
        }

        .diagnostic-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.2rem;
        }

        .diagnostic-card.mastered {
            border-color: #10b981;
        }

        .diagnostic-card.reinforce {
            border-color: #f59e0b;
        }

        .diploma-container {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

    <canvas id="bg-canvas"></canvas>

    <button id="btn-audio-toggle" class="audio-toggle-btn no-print" title="Activar o desactivar sonido" type="button">
        <svg id="icon-sound-on" viewBox="0 0 24 24"><path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zm-2 0L7 7H3v10h4l5 3.77V3.23zM14 8.27v7.46c1.3-.68 2.2-2.04 2.2-3.73s-.9-3.05-2.2-3.73z"/></svg>
        <svg id="icon-sound-off" style="display:none;" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
        <span id="audio-status-text">Sonido Activado</span>
    </button>

    <div class="app-shell">

        <!-- Barra Superior -->
        <header class="navbar-card no-print">
            <div class="user-info">
                <a href="menu.php" class="chip-btn" style="text-decoration:none;">← Volver al Menú</a>
                <div class="user-meta">
                    <h2>📊 Informe de Rendimiento y Retroalimentación</h2>
                    <span>Resultados de tu evaluación formativa</span>
                </div>
            </div>
            <div class="nav-actions">
                <button class="chip-btn" id="btnPrintDiploma" style="background:#6366f1; color:#fff; border-color:transparent;">
                    🖨️ Imprimir Diploma
                </button>
            </div>
        </header>

        <main class="report-section">

            <!-- Tarjeta de Puntaje y Nivel -->
            <section class="score-hero-card no-print">
                <div class="score-percentage-circle" id="scorePercentCircle">
                    80%
                </div>

                <h3 id="simceLevelBadge" style="font-family:'Outfit', sans-serif; font-size:1.8rem; color:#fde047; margin-bottom:0.3rem;">
                    NIVEL ADECUADO (SOBRESALIENTE) 🏆
                </h3>
                <p id="scoreSubtitleText" style="color:#cbd5e1; font-size:1.05rem;">
                    ¡Gran trabajo, <?= $nombre ?>! Acertaste <b>8 de 10</b> situaciones problemáticas del SIMCE.
                </p>

                <!-- Diagnóstico de Tablas -->
                <div class="diagnostic-grid">
                    <div class="diagnostic-card mastered">
                        <h4 style="color:#34d399; font-family:'Outfit', sans-serif; font-size:1.1rem; margin-bottom:0.4rem;">
                            ✅ Tablas Fuertes y Dominadas:
                        </h4>
                        <p id="masteredTablesText" style="color:#cbd5e1; font-size:0.9rem;">
                            Tablas del 2, 4, 5 y 10. ¡Tu comprensión de grupos y dobles es excelente!
                        </p>
                    </div>

                    <div class="diagnostic-card reinforce">
                        <h4 style="color:#fbbf24; font-family:'Outfit', sans-serif; font-size:1.1rem; margin-bottom:0.4rem;">
                            🎯 Tablas Sugeridas para Reforzar:
                        </h4>
                        <p id="reinforceTablesText" style="color:#cbd5e1; font-size:0.9rem;">
                            Practica un poco más las tablas del 7 y del 8 usando los trucos del Laboratorio.
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div style="display:flex; justify-content:center; gap:1rem; margin-top:1.8rem; flex-wrap:wrap;">
                    <a href="simce.php" class="submit-btn secondary" style="width:auto; padding:0.8rem 1.6rem; text-decoration:none;">
                        🔄 Intentar Otro Ensayo
                    </a>
                    <a href="menu.php" class="submit-btn" style="width:auto; padding:0.8rem 1.6rem; text-decoration:none;">
                        🎮 Ir a la Zona de Juegos
                    </a>
                </div>
            </section>

            <!-- DIPLOMA OFICIAL IMPRIMIBLE -->
            <section class="diploma-container" id="diplomaSection">
                <div class="diploma-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <span style="font-size:2.8rem;"><?= $avatar ?></span>
                        <div style="text-align:right;">
                            <span style="font-size:0.85rem; font-weight:800; color:#6366f1; text-transform:uppercase; letter-spacing:0.06em;">
                                Ministerio de la Alegría Matemática
                            </span>
                        </div>
                    </div>

                    <h1 style="font-size:2.2rem; font-weight:900;">DIPLOMA DE MAESTRÍA</h1>
                    <p style="font-size:1.1rem; color:#475569; font-weight:600;">Se certifica que el estudiante:</p>

                    <div class="student-name"><?= $nombre ?></div>

                    <p style="font-size:1.05rem; line-height:1.6; color:#334155; max-width:580px; margin:0 auto 1.5rem;">
                        Ha demostrado dedicación, perseverancia y comprensión en el aprendizaje de las 
                        <b>Tablas de Multiplicar del 2 al 10</b> y en la resolución de problemas tipo SIMCE 
                        correspondientes al nivel de <b><?= $curso ?></b>.
                    </p>

                    <div style="display:flex; justify-content:space-around; align-items:flex-end; border-top:2px dashed #cbd5e1; padding-top:1.5rem; margin-top:1.5rem;">
                        <div style="text-align:center;">
                            <div style="font-family:'Outfit', sans-serif; font-weight:800; font-size:1.1rem; color:#4338ca;">MultiBot 3000</div>
                            <small style="color:#64748b; font-size:0.8rem;">Mascota Pedagógica</small>
                        </div>

                        <div style="background:#fef08a; border:3px solid #eab308; width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; box-shadow:0 4px 10px rgba(0,0,0,0.15);">
                            ⭐
                        </div>

                        <div style="text-align:center;">
                            <div id="diplomaDate" style="font-weight:700; font-size:1rem; color:#334155;">--/--/----</div>
                            <small style="color:#64748b; font-size:0.8rem;">Fecha de Emisión</small>
                        </div>
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
            sound.playWelcome();
            launchConfetti();

            // Fecha en el diploma
            const now = new Date();
            document.getElementById('diplomaDate').textContent = now.toLocaleDateString('es-CL', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Cargar datos del último ensayo SIMCE desde localStorage si existen
            const raw = localStorage.getItem('ultimo_ensayo_simce');
            if (raw) {
                try {
                    const data = JSON.parse(raw);
                    const circle = document.getElementById('scorePercentCircle');
                    const badge = document.getElementById('simceLevelBadge');
                    const subtitle = document.getElementById('scoreSubtitleText');

                    circle.textContent = `${data.porcentaje}%`;

                    if (data.porcentaje >= 80) {
                        badge.textContent = 'NIVEL ADECUADO (SOBRESALIENTE) 🏆';
                        badge.style.color = '#10b981';
                        circle.style.background = 'linear-gradient(135deg, #10b981, #06b6d4)';
                    } else if (data.porcentaje >= 60) {
                        badge.textContent = 'NIVEL ELEMENTAL (BUEN CAMINO) 👍';
                        badge.style.color = '#f59e0b';
                        circle.style.background = 'linear-gradient(135deg, #f59e0b, #ec4899)';
                    } else {
                        badge.textContent = 'NIVEL INICIAL (REQUIERE REFUERZO) 🌱';
                        badge.style.color = '#60a5fa';
                        circle.style.background = 'linear-gradient(135deg, #3b82f6, #6366f1)';
                    }

                    subtitle.innerHTML = `Acertaste <b>${data.correctas} de ${data.total}</b> situaciones problemáticas del SIMCE.`;
                } catch (e) {
                    console.error('Error parseando ensayo:', e);
                }
            }

            // Botón Imprimir
            document.getElementById('btnPrintDiploma').addEventListener('click', () => {
                sound.playClick();
                window.print();
            });

            // Audio Toggle
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
        });
    </script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$nombre = htmlspecialchars($_SESSION['usuario']);
$curso = htmlspecialchars($_SESSION['curso'] ?? '4° Básico');
$avatar = htmlspecialchars($_SESSION['avatar'] ?? '🦊');
$rol = htmlspecialchars($_SESSION['rol'] ?? 'Estudiante');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiplica 4° Básico | Menú Principal</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <canvas id="bg-canvas"></canvas>

    <!-- Botón de Audio Flotante -->
    <button id="btn-audio-toggle" class="audio-toggle-btn" title="Activar o desactivar sonido" type="button">
        <svg id="icon-sound-on" viewBox="0 0 24 24">
            <path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zm-2 0L7 7H3v10h4l5 3.77V3.23zM14 8.27v7.46c1.3-.68 2.2-2.04 2.2-3.73s-.9-3.05-2.2-3.73z"/>
        </svg>
        <svg id="icon-sound-off" style="display:none;" viewBox="0 0 24 24">
            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
        </svg>
        <span id="audio-status-text">Sonido Activado</span>
    </button>

    <div class="app-shell">

        <!-- Barra del Perfil del Estudiante (Gamificación) -->
        <header class="student-bar">
            <div class="student-profile-badge">
                <div class="student-avatar-bubble"><?= $avatar ?></div>
                <div class="student-details">
                    <h2>¡Hola, <?= $nombre ?>! <span id="stat-lvl-title" style="font-size:0.8rem; color:#a5b4fc; font-weight:600;">(Nivel 1)</span></h2>
                    <span class="curso-tag"><?= $curso ?> • <?= $rol ?></span>
                </div>
            </div>

            <!-- Estadísticas Gamificadas -->
            <div class="student-stats">
                <div class="stat-pill xp-pill" title="Puntos de Experiencia">
                    ⚡ <span id="stat-xp">50</span> XP
                </div>
                <div class="stat-pill coin-pill" title="Monedas ganadas">
                    🪙 <span id="stat-coins">20</span> Monedas
                </div>
                <div class="stat-pill star-pill" title="Estrellas de maestría">
                    ⭐ <span id="stat-stars">5</span> Estrellas
                </div>
                <a href="logout.php" class="btn-logout" id="btnLogout" title="Salir de la cuenta">
                    Cerrar Sesión 🚪
                </a>
            </div>

            <!-- Barra de Nivel -->
            <div class="stat-progress-bar-wrap">
                <div class="stat-progress-bar" id="stat-xp-bar"></div>
            </div>
        </header>

        <!-- Banner de Adaptación a Aplicación Móvil (PWA) -->
        <section class="mobile-app-banner">
            <div class="banner-text">
                <h4>📱 ¡Lleva Multiplica 4° Básico en tu Celular o Tablet!</h4>
                <p>Esta plataforma funciona como App nativa. Puedes instalarla o añadirla a la pantalla de inicio.</p>
            </div>
            <button class="chip-btn" id="btnInstallApp" style="background:#6366f1; color:#fff; border-color:transparent; padding:0.6rem 1.1rem;">
                📲 Instalar Aplicación
            </button>
        </section>

        <!-- Título de Sección -->
        <div style="text-align: center; margin: 0.5rem 0 0.2rem;">
            <h2 style="font-family:'Outfit', sans-serif; font-size:1.85rem; font-weight:800; color:#fff;">
                ¿Qué deseas aprender y jugar hoy?
            </h2>
            <p style="color:var(--text-muted); font-size:0.95rem;">
                Elige una estación interactiva para avanzar en tu ruta de aprendizaje
            </p>
        </div>

        <!-- Cuadrícula de Módulos Principales -->
        <div class="modules-grid">

            <!-- 1. Laboratorio de Conceptos -->
            <a href="aprender.php" class="module-card">
                <div>
                    <span class="module-badge badge-purple">Módulo 1 • Comprensión</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">🔬</span>
                        <div class="module-title">
                            <h3>Laboratorio de Conceptos</h3>
                            <p>Descubre el porqué de multiplicar: matrices de puntos, saltos de la rana y sumas repetidas.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>Modelos visuales interactivos</span>
                    <span class="btn-arrow">Entrar al Lab →</span>
                </div>
            </a>

            <!-- 2. Parque de Juegos Dinámicos -->
            <a href="juegos.php" class="module-card">
                <div>
                    <span class="module-badge badge-pink">Módulo 2 • Gamificación</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">🎡</span>
                        <div class="module-title">
                            <h3>Parque de Juegos</h3>
                            <p>¡Cero monotonía! Revienta globos numéricos, compra en el mercado y haz saltar a la rana.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>3 minijuegos dinámicos</span>
                    <span class="btn-arrow">¡A Jugar! →</span>
                </div>
            </a>

            <!-- 3. Gimnasio de Tablas del 2 al 10 -->
            <a href="gimnasio.php" class="module-card">
                <div>
                    <span class="module-badge badge-amber">Módulo 3 • Entrenamiento</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">⚡</span>
                        <div class="module-title">
                            <h3>Gimnasio del 2 al 10</h3>
                            <p>Domina cada tabla individual con trucos mnemotécnicos, patrones numéricos y desafíos.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>Tablas del 2, 3, 4, 5... al 10</span>
                    <span class="btn-arrow">Entrenar →</span>
                </div>
            </a>

            <!-- 4. Ensayo Matemático SIMCE 4° Básico -->
            <a href="simce.php" class="module-card" style="border-color: rgba(16, 185, 129, 0.45);">
                <div>
                    <span class="module-badge badge-green">Módulo 4 • Evaluación Real</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">📝</span>
                        <div class="module-title">
                            <h3>Ensayo Tipo SIMCE</h3>
                            <p>10 situaciones contextualizadas de la vida real con alternativas, retroalimentación y análisis.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>Preguntas contextualizadas</span>
                    <span class="btn-arrow" style="color:#6ee7b7;">Comenzar Ensayo →</span>
                </div>
            </a>

            <!-- 5. Mis Medallas y Logros -->
            <div class="module-card" id="btnOpenBadges" style="cursor:pointer;">
                <div>
                    <span class="module-badge badge-blue">Módulo 5 • Recompensas</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">🏆</span>
                        <div class="module-title">
                            <h3>Álbum de Medallas</h3>
                            <p>Revisa tus 8 insignias de honor desbloqueadas por aprender, jugar y superar retos.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>Colección de logros</span>
                    <span class="btn-arrow">Ver Mis Medallas →</span>
                </div>
            </div>

            <!-- 6. Mi Diploma y Reporte -->
            <a href="resultados.php" class="module-card">
                <div>
                    <span class="module-badge badge-purple">Módulo 6 • Retroalimentación</span>
                    <div class="module-header" style="margin-top:0.8rem;">
                        <span class="module-icon">📜</span>
                        <div class="module-title">
                            <h3>Reporte y Diploma</h3>
                            <p>Consulta tus tablas fuertes y débiles, tu nivel de logro e imprime tu Diploma Oficial.</p>
                        </div>
                    </div>
                </div>
                <div class="module-footer">
                    <span>Certificado con tu nombre</span>
                    <span class="btn-arrow">Ver Diploma →</span>
                </div>
            </a>

        </div>

    </div>

    <!-- Modal del Álbum de Medallas -->
    <div id="modalBadges" class="level-up-modal-backdrop" style="display:none;">
        <div class="level-up-card" style="max-width:550px; background:#111827; border-color:#6366f1;">
            <h2 style="color:#f8fafc; font-size:1.6rem;">🏆 Tus Medallas y Logros</h2>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
                Completa actividades para desbloquear todas las recompensas
            </p>

            <div id="badgesGridModal" style="display:grid; grid-template-columns:1fr 1fr; gap:0.8rem; text-align:left;">
                <!-- Cargado dinámicamente con JavaScript -->
            </div>

            <button class="submit-btn" id="btnCloseBadges" style="margin-top:1.5rem;">
                Volver al Menú
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/sound.js"></script>
    <script src="js/particles.js"></script>
    <script src="js/confetti.js"></script>
    <script src="js/progress.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            sound.playWelcome();

            // Control de Audio
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

            // Sonidos en tarjetas
            document.querySelectorAll('.module-card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    sound.playKey();
                });
                card.addEventListener('click', () => {
                    sound.playClick();
                });
            });

            // Modal de Medallas
            const btnOpenBadges = document.getElementById('btnOpenBadges');
            const modalBadges = document.getElementById('modalBadges');
            const btnCloseBadges = document.getElementById('btnCloseBadges');
            const badgesGrid = document.getElementById('badgesGridModal');

            btnOpenBadges.addEventListener('click', () => {
                sound.playClick();
                badgesGrid.innerHTML = '';
                const logros = progress.data.logros;

                for (let key in logros) {
                    const l = logros[key];
                    const item = document.createElement('div');
                    item.style.background = l.obtenido ? 'rgba(99, 102, 241, 0.2)' : 'rgba(255, 255, 255, 0.05)';
                    item.style.border = l.obtenido ? '1px solid #818cf8' : '1px solid rgba(255, 255, 255, 0.1)';
                    item.style.borderRadius = '12px';
                    item.style.padding = '0.75rem';
                    item.style.display = 'flex';
                    item.style.gap = '0.6rem';
                    item.style.alignItems = 'center';
                    item.style.opacity = l.obtenido ? '1' : '0.55';

                    item.innerHTML = `
                        <div style="font-size:2rem;">${l.icono}</div>
                        <div>
                            <strong style="color:${l.obtenido ? '#fff' : '#94a3b8'}; font-size:0.9rem; display:block;">${l.nombre}</strong>
                            <small style="color:#94a3b8; font-size:0.75rem;">${l.desc}</small>
                            <span style="display:block; font-size:0.7rem; font-weight:700; color:${l.obtenido ? '#10b981' : '#64748b'};">
                                ${l.obtenido ? '✓ Obtenida' : '🔒 Bloqueada'}
                            </span>
                        </div>
                    `;
                    badgesGrid.appendChild(item);
                }

                modalBadges.style.display = 'flex';
            });

            btnCloseBadges.addEventListener('click', () => {
                sound.playClick();
                modalBadges.style.display = 'none';
            });

            // PWA Installation Prompt
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
            });

            document.getElementById('btnInstallApp').addEventListener('click', () => {
                sound.playClick();
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choice) => {
                        if (choice.outcome === 'accepted') {
                            alert('¡Genial! Multiplica 4° Básico se instaló en tu dispositivo.');
                        }
                        deferredPrompt = null;
                    });
                } else {
                    alert('📱 Para usar en tu celular o tablet:\n- En Google Chrome: pulsa los tres puntos (⋮) y selecciona "Instalar aplicación" o "Añadir a pantalla de inicio".\n- En Safari (iPhone/iPad): pulsa el botón Compartir y selecciona "Añadir a pantalla de inicio".');
                }
            });
        });
    </script>
</body>
</html>

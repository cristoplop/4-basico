<?php
session_start();

$error = '';
$success = '';

// Si ya inició sesión, redirigir al menú principal
if (isset($_SESSION['usuario']) && !isset($_GET['relogin'])) {
    header("Location: menu.php");
    exit;
}

// Avatares disponibles para 4° Básico
$avatares = [
    '🦊' => ['id' => 'zorro', 'nombre' => 'Zorro Astuto', 'color' => '#f97316'],
    '🚀' => ['id' => 'astronauta', 'nombre' => 'Astronauta', 'color' => '#6366f1'],
    '🦉' => ['id' => 'buho', 'nombre' => 'Búho ', 'color' => '#8b5cf6'],
    '🤖' => ['id' => 'robot', 'nombre' => 'MultiBot', 'color' => '#06b6d4'],
    '🐱' => ['id' => 'michi', 'nombre' => 'Michi', 'color' => '#ec4899'],
    '🦖' => ['id' => 'dino', 'nombre' => 'Dino', 'color' => '#10b981'],
    '🦁' => ['id' => 'leon', 'nombre' => 'León', 'color' => '#eab308'],
    '🦄' => ['id' => 'unicornio', 'nombre' => 'Unicornio', 'color' => '#d946ef']
];

// Procesar ingreso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $curso = trim($_POST['curso'] ?? '4° Básico A');
    $avatar = trim($_POST['avatar'] ?? '🦊');
    $password = trim($_POST['password'] ?? '');

    // Cuentas de demostración de profesor o admin
    $cuentasEspeciales = [
        'profesor' => ['pass' => 'mate2026', 'nombre' => 'Profesor de Matemáticas', 'rol' => 'Profesor', 'avatar' => '👨‍🏫'],
        'admin'    => ['pass' => 'admin123', 'nombre' => 'Administrador Escolar', 'rol' => 'Administrador', 'avatar' => '⚡']
    ];

    if (empty($nombre)) {
        $error = '¡Por favor ingresa tu nombre de estudiante!';
    } elseif (isset($cuentasEspeciales[strtolower($nombre)])) {
        $cuenta = $cuentasEspeciales[strtolower($nombre)];
        if ($password === $cuenta['pass']) {
            $_SESSION['usuario'] = $cuenta['nombre'];
            $_SESSION['curso'] = 'Docente';
            $_SESSION['rol'] = $cuenta['rol'];
            $_SESSION['avatar'] = $cuenta['avatar'];
            $success = '¡Bienvenido, ' . $cuenta['nombre'] . '!';
        } else {
            $error = 'Contraseña incorrecta para cuenta docente.';
        }
    } else {
        // Ingreso normal del estudiante
        if (mb_strlen($nombre) < 2) {
            $error = 'El nombre debe tener al menos 2 letras.';
        } else {
            $_SESSION['usuario'] = ucfirst($nombre);
            $_SESSION['curso'] = $curso;
            $_SESSION['rol'] = 'Estudiante 4° Básico';
            $_SESSION['avatar'] = array_key_exists($avatar, $avatares) ? $avatar : '🦊';
            $success = '¡Hola ' . htmlspecialchars(ucfirst($nombre)) . '! Preparando tu aventura matemática...';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiplica 4° Básico | Ingreso y Selección de Avatar</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Fondo de fórmulas dinámicas -->
    <canvas id="bg-canvas"></canvas>

    <!-- Botón Flotante de Audio -->
    <button id="btn-audio-toggle" class="audio-toggle-btn" title="Activar o silenciar sonido" type="button">
        <svg id="icon-sound-on" viewBox="0 0 24 24">
            <path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zm-2 0L7 7H3v10h4l5 3.77V3.23zM14 8.27v7.46c1.3-.68 2.2-2.04 2.2-3.73s-.9-3.05-2.2-3.73z"/>
        </svg>
        <svg id="icon-sound-off" style="display:none;" viewBox="0 0 24 24">
            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
        </svg>
        <span id="audio-status-text">Sonido Activado</span>
    </button>

    <main class="app-shell" style="max-width: 540px; margin: auto;">
        <div class="login-card" id="loginCard">

            <!-- Mascota Interactiva MultiBot -->
            <div class="mascot-wrapper">
                <svg id="mascot-svg" viewBox="0 0 100 100">
                    <ellipse cx="50" cy="94" rx="34" ry="5" fill="rgba(0,0,0,0.35)"/>
                    <line x1="50" y1="20" x2="50" y2="8" stroke="#6366f1" stroke-width="4" stroke-linecap="round"/>
                    <circle id="mascot-antenna-light" cx="50" cy="7" r="5" fill="#10b981">
                        <animate attributeName="r" values="4.5;6;4.5" dur="1.8s" repeatCount="indefinite" />
                    </circle>
                    <rect x="20" y="20" width="60" height="58" rx="18" fill="#1e293b" stroke="#6366f1" stroke-width="3"/>
                    <rect x="13" y="38" width="8" height="20" rx="4" fill="#3b82f6"/>
                    <rect x="79" y="38" width="8" height="20" rx="4" fill="#3b82f6"/>
                    <rect x="27" y="27" width="46" height="42" rx="12" fill="#0f172a"/>
                    <g id="eye-left-group">
                        <circle cx="39" cy="45" r="9" fill="#1e293b" stroke="#38bdf8" stroke-width="2"/>
                        <circle id="pupil-left" cx="39" cy="45" r="4.5" fill="#38bdf8"/>
                    </g>
                    <g id="eye-right-group">
                        <circle cx="61" cy="45" r="9" fill="#1e293b" stroke="#38bdf8" stroke-width="2"/>
                        <circle id="pupil-right" cx="61" cy="45" r="4.5" fill="#38bdf8"/>
                    </g>
                    <g id="eye-cover-left" class="eye-cover">
                        <circle cx="39" cy="45" r="9.5" fill="#ec4899"/>
                        <text x="39" y="49" font-size="10" font-weight="bold" fill="#ffffff" text-anchor="middle">×</text>
                    </g>
                    <g id="eye-cover-right" class="eye-cover">
                        <circle cx="61" cy="45" r="9.5" fill="#ec4899"/>
                        <text x="61" y="49" font-size="10" font-weight="bold" fill="#ffffff" text-anchor="middle">×</text>
                    </g>
                    <path id="mascot-mouth" d="M 40 73 Q 50 82 60 73" fill="none" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Cabecera -->
            <header style="text-align: center; margin-bottom: 1.4rem;">
                <span class="badge-tag">🎒 Plataforma Escolar 4° Básico</span>
                <h1 style="font-family:'Outfit', sans-serif; font-size:1.85rem; font-weight:800; margin:0.3rem 0;">
                    El Mundo de las Tablas
                </h1>
                <p style="color:var(--text-muted); font-size:0.92rem;">
                    Aprende, juega, supera desafíos y prepárate para el SIMCE
                </p>
            </header>

            <!-- Alertas PHP -->
            <?php if (!empty($error)): ?>
                <div class="alert-box alert-error">
                    <span>⚠️ <?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-box alert-success">
                    <span>🎉 <?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulario de Identificación y Selección de Avatar -->
            <form action="index.php" method="POST" class="login-form" id="studentForm">
                
                <!-- Nombre del Estudiante -->
                <div class="input-group">
                    <label class="input-label" for="nombreEstudiante">Tu Nombre y Apellido:</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            name="nombre" 
                            id="nombreEstudiante" 
                            class="form-input" 
                            placeholder="Ej. Matías Silva, Valentina..." 
                            value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                            required
                            autocomplete="name"
                        >
                    </div>
                </div>

                <!-- Selección de Curso -->
                <div class="input-group">
                    <label class="input-label" for="cursoEstudiante">Curso:</label>
                    <select name="curso" id="cursoEstudiante" class="form-select">
                        <option value="4° Básico A">4° Básico A</option>
                        <option value="4° Básico B">4° Básico B</option>
                        <option value="4° Básico C">4° Básico C</option>
                        <option value="4° Básico D">4° Básico D</option>
                    </select>
                </div>

                <!-- Campo Oculto para Contraseña Docente si corresponde -->
                <div class="input-group" id="passWrapper" style="display:none;">
                    <label class="input-label" for="password">Contraseña de Profesor / Admin:</label>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Clave secreta docente">
                </div>

                <!-- SELECCIÓN DE AVATAR (8 Opciones) -->
                <div>
                    <div class="avatar-selection-title">
                        <span>Elige tu Avatar de Aventurero:</span>
                        <span id="avatarSelectedName" style="color:#ec4899; font-weight:800;">Zorro Astuto</span>
                    </div>

                    <input type="hidden" name="avatar" id="selectedAvatarInput" value="🦊">

                    <div class="avatar-grid" id="avatarGrid">
                        <?php 
                        $first = true;
                        foreach ($avatares as $emoji => $info): 
                        ?>
                            <div class="avatar-card <?= $first ? 'selected' : '' ?>" data-emoji="<?= $emoji ?>" data-name="<?= $info['nombre'] ?>">
                                <span class="avatar-emoji"><?= $emoji ?></span>
                                <span class="avatar-name"><?= $info['nombre'] ?></span>
                            </div>
                        <?php 
                            $first = false;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Botón de Entrada -->
                <button type="submit" class="submit-btn" id="btnIngresar">
                    <span>¡Comenzar Mi Aventura! 🚀</span>
                </button>
            </form>


                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- Scripts -->
    <script src="js/sound.js"></script>
    <script src="js/particles.js"></script>
    <script src="js/mascot.js"></script>
    <script src="js/confetti.js"></script>
    <script src="js/progress.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('studentForm');
            const loginCard = document.getElementById('loginCard');
            const nombreInput = document.getElementById('nombreEstudiante');
            const passWrapper = document.getElementById('passWrapper');
            const passInput = document.getElementById('password');
            const avatarInput = document.getElementById('selectedAvatarInput');
            const avatarSelectedText = document.getElementById('avatarSelectedName');
            const avatarCards = document.querySelectorAll('.avatar-card');

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

            // Selección interactiva de Avatar con sonido
            avatarCards.forEach(card => {
                card.addEventListener('click', () => {
                    sound.playClick();
                    avatarCards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');

                    const emoji = card.getAttribute('data-emoji');
                    const name = card.getAttribute('data-name');
                    avatarInput.value = emoji;
                    avatarSelectedText.textContent = name;
                });
            });

            // Detectar si escribe "profesor" o "admin" para desplegar clave
            nombreInput.addEventListener('input', () => {
                sound.playKey();
                const val = nombreInput.value.toLowerCase().trim();
                if (val === 'profesor' || val === 'admin') {
                    passWrapper.style.display = 'flex';
                } else {
                    passWrapper.style.display = 'none';
                }
            });

            // Botones de demostración rápida
            document.querySelectorAll('.chip-btn').forEach(chip => {
                chip.addEventListener('click', () => {
                    sound.playClick();
                    const name = chip.getAttribute('data-name');
                    const curso = chip.getAttribute('data-curso');
                    const emoji = chip.getAttribute('data-emoji');
                    const pass = chip.getAttribute('data-pass');

                    if (name) nombreInput.value = name;
                    if (curso) document.getElementById('cursoEstudiante').value = curso;

                    if (emoji) {
                        avatarCards.forEach(c => {
                            if (c.getAttribute('data-emoji') === emoji) {
                                c.click();
                            }
                        });
                    }

                    if (pass) {
                        passWrapper.style.display = 'flex';
                        passInput.value = pass;
                    }
                });
            });

            // Validación al enviar
            form.addEventListener('submit', (e) => {
                const nameVal = nombreInput.value.trim();
                if (!nameVal) {
                    e.preventDefault();
                    sound.playError();
                    if (window.mascotSetState) mascotSetState('error');
                    loginCard.classList.remove('shake-form');
                    void loginCard.offsetWidth;
                    loginCard.classList.add('shake-form');
                    nombreInput.focus();
                    return;
                }
                sound.playClick();
            });

            // Alertas PHP
            <?php if (!empty($error)): ?>
                sound.playError();
                if (window.mascotSetState) mascotSetState('error');
                loginCard.classList.add('shake-form');
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                sound.playSuccess();
                if (window.mascotSetState) mascotSetState('success');
                if (window.launchConfetti) launchConfetti();
                // Desbloquear logro inicial
                progress.unlockBadge('primer_paso');
                setTimeout(() => {
                    window.location.href = 'menu.php';
                }, 1300);
            <?php endif; ?>

            // Registro de Service Worker para PWA (Móvil)
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('sw.js').catch(err => {
                    console.log('SW registration note:', err);
                });
            }
        });
    </script>
</body>
</html>

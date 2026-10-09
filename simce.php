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
    <title>Ensayo Matemático Tipo SIMCE | 4° Básico</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#6366f1">
    <link rel="stylesheet" href="css/style.css">
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
                    <h2>📝 Ensayo Matemático Oficial Tipo SIMCE</h2>
                    <span>4° Básico • Situaciones de la Vida Cotidiana</span>
                </div>
            </div>
            <div class="nav-actions">
                <div class="stat-pill star-pill">⭐ Pregunta <span id="currentQNum">1</span> de 10</div>
            </div>
        </header>

        <main class="simce-card">

            <div class="simce-header-bar">
                <div>
                    <span class="badge-tag" style="background:rgba(16, 185, 129, 0.2); color:#6ee7b7; border-color:#10b981;">
                        Eje Temático: Números y Operaciones (OA 8 y OA 11)
                    </span>
                </div>
                <div style="font-size:0.88rem; color:var(--text-muted); font-weight:700;">
                    Aciertos: <b id="simceScoreLive" style="color:#10b981;">0</b> / 10
                </div>
            </div>

            <!-- Pregunta Contextualizada -->
            <div class="simce-question-box" id="questionText">
                Cargando situación matemática...
            </div>

            <!-- Ilustración contextual -->
            <div class="simce-context-illustration" id="illustrationBox">
                📚 📚 📚
            </div>

            <!-- Alternativas A, B, C, D -->
            <div class="simce-options-grid" id="optionsGrid">
                <!-- Generadas por JavaScript -->
            </div>

            <!-- Caja de Retroalimentación Formativa Inmediata -->
            <div id="feedbackContainer" style="display:none;"></div>

            <!-- Botón Siguiente Pregunta -->
            <div style="display:flex; justify-content:flex-end; margin-top:0.5rem;">
                <button class="submit-btn" id="btnNextQuestion" style="width:auto; padding:0.85rem 1.8rem; display:none;">
                    Siguiente Pregunta →
                </button>
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

            // BANCO DE 10 PREGUNTAS CONTEXTUALIZADAS TIPO SIMCE 4° BÁSICO
            const simceQuestions = [
                {
                    id: 1,
                    context: "En la biblioteca de la escuela hay 6 estantes. Si en cada estante se ordenaron exactamente 8 libros de cuentos, ¿cuántos libros de cuentos hay en total en la biblioteca?",
                    icons: "📚 📚 📚 📚 📚 📚 📚 📚<br><small style='font-size:0.8rem; color:#94a3b8;'>(6 estantes con 8 libros cada uno)</small>",
                    tablaInvolucrada: 8,
                    options: [
                        { letter: "A", text: "14 libros", correct: false, errorHelp: "Cuidado: sumaste 6 + 8 en lugar de multiplicar los 6 grupos de 8." },
                        { letter: "B", text: "42 libros", correct: false, errorHelp: "42 corresponde a 6 × 7. En cada estante hay 8 libros." },
                        { letter: "C", text: "48 libros", correct: true, explain: "¡Excelente! Multiplicamos 6 estantes × 8 libros = 48 libros en total." },
                        { letter: "D", text: "56 libros", correct: false, errorHelp: "56 corresponde a 7 × 8. Son 6 estantes." }
                    ]
                },
                {
                    id: 2,
                    context: "Los estudiantes de 4° básico sembraron un huerto escolar con 5 hileras de lechugas. En cada hilera plantaron 9 lechugas. ¿Cuántas lechugas plantaron en total los estudiantes?",
                    icons: "🥬 🥬 🥬 🥬 🥬 🥬 🥬 🥬 🥬<br><small style='font-size:0.8rem; color:#94a3b8;'>(5 hileras con 9 lechugas)</small>",
                    tablaInvolucrada: 9,
                    options: [
                        { letter: "A", text: "45 lechugas", correct: true, explain: "¡Correcto! 5 hileras de 9 lechugas corresponden a 5 × 9 = 45 lechugas en el huerto." },
                        { letter: "B", text: "40 lechugas", correct: false, errorHelp: "40 es 5 × 8. En cada hilera hay 9 lechugas." },
                        { letter: "C", text: "14 lechugas", correct: false, errorHelp: "Sumaste 5 + 9. Recuerda que son grupos repetidos de lechugas." },
                        { letter: "D", text: "54 lechugas", correct: false, errorHelp: "54 es 6 × 9. Son solo 5 hileras." }
                    ]
                },
                {
                    id: 3,
                    context: "Para la ceremonia del colegio, el profesor ordenó las sillas en 7 filas con 7 sillas en cada fila. ¿Cuántas sillas dispuso en total el profesor?",
                    icons: "🪑 🪑 🪑 🪑 🪑 🪑 🪑<br><small style='font-size:0.8rem; color:#94a3b8;'>(Arreglo cuadrado de 7 filas × 7 sillas)</small>",
                    tablaInvolucrada: 7,
                    options: [
                        { letter: "A", text: "14 sillas", correct: false, errorHelp: "Sumaste 7 + 7. Para saber el total de un arreglo rectangular se multiplica." },
                        { letter: "B", text: "42 sillas", correct: false, errorHelp: "42 es 7 × 6. Aquí son 7 filas de 7 sillas." },
                        { letter: "C", text: "49 sillas", correct: true, explain: "¡Muy bien! Un arreglo de 7 × 7 da exactamente 49 sillas." },
                        { letter: "D", text: "56 sillas", correct: false, errorHelp: "56 es 7 × 8." }
                    ]
                },
                {
                    id: 4,
                    context: "En una pastelería venden cajas de alfajores artesanales. Cada caja contiene 6 alfajores. Si la señora Carmen compró 4 cajas para su familia, ¿cuántos alfajores compró en total?",
                    icons: "🍪 🍪 🍪 🍪 🍪 🍪<br><small style='font-size:0.8rem; color:#94a3b8;'>(4 cajas con 6 alfajores cada una)</small>",
                    tablaInvolucrada: 6,
                    options: [
                        { letter: "A", text: "10 alfajores", correct: false, errorHelp: "Sumaste 4 + 6. Cada una de las 4 cajas trae 6 alfajores." },
                        { letter: "B", text: "24 alfajores", correct: true, explain: "¡Excelente! Multiplicamos 4 cajas × 6 alfajores = 24 alfajores." },
                        { letter: "C", text: "28 alfajores", correct: false, errorHelp: "28 es 4 × 7." },
                        { letter: "D", text: "30 alfajores", correct: false, errorHelp: "30 es 5 × 6. Solo compró 4 cajas." }
                    ]
                },
                {
                    id: 5,
                    context: "Joaquín guardó en su alcancía 8 monedas de $10 que le sobraron de sus colaciones. ¿Cuánto dinero ahorró Joaquín en total?",
                    icons: "🪙 🪙 🪙 🪙 🪙 🪙 🪙 🪙<br><small style='font-size:0.8rem; color:#94a3b8;'>(8 monedas de $10)</small>",
                    tablaInvolucrada: 10,
                    options: [
                        { letter: "A", text: "$18", correct: false, errorHelp: "Sumaste 8 + 10. Son 8 veces diez pesos." },
                        { letter: "B", text: "$80", correct: true, explain: "¡Correcto! 8 monedas de $10 equivalen a 8 × 10 = $80." },
                        { letter: "C", text: "$70", correct: false, errorHelp: "70 son 7 monedas de $10." },
                        { letter: "D", text: "$800", correct: false, errorHelp: "$800 serían monedas de $100." }
                    ]
                },
                {
                    id: 6,
                    context: "Para un paseo escolar al zoológico se contrataron 3 furgones. En cada furgón viajan 9 estudiantes. ¿Cuántos estudiantes viajan en total al paseo?",
                    icons: "🚐 🚐 🚐<br><small style='font-size:0.8rem; color:#94a3b8;'>(3 furgones con 9 estudiantes cada uno)</small>",
                    tablaInvolucrada: 9,
                    options: [
                        { letter: "A", text: "12 estudiantes", correct: false, errorHelp: "Sumaste 3 + 9 en vez de multiplicar." },
                        { letter: "B", text: "24 estudiantes", correct: false, errorHelp: "24 es 3 × 8. En cada furgón van 9 alumnos." },
                        { letter: "C", text: "27 estudiantes", correct: true, explain: "¡Muy bien! 3 furgones × 9 estudiantes = 27 estudiantes en total." },
                        { letter: "D", text: "36 estudiantes", correct: false, errorHelp: "36 es 4 × 9." }
                    ]
                },
                {
                    id: 7,
                    context: "En la sala de clases hay 8 paquetes de lápices de colores para compartir. Cada paquete contiene 4 lápices. ¿Cuántos lápices de colores hay en total?",
                    icons: "✏️ ✏️ ✏️ ✏️<br><small style='font-size:0.8rem; color:#94a3b8;'>(8 paquetes con 4 lápices cada uno)</small>",
                    tablaInvolucrada: 4,
                    options: [
                        { letter: "A", text: "32 lápices", correct: true, explain: "¡Correcto! 8 paquetes × 4 lápices = 32 lápices en total." },
                        { letter: "B", text: "12 lápices", correct: false, errorHelp: "Sumaste 8 + 4. Recuerda calcular 8 veces cuatro." },
                        { letter: "C", text: "28 lápices", correct: false, errorHelp: "28 es 7 × 4." },
                        { letter: "D", text: "36 lápices", correct: false, errorHelp: "36 es 9 × 4." }
                    ]
                },
                {
                    id: 8,
                    context: "Sofía compró 6 sobres de láminas para completar su álbum. Si cada sobre contiene exactamente 5 láminas, ¿cuántas láminas nuevas tiene Sofía?",
                    icons: "🎴 🎴 🎴 🎴 🎴 🎴<br><small style='font-size:0.8rem; color:#94a3b8;'>(6 sobres con 5 láminas)</small>",
                    tablaInvolucrada: 5,
                    options: [
                        { letter: "A", text: "11 láminas", correct: false, errorHelp: "Sumaste 6 + 5." },
                        { letter: "B", text: "25 láminas", correct: false, errorHelp: "25 es 5 × 5. Son 6 sobres." },
                        { letter: "C", text: "30 láminas", correct: true, explain: "¡Excelente! 6 sobres × 5 láminas = 30 láminas." },
                        { letter: "D", text: "35 láminas", correct: false, errorHelp: "35 es 7 × 5." }
                    ]
                },
                {
                    id: 9,
                    context: "Matías dice: 'Construí 4 torres con 3 bloques cada una'. Valentina dice: 'Yo construí 3 torres con 4 bloques cada una'. ¿Quién utilizó más bloques en total?",
                    icons: "🧱 🧱 🧱 🧱 vs 🧱 🧱 🧱<br><small style='font-size:0.8rem; color:#94a3b8;'>(Propiedad conmutativa: 4 × 3 vs 3 × 4)</small>",
                    tablaInvolucrada: 3,
                    options: [
                        { letter: "A", text: "Matías, porque tiene 4 torres", correct: false, errorHelp: "Aunque tenga más torres, cada una tiene menos bloques." },
                        { letter: "B", text: "Valentina, porque sus torres son más altas", correct: false, errorHelp: "Aunque sus torres sean más altas, tiene menos torres." },
                        { letter: "C", text: "Ambos usaron la misma cantidad (12 bloques)", correct: true, explain: "¡Brillante! Por la propiedad conmutativa: 4 × 3 = 12 y 3 × 4 = 12. Ambos usaron 12 bloques." },
                        { letter: "D", text: "No se puede saber sin medirlos", correct: false, errorHelp: "Sí se puede saber multiplicando la cantidad de torres por bloques." }
                    ]
                },
                {
                    id: 10,
                    context: "Para hornear una bandeja de ricas galletas se necesitan 2 huevos. Si en el taller de cocina hornearán 9 bandejas de galletas, ¿cuántos huevos necesitarán en total?",
                    icons: "🥚 🥚<br><small style='font-size:0.8rem; color:#94a3b8;'>(9 bandejas × 2 huevos)</small>",
                    tablaInvolucrada: 2,
                    options: [
                        { letter: "A", text: "11 huevos", correct: false, errorHelp: "Sumaste 9 + 2 en vez de duplicar." },
                        { letter: "B", text: "18 huevos", correct: true, explain: "¡Excelente! 9 bandejas × 2 huevos = 18 huevos en total." },
                        { letter: "C", text: "16 huevos", correct: false, errorHelp: "16 es 8 × 2. Son 9 bandejas." },
                        { letter: "D", text: "20 huevos", correct: false, errorHelp: "20 es 10 × 2." }
                    ]
                }
            ];

            let currentIndex = 0;
            let correctAnswersCount = 0;
            let answered = false;

            const qNumEl = document.getElementById('currentQNum');
            const liveScoreEl = document.getElementById('simceScoreLive');
            const qTextEl = document.getElementById('questionText');
            const illustrationEl = document.getElementById('illustrationBox');
            const optionsGrid = document.getElementById('optionsGrid');
            const feedbackContainer = document.getElementById('feedbackContainer');
            const btnNext = document.getElementById('btnNextQuestion');

            // Historial de respuestas para el reporte final
            const resultadosDetalle = [];

            function loadQuestion(idx) {
                answered = false;
                feedbackContainer.style.display = 'none';
                feedbackContainer.innerHTML = '';
                btnNext.style.display = 'none';

                const q = simceQuestions[idx];
                qNumEl.textContent = idx + 1;
                qTextEl.textContent = `${idx + 1}. ${q.context}`;
                illustrationEl.innerHTML = q.icons;
                optionsGrid.innerHTML = '';

                q.options.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'simce-option-btn';
                    btn.innerHTML = `
                        <span class="option-letter">${opt.letter}</span>
                        <span>${opt.text}</span>
                    `;

                    btn.addEventListener('click', () => {
                        if (answered) return;
                        checkAnswer(btn, opt, q);
                    });

                    optionsGrid.appendChild(btn);
                });
            }

            function checkAnswer(selectedBtn, opt, question) {
                answered = true;
                const allButtons = optionsGrid.querySelectorAll('.simce-option-btn');

                if (opt.correct) {
                    sound.playCorrect();
                    selectedBtn.style.borderColor = '#10b981';
                    selectedBtn.style.background = 'rgba(16, 185, 129, 0.25)';
                    correctAnswersCount++;
                    liveScoreEl.textContent = correctAnswersCount;

                    feedbackContainer.className = 'feedback-box correct';
                    feedbackContainer.innerHTML = `
                        <h4>🎉 ¡Respuesta Correcta!</h4>
                        <p>${opt.explain}</p>
                    `;
                } else {
                    sound.playError();
                    selectedBtn.style.borderColor = '#ef4444';
                    selectedBtn.style.background = 'rgba(239, 68, 68, 0.25)';

                    // Resaltar la correcta
                    allButtons.forEach(b => {
                        const letter = b.querySelector('.option-letter').textContent;
                        const optMatch = question.options.find(o => o.letter === letter);
                        if (optMatch && optMatch.correct) {
                            b.style.borderColor = '#10b981';
                            b.style.background = 'rgba(16, 185, 129, 0.2)';
                        }
                    });

                    feedbackContainer.className = 'feedback-box wrong';
                    feedbackContainer.innerHTML = `
                        <h4>💡 Retroalimentación Formativa:</h4>
                        <p>${opt.errorHelp}</p>
                    `;
                }

                feedbackContainer.style.display = 'block';
                btnNext.style.display = 'inline-flex';

                // Guardar en detalle
                resultadosDetalle.push({
                    pregunta: question.context,
                    acerto: opt.correct,
                    tabla: question.tablaInvolucrada
                });

                if (currentIndex === simceQuestions.length - 1) {
                    btnNext.textContent = 'Ver Resultado Final y Diploma 🏆';
                }
            }

            btnNext.addEventListener('click', () => {
                sound.playClick();
                currentIndex++;

                if (currentIndex < simceQuestions.length) {
                    loadQuestion(currentIndex);
                } else {
                    // Finalizó el ensayo SIMCE
                    finishSimce();
                }
            });

            function finishSimce() {
                // Guardar en localStorage / Progress
                progress.unlockBadge('listo_simce');
                if (correctAnswersCount === 10) {
                    progress.unlockBadge('genio_100');
                }

                progress.addXP(correctAnswersCount * 15);
                progress.addCoins(correctAnswersCount * 5);

                const dataToSave = {
                    total: 10,
                    correctas: correctAnswersCount,
                    porcentaje: Math.round((correctAnswersCount / 10) * 100),
                    fecha: new Date().toLocaleDateString(),
                    detalles: resultadosDetalle
                };

                localStorage.setItem('ultimo_ensayo_simce', JSON.stringify(dataToSave));

                window.location.href = 'resultados.php';
            }

            loadQuestion(0);
        });
    </script>
</body>
</html>

/**
 * progress.js - Sistema Gamificado de Progreso, Niveles, Puntos y Logros
 * Guarda en localStorage y sincroniza estado en la plataforma
 */

class ProgressManager {
    constructor() {
        this.storageKey = 'multiplica_progreso_4to';
        this.data = this.load();
    }

    getDefaults() {
        return {
            xp: 50,
            monedas: 20,
            estrellas: 5,
            actividadesCompletadas: 0,
            tablasPracticadas: [2],
            logros: {
                'primer_paso': { nombre: '¡Primer Paso!', desc: 'Ingresar a la plataforma de 4° Básico', icono: '🚀', obtenido: true },
                'maestro_matrices': { nombre: 'Maestro de Matrices', desc: 'Explorar filas y columnas en el laboratorio', icono: '🧱', obtenido: false },
                'cazador_globos': { nombre: 'Explotador de Globos', desc: 'Acertar 5 multiplicaciones con globos', icono: '🎈', obtenido: false },
                'chef_mercado': { nombre: 'Comprador Experto', desc: 'Resolver compras en la tiendita', icono: '🛒', obtenido: false },
                'salto_ranita': { nombre: 'Ranita Saltarina', desc: 'Hacer saltos exactos en la recta numérica', icono: '🐸', obtenido: false },
                'tabla_dificil': { nombre: 'Domador del 7 y 8', desc: 'Aprender el truco de la tabla del 7 u 8', icono: '⚡', obtenido: false },
                'listo_simce': { nombre: 'Héroe del SIMCE', desc: 'Completar el ensayo contextualizado', icono: '🎓', obtenido: false },
                'genio_100': { nombre: 'Puntaje Perfecto', desc: 'Lograr 100% en una prueba o reto', icono: '👑', obtenido: false }
            },
            simceHistorial: []
        };
    }

    load() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (raw) {
                const parsed = JSON.parse(raw);
                return { ...this.getDefaults(), ...parsed };
            }
        } catch (e) {
            console.error('Error cargando progreso:', e);
        }
        return this.getDefaults();
    }

    save() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.data));
            this.updateUI();
        } catch (e) {
            console.error('Error guardando progreso:', e);
        }
    }

    addXP(amount) {
        const prevLevel = this.getLevel().nivel;
        this.data.xp += amount;
        this.save();

        const newLevel = this.getLevel().nivel;
        if (newLevel > prevLevel) {
            this.showLevelUpModal(newLevel);
        }
    }

    addCoins(amount) {
        this.data.monedas += amount;
        this.save();
        if (window.sound) sound.playCoin();
    }

    addStars(amount) {
        this.data.estrellas += amount;
        this.save();
        if (window.sound) sound.playSuccess();
    }

    unlockBadge(badgeKey) {
        if (this.data.logros[badgeKey] && !this.data.logros[badgeKey].obtenido) {
            this.data.logros[badgeKey].obtenido = true;
            this.data.xp += 100;
            this.data.monedas += 30;
            this.save();
            this.showBadgeNotification(this.data.logros[badgeKey]);
        }
    }

    getLevel() {
        const xp = this.data.xp;
        if (xp < 150) return { nivel: 1, titulo: 'Novato de los Números', min: 0, max: 150, icon: '🌱' };
        if (xp < 350) return { nivel: 2, titulo: 'Explorador Matemático', min: 150, max: 350, icon: '🔍' };
        if (xp < 650) return { nivel: 3, titulo: 'Maestro de Matrices', min: 350, max: 650, icon: '⭐' };
        if (xp < 1000) return { nivel: 4, titulo: 'Campeón de 4° Básico', min: 650, max: 1000, icon: '🏆' };
        return { nivel: 5, titulo: 'Genio Legendario', min: 1000, max: 2000, icon: '👑' };
    }

    showBadgeNotification(badge) {
        if (window.sound) sound.playSuccess();
        if (window.launchConfetti) launchConfetti();

        const toast = document.createElement('div');
        toast.className = 'badge-toast';
        toast.innerHTML = `
            <div class="toast-icon">${badge.icono}</div>
            <div class="toast-content">
                <span class="toast-title">¡Nuevo Logro Desbloqueado!</span>
                <strong>${badge.nombre}</strong>
                <p>${badge.desc}</p>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 4500);
    }

    showLevelUpModal(newLevel) {
        if (window.sound) sound.playSuccess();
        if (window.launchConfetti) launchConfetti();

        const lvlInfo = this.getLevel();
        const modal = document.createElement('div');
        modal.className = 'level-up-modal-backdrop';
        modal.innerHTML = `
            <div class="level-up-card">
                <div class="lvl-badge-giant">${lvlInfo.icon}</div>
                <h2>¡SUBISTE DE NIVEL!</h2>
                <div class="lvl-number">NIVEL ${newLevel}</div>
                <p class="lvl-title">${lvlInfo.titulo}</p>
                <p>¡Tus habilidades multiplicativas están creciendo! Sigue así.</p>
                <button class="submit-btn" id="btnCloseLvlUp">¡Genial! 🚀</button>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('btnCloseLvlUp').addEventListener('click', () => {
            if (window.sound) sound.playClick();
            modal.remove();
        });
    }

    updateUI() {
        const lvl = this.getLevel();
        const xpPercent = Math.min(100, Math.max(0, ((this.data.xp - lvl.min) / (lvl.max - lvl.min)) * 100));

        // Actualizar elementos en pantalla si existen
        const xpText = document.getElementById('stat-xp');
        if (xpText) xpText.textContent = this.data.xp;

        const coinText = document.getElementById('stat-coins');
        if (coinText) coinText.textContent = this.data.monedas;

        const starText = document.getElementById('stat-stars');
        if (starText) starText.textContent = this.data.estrellas;

        const lvlNum = document.getElementById('stat-lvl-num');
        if (lvlNum) lvlNum.textContent = lvl.nivel;

        const lvlTitle = document.getElementById('stat-lvl-title');
        if (lvlTitle) lvlTitle.textContent = lvl.titulo;

        const xpBar = document.getElementById('stat-xp-bar');
        if (xpBar) xpBar.style.width = `${xpPercent}%`;
    }
}

const progress = new ProgressManager();
document.addEventListener('DOMContentLoaded', () => {
    progress.updateUI();
});

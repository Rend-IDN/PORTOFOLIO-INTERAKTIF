class ThemeManager {
    constructor() { this.theme = localStorage.getItem('theme') || 'light'; this.init(); }
    init() { this.applyTheme(this.theme); this.createThemeToggle(); }
    applyTheme(theme) { if (theme === 'dark') document.body.classList.add('dark-theme'); else document.body.classList.remove('dark-theme'); }
    createThemeToggle() {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'theme-toggle';
        toggleBtn.innerHTML = this.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        toggleBtn.onclick = () => {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            this.applyTheme(this.theme);
            toggleBtn.innerHTML = this.theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        };
        document.body.appendChild(toggleBtn);
    }
}

class ScrollProgress {
    constructor() { this.createProgressBar(); window.addEventListener('scroll', () => this.updateProgress()); }
    createProgressBar() { this.progressBar = document.createElement('div'); this.progressBar.className = 'scroll-progress'; document.body.appendChild(this.progressBar); }
    updateProgress() { const winScroll = document.documentElement.scrollTop; const height = document.documentElement.scrollHeight - document.documentElement.clientHeight; this.progressBar.style.width = (winScroll / height) * 100 + '%'; }
}

class ScrollReveal {
    constructor() {
        const revealElements = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('revealed'); observer.unobserve(entry.target); } }); }, { threshold: 0.1 });
        revealElements.forEach(el => observer.observe(el));
    }
}

class CustomCursor {
    constructor() {
        if (window.innerWidth <= 768) return;
        this.cursor = document.createElement('div'); this.cursor.className = 'custom-cursor';
        this.cursorDot = document.createElement('div'); this.cursorDot.className = 'custom-cursor-dot';
        document.body.appendChild(this.cursor); document.body.appendChild(this.cursorDot);
        document.addEventListener('mousemove', (e) => { this.cursor.style.transform = `translate(${e.clientX - 15}px, ${e.clientY - 15}px)`; this.cursorDot.style.transform = `translate(${e.clientX - 3}px, ${e.clientY - 3}px)`; });
        document.querySelectorAll('a, button, .btn, .card').forEach(el => {
            el.addEventListener('mouseenter', () => { this.cursor.classList.add('cursor-hover'); this.cursorDot.classList.add('cursor-hover'); });
            el.addEventListener('mouseleave', () => { this.cursor.classList.remove('cursor-hover'); this.cursorDot.classList.remove('cursor-hover'); });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ThemeManager();
    new ScrollProgress();
    new ScrollReveal();
    new CustomCursor();
});
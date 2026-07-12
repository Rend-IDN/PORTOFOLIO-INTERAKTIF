window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');
});

const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
if (menuToggle) {
    menuToggle.addEventListener('click', () => navLinks.classList.toggle('active'));
}
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => { if (window.innerWidth <= 768) navLinks.classList.remove('active'); });
});

const typingElement = document.getElementById('typing');
if (typingElement) {
    const texts = ['Web Developer', 'UI/UX Designer', 'Creative Coder', 'Problem Solver'];
    let textIndex = 0, charIndex = 0, isDeleting = false;
    function typeEffect() {
        const currentText = texts[textIndex];
        if (isDeleting) typingElement.textContent = currentText.substring(0, charIndex - 1), charIndex--;
        else typingElement.textContent = currentText.substring(0, charIndex + 1), charIndex++;
        if (!isDeleting && charIndex === currentText.length) isDeleting = true, setTimeout(typeEffect, 2000);
        else if (isDeleting && charIndex === 0) isDeleting = false, textIndex = (textIndex + 1) % texts.length, setTimeout(typeEffect, 500);
        else setTimeout(typeEffect, isDeleting ? 50 : 100);
    }
    typeEffect();
}

const statNumbers = document.querySelectorAll('.stat-number');
if (statNumbers.length > 0) {
    const animateNumbers = () => {
        statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            let current = parseInt(stat.innerText);
            const increment = target / 50;
            if (current < target) { current = Math.ceil(current + increment); stat.innerText = current; setTimeout(animateNumbers, 20); }
            else stat.innerText = target;
        });
    };
    const observer = new IntersectionObserver((entries) => { entries.forEach(entry => { if (entry.isIntersecting) { animateNumbers(); observer.unobserve(entry.target); } }); }, { threshold: 0.5 });
    const statsSection = document.querySelector('.stats');
    if (statsSection) observer.observe(statsSection);
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '') {
            const target = document.querySelector(href);
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        }
    });
});

const fadeElements = document.querySelectorAll('.service-card, .preview-card, .testimonial-card');
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) { entry.target.style.opacity = '1'; entry.target.style.transform = 'translateY(0)'; } });
}, { threshold: 0.1 });
fadeElements.forEach(element => { element.style.opacity = '0'; element.style.transform = 'translateY(30px)'; element.style.transition = 'all 0.6s ease'; fadeObserver.observe(element); });

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => btn.classList.add('ripple'));
    const cards = document.querySelectorAll('.card, .service-card');
    cards.forEach(card => card.classList.add('hover-lift'));
    const hero = document.querySelector('.hero');
    if(hero) hero.classList.add('shimmer');
});
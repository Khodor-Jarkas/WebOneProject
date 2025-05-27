const components = [
    { id: 'header-container', file: '../components/header.html' },
    { id: 'hero-container', file: '../components/hero.html' },
    { id: 'about-container', file: '../components/about.html' },
    { id: 'projects-container', file: '../components/projects.html' },
    { id: 'skills-container', file: '../components/skills.html' },
    { id: 'contact-container', file: '../components/contact.html' },
    { id: 'footer-container', file: '../components/footer.html' }
];

components.forEach(component => {
    fetch(`components/${component.file}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById(component.id).innerHTML = html;
        })
        .catch(error => {
            console.error(`Error loading ${component.file}:`, error);
        });
});

// Animate radial progress circles when they come into view
function animateSkills() {
    const radialProgresses = document.querySelectorAll('.radial-progress');
    
    radialProgresses.forEach(progress => {
        const value = progress.getAttribute('data-value');
        const fill = progress.querySelector('.progress-fill');
        const percentage = progress.querySelector('.percentage');
        
        if (fill && percentage) {
            // Calculate the offset (251 is the circumference of a circle with radius 40)
            const circumference = 251;
            const offset = circumference - (value / 100) * circumference;
            
            // Set the CSS properties
            fill.style.strokeDashoffset = offset;
            percentage.textContent = `${value}%`;
        }
    });
}

// Run when DOM is loaded and when scrolled to skills section
document.addEventListener('DOMContentLoaded', animateSkills);

// Optional: Animate when scrolling to the section
window.addEventListener('scroll', function() {
    const skillsSection = document.getElementById('skills');
    if (isElementInViewport(skillsSection)) {
        animateSkills();
    }
});

// Helper function to check if element is in viewport
function isElementInViewport(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.bottom >= 0
    );
}
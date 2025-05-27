document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu');
    const nav = document.querySelector('.nav');
    
    if (mobileMenuBtn && nav) {
        mobileMenuBtn.addEventListener('click', function() {
            nav.classList.toggle('active');
            this.querySelector('i').classList.toggle('fa-times');
        });
        
        // Close mobile menu when clicking on a nav link
        const navLinks = document.querySelectorAll('.nav ul li a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (nav.classList.contains('active')) {
                    nav.classList.remove('active');
                    mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                }
            });
        });
    }

    // Sticky header on scroll
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 10px 30px -10px rgba(2, 12, 27, 0.7)';
            } else {
                header.style.boxShadow = 'none';
            }
        });
    }
});
/* public/js/dashboard.js */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Dark Mode Toggle
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    
    // Check local storage or system preference
    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark' && themeIcon) {
            themeIcon.classList.replace('ph-moon', 'ph-sun');
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', (e) => {
            const current = document.documentElement.getAttribute('data-theme');
            let targetTheme = 'light';

            if (current === 'light' || !current) {
                targetTheme = 'dark';
                themeIcon.classList.replace('ph-moon', 'ph-sun');
            } else {
                targetTheme = 'light';
                themeIcon.classList.replace('ph-sun', 'ph-moon');
            }

            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            
            // Create ripple effect for theme toggle
            createRipple(e, themeToggleBtn);
        });
    }

    // 2. Ripple Effect for buttons
    const buttons = document.querySelectorAll('.btn-modern');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            createRipple(e, this);
        });
    });

    function createRipple(event, button) {
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        const rect = button.getBoundingClientRect();
        
        // Check if event is from keyboard (no clientX/Y)
        const x = event.clientX ? event.clientX - rect.left - radius : button.clientWidth / 2 - radius;
        const y = event.clientY ? event.clientY - rect.top - radius : button.clientHeight / 2 - radius;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${x}px`;
        circle.style.top = `${y}px`;
        circle.classList.add('ripple');

        const existingRipple = button.querySelector('.ripple');
        if (existingRipple) {
            existingRipple.remove();
        }

        button.appendChild(circle);
    }

    // 3. Count Up Animation for Balance and Stats
    const countUpElements = document.querySelectorAll('.js-count-up');
    
    const countUpObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const targetValue = parseFloat(el.getAttribute('data-value')) || 0;
                const duration = 1500; // ms
                const isCurrency = el.hasAttribute('data-currency');
                
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    
                    // easeOutQuart
                    const easeProgress = 1 - Math.pow(1 - progress, 4);
                    const currentVal = easeProgress * targetValue;
                    
                    if (isCurrency) {
                        el.textContent = 'Rp ' + Math.floor(currentVal).toLocaleString('id-ID');
                    } else {
                        el.textContent = Math.floor(currentVal);
                    }
                    
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        if (isCurrency) {
                            el.textContent = 'Rp ' + targetValue.toLocaleString('id-ID');
                        } else {
                            el.textContent = targetValue;
                        }
                    }
                };
                window.requestAnimationFrame(step);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.1 });

    countUpElements.forEach(el => countUpObserver.observe(el));

    // 4. Toggle Forms (Nabung)
    const toggleNabungBtn = document.getElementById('btn-toggle-nabung');
    const nabungSection = document.getElementById('section-nabung');
    
    if (toggleNabungBtn && nabungSection) {
        toggleNabungBtn.addEventListener('click', (e) => {
            e.preventDefault();
            nabungSection.classList.toggle('show');
            
            // Scroll smoothly to section if it's opened
            if (nabungSection.classList.contains('show')) {
                setTimeout(() => {
                    nabungSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
    }

    // 5. Initialize Progress Bars with smooth animation
    setTimeout(() => {
        document.querySelectorAll('.js-progress-bar-modern').forEach(function (el) {
            const value = parseFloat(el.getAttribute('data-progress') || '0');
            const clamped = Math.max(0, Math.min(100, isNaN(value) ? 0 : value));
            el.style.width = clamped + '%';
        });
    }, 100);

    // 6. Calendar Integration (if exists in DOM)
    const calendarEl = document.getElementById('savingCalendar');
    if (calendarEl && typeof FullCalendar !== 'undefined') {
        const badge = document.getElementById('pickedDateBadge');
        const dateInput = document.getElementById('input-tanggal');
        
        // Fetch events data from script tag
        const eventsDataEl = document.getElementById('calendarEventsData');
        const events = eventsDataEl ? JSON.parse(eventsDataEl.textContent || '[]') : [];

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            locale: 'id',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'today'
            },
            navLinks: true,
            selectable: true,
            events: events,

            dateClick: function(info) {
                const picked = info.dateStr;
                if (badge) badge.textContent = picked;
                
                if (dateInput) {
                    dateInput.value = picked;
                    // Auto open form if it's hidden
                    if (nabungSection && !nabungSection.classList.contains('show')) {
                        nabungSection.classList.add('show');
                        setTimeout(() => {
                            dateInput.focus();
                        }, 400);
                    }
                }
            },
        });

        calendar.render();
        
        const today = new Date().toISOString().slice(0, 10);
        if (badge && !badge.textContent) badge.textContent = today;
    }

    // 7. Auto-Format Currency Input
    const currencyInputs = document.querySelectorAll('.js-currency-format');
    
    currencyInputs.forEach(input => {
        // Format on input
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, ''); // Remove non-numeric
            if (value !== '') {
                this.value = parseInt(value, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });

        // Format on initial load if there's a value
        if (input.value) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value !== '') {
                input.value = parseInt(value, 10).toLocaleString('id-ID');
            }
        }
    });

    // Strip dots before form submission for any form containing currency inputs
    const formsWithCurrency = document.querySelectorAll('form');
    formsWithCurrency.forEach(form => {
        const formCurrencyInputs = form.querySelectorAll('.js-currency-format');
        if (formCurrencyInputs.length > 0) {
            form.addEventListener('submit', function() {
                formCurrencyInputs.forEach(input => {
                    input.value = input.value.replace(/[^0-9]/g, '');
                });
            });
        }
    });

    // 8. Mobile Sidebar Toggle Logic
    const sidebarBtn = document.getElementById('sidebar-toggle-btn');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const leftSidebar = document.getElementById('leftSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarBtn && leftSidebar && sidebarOverlay) {
        // Open Sidebar
        sidebarBtn.addEventListener('click', () => {
            leftSidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });

        // Close Sidebar function
        const closeSidebar = () => {
            leftSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        };

        // Close on X button
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', closeSidebar);
        }

        // Close on Overlay click
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
});

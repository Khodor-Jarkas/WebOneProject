/**
 * Render projects to the DOM
 * @param {Array} projects - Array of project objects
 * @param {HTMLElement} container - DOM element to render into
 */
export function renderProjects(projects, container) {
    if (!projects || projects.length === 0) {
        container.innerHTML = '<div class="loading">No projects found. Add your first project!</div>';
        return;
    }

    let html = '';
    
    projects.forEach(project => {
        html += `
            <div class="project-card">
                <div class="project-image">
                    <img src="${project.imageUrl}" alt="${project.title}" loading="lazy">
                </div>
                <div class="project-info">
                    <h3>${project.title}</h3>
                    <p>${project.description}</p>
                    <div class="project-tech">
                        ${(project.technologies || []).map(tech => `<span>${tech}</span>`).join('')}
                    </div>
                    <a href="${project.projectUrl || '#'}" class="project-link" target="_blank" rel="noopener noreferrer">
                        View Project <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Show loading state in projects container
 * @param {HTMLElement} container - DOM element to show loading in
 */
export function showLoading(container) {
    container.innerHTML = '<div class="loading">Loading projects...</div>';
}

/**
 * Show error state in projects container
 * @param {HTMLElement} container - DOM element to show error in
 * @param {string} message - Error message to display
 */
export function showError(container, message) {
    container.innerHTML = `<div class="loading error">${message}</div>`;
}

/**
 * Show success message
 * @param {string} message - Success message
 */
export function showSuccessMessage(message) {
    const successMsg = document.getElementById('success-message');
    if (successMsg) {
        successMsg.textContent = message;
        successMsg.style.display = 'block';
        setTimeout(() => successMsg.style.display = 'none', 3000);
    }
}

/**
 * Handle modal operations
 */
export const modal = {
    open: () => {
        const modal = document.getElementById('project-modal');
        if (modal) modal.style.display = 'block';
    },
    close: () => {
        const modal = document.getElementById('project-modal');
        const form = document.getElementById('project-form');
        if (modal) modal.style.display = 'none';
        if (form) form.reset();
    },
    handleOutsideClick: (e) => {
        const modal = document.getElementById('project-modal');
        if (e.target === modal) {
            modal.close();
        }
    }
};

/**
 * Get form data as an object
 * @returns {Object} Form data
 */
export function getFormData() {
    return {
        title: document.getElementById('project-title').value,
        description: document.getElementById('project-description').value,
        technologies: document.getElementById('project-technologies').value.split(',').map(tech => tech.trim()),
        imageUrl: document.getElementById('project-image').value,
        projectUrl: document.getElementById('project-link').value || ''
    };
}
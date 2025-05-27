import { loadProjects, saveProject } from './api.js';
import { renderProjects, showLoading, showError, showSuccessMessage, modal, getFormData } from './ui.js';

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const addProjectBtn = document.getElementById('add-project-btn');
    const closeModalBtn = document.querySelector('.close-modal');
    const projectForm = document.getElementById('project-form');
    const projectsContainer = document.getElementById('projects-container');
    
    // Event Listeners
    addProjectBtn?.addEventListener('click', modal.open);
    closeModalBtn?.addEventListener('click', modal.close);
    window.addEventListener('click', modal.handleOutsideClick);
    projectForm?.addEventListener('submit', handleFormSubmit);
    
    // Initial load
    initializeProjects();
    
    /**
     * Initialize projects on page load
     */
    async function initializeProjects() {
        showLoading(projectsContainer);
        
        try {
            const projects = await loadProjects();
            renderProjects(projects, projectsContainer);
        } catch (error) {
            showError(projectsContainer, 'Error loading projects. Please try again later.');
        }
    }
    
    /**
     * Handle form submission
     * @param {Event} e - Form submit event
     */
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        try {
            const projectData = getFormData();
            await saveProject(projectData);
            
            showSuccessMessage('Project saved successfully!');
            modal.close();
            initializeProjects(); // Refresh projects list
        } catch (error) {
            showError(projectsContainer, error.message);
        }
    }
});
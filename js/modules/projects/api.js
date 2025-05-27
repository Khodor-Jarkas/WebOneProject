// API URL - Update this to your actual path
const API_URL = 'api/projects.php';

/**
 * Load all projects from the API
 * @returns {Promise<Array>} Array of projects
 */
export async function loadProjects() {
    try {
        const response = await fetch(API_URL);
        
        if (!response.ok) {
            throw new Error('Failed to load projects');
        }
        
        const projects = await response.json();
        return projects || [];
    } catch (error) {
        console.error('Error loading projects:', error);
        throw error;
    }
}

/**
 * Save a project to the API
 * @param {Object} projectData - Project data to save
 * @returns {Promise<Object>} Saved project data
 */
export async function saveProject(projectData) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ...projectData,
                technologies: projectData.technologies.join(',')
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to save project');
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error saving project:', error);
        throw error;
    }
}
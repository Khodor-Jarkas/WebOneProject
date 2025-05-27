<?php
// Database configuration
require_once __DIR__ . '/../../config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Get form data
        $title = $conn->real_escape_string($_POST['title'] ?? '');
        $description = $conn->real_escape_string($_POST['description'] ?? '');
        $technologies = $conn->real_escape_string($_POST['technologies'] ?? '');
        $imageUrl = $conn->real_escape_string($_POST['image_url'] ?? '');
        $projectUrl = $conn->real_escape_string($_POST['project_url'] ?? '');
        $id = $_POST['id'] ?? null;

        // Validate required fields
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';
        if (empty($technologies)) $errors['technologies'] = 'Technologies are required';
        if (empty($imageUrl)) $errors['image_url'] = 'Image URL is required';

        if (!empty($errors)) {
            throw new Exception(json_encode(['errors' => $errors]));
        }

        // Prepare SQL based on whether we're updating or inserting
        if ($id) {
            $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, technologies=?, image_url=?, project_url=? WHERE id=?");
            $stmt->bind_param("sssssi", $title, $description, $technologies, $imageUrl, $projectUrl, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO projects (title, description, technologies, image_url, project_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $description, $technologies, $imageUrl, $projectUrl);
        }

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        echo json_encode(['success' => true, 'message' => 'Project saved successfully']);
    } catch (Exception $e) {
        http_response_code(400);
        echo $e->getMessage();
    } finally {
        if (isset($conn)) $conn->close();
        exit;
    }
}

// Handle GET request to load projects
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'load') {
    header('Content-Type: application/json');
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
        $projects = [];

        while ($row = $result->fetch_assoc()) {
            $projects[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'technologies' => explode(',', $row['technologies']),
                'imageUrl' => $row['image_url'],
                'projectUrl' => $row['project_url'] ?: '#',
                'createdAt' => $row['created_at']
            ];
        }

        echo json_encode($projects);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    } finally {
        if (isset($conn)) $conn->close();
        exit;
    }
}
?>

<section id="projects" class="projects">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Projects</h2>
            <button id="add-project-btn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>
        
        <!-- Success message (hidden by default) -->
        <div id="success-message" class="success-message"></div>
        
        <!-- Project Form Modal -->
        <div id="project-modal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h3 id="modal-title">Add New Project</h3>
                <form id="project-form">
                    <input type="hidden" id="project-id">
                    <div class="form-group">
                        <label for="project-title">Project Title*</label>
                        <input type="text" id="project-title" required>
                        <div class="error-message" id="title-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="project-description">Description*</label>
                        <textarea id="project-description" required></textarea>
                        <div class="error-message" id="description-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="project-technologies">Technologies* (comma separated)</label>
                        <input type="text" id="project-technologies" required>
                        <div class="error-message" id="technologies-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="project-image">Image URL*</label>
                        <input type="url" id="project-image" required>
                        <div class="error-message" id="image-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="project-link">Project URL</label>
                        <input type="url" id="project-link">
                    </div>
                    <div class="form-actions">
                        <button type="button" id="delete-project" class="btn btn-danger" style="display: none;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <button type="submit" class="btn btn-primary">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Projects Container -->
        <div class="projects-grid" id="projects-container">
            <div class="loading">Loading projects...</div>
        </div>
    </div>
</section>

<script>
// JavaScript to handle form submission and loading projects
document.addEventListener('DOMContentLoaded', function() {
    const addProjectBtn = document.getElementById('add-project-btn');
    const projectModal = document.getElementById('project-modal');
    const projectForm = document.getElementById('project-form');
    const projectsContainer = document.getElementById('projects-container');
    
    // Load projects on page load
    loadProjects();
    
    // Form submission handler
    projectForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            id: document.getElementById('project-id').value || null,
            title: document.getElementById('project-title').value,
            description: document.getElementById('project-description').value,
            technologies: document.getElementById('project-technologies').value,
            image_url: document.getElementById('project-image').value,
            project_url: document.getElementById('project-link').value || ''
        };
        
        try {
            const response = await fetch('projects.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.errors) {
                // Handle validation errors
                Object.entries(data.errors).forEach(([field, message]) => {
                    const errorElement = document.getElementById(`${field}-error`);
                    if (errorElement) {
                        errorElement.textContent = message;
                        errorElement.style.display = 'block';
                    }
                });
                return;
            }
            
            if (data.success) {
                showSuccessMessage(data.message);
                closeModal();
                loadProjects();
            }
        } catch (error) {
            console.error('Error:', error);
            showSuccessMessage('An error occurred', false);
        }
    });
    
    // Function to load projects
    async function loadProjects() {
        projectsContainer.innerHTML = '<div class="loading">Loading projects...</div>';
        
        try {
            const response = await fetch('projects.php?action=load');
            const projects = await response.json();
            
            if (projects.length === 0) {
                projectsContainer.innerHTML = '<div class="loading">No projects found. Add your first project!</div>';
                return;
            }
            
            renderProjects(projects);
        } catch (error) {
            console.error('Error loading projects:', error);
            projectsContainer.innerHTML = '<div class="error">Error loading projects. Please try again later.</div>';
        }
    }
    
    // Function to render projects
    function renderProjects(projects) {
        let html = '';
        
        projects.forEach(project => {
            html += `
                <div class="project-card" data-id="${project.id}">
                    <div class="project-image">
                        <img src="${project.imageUrl}" alt="${project.title}" loading="lazy">
                    </div>
                    <div class="project-info">
                        <h3>${project.title}</h3>
                        <p>${project.description}</p>
                        <div class="project-tech">
                            ${project.technologies.map(tech => `<span>${tech}</span>`).join('')}
                        </div>
                        <a href="${project.projectUrl}" class="project-link" target="_blank" rel="noopener noreferrer">
                            View Project <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            `;
        });
        
        projectsContainer.innerHTML = html;
    }
    
    // Helper functions
    function showSuccessMessage(message, isSuccess = true) {
        const successMsg = document.getElementById('success-message');
        successMsg.textContent = message;
        successMsg.style.display = 'block';
        successMsg.style.backgroundColor = isSuccess ? 'rgba(100, 255, 218, 0.2)' : 'rgba(255, 107, 107, 0.2)';
        setTimeout(() => successMsg.style.display = 'none', 3000);
    }
    
    function closeModal() {
        projectModal.style.display = 'none';
        projectForm.reset();
        document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    }
});
</script>
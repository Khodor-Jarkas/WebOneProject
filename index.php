<?php
// Prevent directory listing
if (basename($_SERVER['PHP_SELF']) == 'index.php') {
    define('IN_PORTFOLIO', true);
} else {
    exit('Access denied');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfoio</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'assets/components/header.html'; ?>
    <?php include 'assets/components/hero.html'; ?>
    <?php include 'assets/components/about.html'; ?>
    <?php include 'assets/components/projects.php'; ?>
    <?php include 'assets/components/skills.html'; ?>
    <?php include 'assets/components/contact.html'; ?>
    <?php include 'assets/components/footer.html'; ?>

    <!-- JavaScript -->
    <script src="https://www.w3schools.com/lib/w3.js"></script>
    <script>
        w3.includeHTML();
    </script>
    
    <!-- Modern Modules -->
    <script type="module" src="assets/js/modules/projects/main.js"></script>
    
    <!-- Legacy Support // just incase -->
    <script src="assets/js/main.js"></script>
</body>
</html>
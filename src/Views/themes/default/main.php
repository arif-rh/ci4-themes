<!DOCTYPE html>
<html lang="en">
<head>
    <title>Page Title</title>

    <?php 
    /**
     * This will render any CSS added into theme on the fly,
     * You can directly load common css before call renderCSS();
    */
    Arifrh\Themes\Themes::renderCSS();
    ?>
</head>
<body>
    <main role="main" class="container">
        <?= $this->renderSection('main') ?>
    </main>
    <?php 
    /**
     * This will render any JS added into theme on the fly,
     * You can directly load common js before call renderJS();
    */
    Arifrh\Themes\Themes::renderJS();
    ?>
</body>
</html>
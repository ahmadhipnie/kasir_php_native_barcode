<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?> - <?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS - Tempat untuk Bootstrap template Anda -->
    <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS - Tempat untuk Bootstrap template Anda -->
    <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body>
</html>

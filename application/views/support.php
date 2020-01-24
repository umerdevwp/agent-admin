<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Admin</title>
    <link rel="stylesheet" href="components/base/base.css">
    <link rel="stylesheet" href="components/base/custom.css">
</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 100vh">
        <?php if (empty($this->session->flashdata("error"))) : ?>
            <h3>Please contact administrator, service unable to validate your account properly.</h3>
        <?php endif; ?>
        <?php if (!empty($this->session->flashdata("error"))) : ?>
            <h3><?php print $this->session->flashdata("error"); ?></h3>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>
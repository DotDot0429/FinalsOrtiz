<?php
require 'header.php'; // Include the reusable header
?>

<div class="container mt-5">
    <div class="row text-center">
        <h1>Welcome to FindHire!</h1>
        <p class="lead">Connecting talent with opportunity, one hire at a time.</p>
    </div>
    <div class="row mt-4">
        <?php if (!$isLoggedIn): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">New User?</h5>
                        <p class="card-text">Create an account to get started!</p>
                        <a href="register.php" class="btn btn-primary">Register</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Already a Member?</h5>
                        <p class="card-text">Login to access your account.</p>
                        <a href="login.php" class="btn btn-success">Login</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"></h5>
                        <p class="card-text"></p>
                        <a href="<?php echo $userRole == 'hr' ? 'hr-dashboard.php' : 'applicant-dashboard.php'; ?>" class="btn btn-primary">
                            Go to Dashboard
                        </a>
                        <?php if ($userRole == 'hr'): ?>
                            <p class="mt-3"> <a href="create-job.php" class="btn btn-info">Create a Job Post</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
?>

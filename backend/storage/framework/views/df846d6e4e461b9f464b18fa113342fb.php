<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 - Page Not Found</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f9fafb;
      color: #1f2937;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .error-container {
      text-align: center;
      max-width: 600px;
      padding: 2rem;
    }

    .error-code {
      font-size: 7rem;
      font-weight: 700;
      color: #2563eb;
      letter-spacing: -3px;
    }

    .error-message {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .error-description {
      color: #6b7280;
      margin-bottom: 2rem;
    }

    .btn-primary {
      background-color: #2563eb;
      border-color: #2563eb;
    }

    .btn-outline-dark {
      border-radius: 30px;
    }

    .illustration {
      max-width: 100%;
      margin: 2rem 0;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <div class="error-code">404</div>
    <div class="error-message">Page Not Found</div>
    <p class="error-description">
      Oops! The page you are looking for doesn’t exist or has been moved.<br>
      Let’s get you back on track.
    </p>
    <div>
      <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary btn-lg px-4">Go Home</a>
      <a href="javascript:history.back()" class="btn btn-outline-dark btn-lg ms-3 px-4">Go Back</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/errors/404.blade.php ENDPATH**/ ?>
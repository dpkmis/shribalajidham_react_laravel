<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 - Forbidden</title>
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
      color: #ef4444; /* Red tone for forbidden */
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

    .illustration {
      max-width: 100%;
      margin: 2rem 0;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <div class="error-code">403</div>
    <div class="error-message">Access Forbidden</div>
    <p class="error-description">
      You don’t have permission to access this page.<br>
      If you believe this is a mistake, please contact the administrator.
    </p>
    <img src="https://illustrations.popsy.co/red/lock.svg" alt="403 Illustration" class="illustration">
    <div>
      <a href="/" class="btn btn-primary btn-lg px-4">Go Home</a>
      <a href="mailto:support@example.com" class="btn btn-outline-dark btn-lg ms-3 px-4">Contact Support</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

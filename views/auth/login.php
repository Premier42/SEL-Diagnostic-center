<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #764ba2;
            --secondary-color: #f093fb;
            --secondary-dark: #f5576c;
            --success-color: #43e97b;
            --danger-color: #f5576c;
            --warning-color: #feca57;
            --info-color: #4facfe;
            --dark-color: #2d3748;
            --light-color: #f7fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
            display: flex;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.05);
            opacity: 0.5;
        }

        .login-right {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .feature-list {
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .feature-item i {
            margin-right: 0.75rem;
            width: 20px;
            opacity: 0.9;
        }

        .login-form {
            max-width: 400px;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating > .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
            color: #6b7280;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: rgba(245, 87, 108, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 10px;
                border-radius: 15px;
            }

            .login-left {
                padding: 40px 30px;
                text-align: center;
            }

            .login-right {
                padding: 40px 30px;
            }

            .brand-logo {
                font-size: 2rem;
            }
        }

        .loading {
            display: none;
        }

        .btn-login.loading .loading {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>

            <div class="brand-logo">
                <i class="fas fa-flask me-3"></i>
                SEL Diagnostic
            </div>
            <p class="brand-subtitle">
                Modern Pathology Laboratory Management System
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-user-md"></i>
                    <span>Complete Patient Management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-vial"></i>
                    <span>Advanced Test Management</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time Analytics</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Data Protection</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobile Responsive Design</span>
                </div>
            </div>
        </div>

        <div class="login-right">
            <form class="login-form" method="POST" action="/login" id="loginForm">
                <h2 class="form-title">Welcome Back</h2>
                <p class="form-subtitle">Please sign in to your account</p>

                <?php if (!empty(errors('general'))): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars(errors('general')[0]) ?>
                    </div>
                <?php endif; ?>

                <?= csrf_field() ?>

                <div class="form-floating">
                    <input type="text"
                           class="form-control <?= !empty(errors('username')) ? 'is-invalid' : '' ?>"
                           id="username"
                           name="username"
                           placeholder="Username"
                           value="<?= htmlspecialchars(old('username')) ?>"
                           required>
                    <label for="username">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <?php if (!empty(errors('username'))): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(errors('username')[0]) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-floating">
                    <input type="password"
                           class="form-control <?= !empty(errors('password')) ? 'is-invalid' : '' ?>"
                           id="password"
                           name="password"
                           placeholder="Password"
                           required>
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <?php if (!empty(errors('password'))): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(errors('password')[0]) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-login" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </span>
                    <span class="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Signing In...
                    </span>
                </button>

                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Default credentials: admin / password
                    </small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Auto-focus on username field
        document.getElementById('username').focus();

        // Clear old input after page load
        <?php clearOldInput(); ?>
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>PTIT || Login Page</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #C5A059;
      --primary-dark: #A48342;
      --primary-light: #D4AF37;
      --secondary-color: #111111;
      --accent-color: #f5f5f5;
      --text-dark: #111111;
      --text-light: #555555;
      --white: #ffffff;
      --shadow-light: 0 2px 4px rgba(0, 0, 0, 0.1);
      --shadow-medium: 0 4px 8px rgba(0, 0, 0, 0.1);
      --shadow-heavy: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f8f8;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow-x: hidden;
    }

    .login-container {
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      padding: 20px;
      position: relative;
      z-index: 10;
    }

    .login-card {
      background: var(--white);
      border-radius: 8px;
      box-shadow: var(--shadow-medium);
      overflow: hidden;
      border: 1px solid #e5e5e5;
      transition: all 0.3s ease;
    }

    .login-card:hover {
      box-shadow: var(--shadow-heavy);
    }

    .login-image-side {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 500px;
    }

    .login-image-side::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
      opacity: 0.5;
    }

    .login-image-content {
      text-align: center;
      color: var(--white);
      z-index: 2;
      position: relative;
      padding: 20px;
    }

    .logo-image {
      width: 160px;
      height: auto;
      margin: 0 auto 20px;
      display: block;
      filter: brightness(0) invert(1);
      opacity: 0.95;
    }

    .login-form-side {
      padding: 40px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 500px;
    }

    .welcome-text {
      text-align: center;
      margin-bottom: 35px;
    }

    .welcome-text h1 {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .welcome-text p {
      color: var(--text-light);
      font-size: 0.95rem;
      font-weight: 400;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-control {
      height: 48px;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 12px 20px 12px 50px;
      font-size: 15px;
      font-weight: 400;
      background: var(--white);
      transition: all 0.3s ease;
      color: var(--text-dark);
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 2px rgba(197, 160, 89, 0.1);
      background: var(--white);
      outline: none;
    }

    .form-control.is-invalid {
      border-color: #e53e3e;
      box-shadow: 0 0 0 2px rgba(229, 62, 62, 0.1);
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 16px;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .form-control:focus + .input-icon {
      color: var(--primary-color);
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .remember-checkbox {
      display: flex;
      align-items: center;
    }

    .remember-checkbox input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-right: 10px;
      accent-color: var(--primary-color);
    }

    .remember-checkbox label {
      color: var(--text-light);
      font-weight: 500;
      cursor: pointer;
      font-size: 14px;
    }

    .forgot-link {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .forgot-link:hover {
      color: var(--secondary-color);
    }

    .btn-login {
      background: var(--primary-color);
      border: none;
      height: 48px;
      border-radius: 4px;
      font-size: 15px;
      font-weight: 600;
      color: var(--white);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .btn-login:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(197, 160, 89, 0.3);
    }

    .invalid-feedback {
      display: block;
      margin-top: 6px;
      font-size: 13px;
      color: #e53e3e;
      font-weight: 500;
    }

    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 20px 0;
      color: var(--text-light);
      font-size: 13px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #ddd;
    }

    .divider span {
      padding: 0 12px;
    }

    .social-login {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .btn-social {
      flex: 1;
      height: 48px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: var(--white);
      color: var(--text-dark);
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s;
      text-decoration: none;
      font-size: 14px;
    }

    .btn-social:hover {
      border-color: var(--primary-color);
      background: #fafafa;
      transform: translateY(-1px);
    }

    .btn-social.google {
      border-color: #4285f4;
      color: #4285f4;
    }

    .btn-social.google:hover {
      background: #4285f4;
      color: var(--white);
    }

    .btn-social.facebook {
      border-color: #1877f2;
      color: #1877f2;
    }

    .btn-social.facebook:hover {
      background: #1877f2;
      color: var(--white);
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #eee;
    }

    .register-link p {
      color: var(--text-light);
      margin: 0;
      font-size: 14px;
    }

    .register-link a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }

    .register-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .login-image-side {
        display: none;
      }
      
      .login-form-side {
        padding: 30px 25px;
      }

      .welcome-text h1 {
        font-size: 1.6rem;
      }

      .login-container {
        padding: 10px;
      }

      .social-login {
        flex-direction: column;
      }

      .remember-forgot {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="row g-0">
        <div class="col-lg-5">
          <div class="login-image-side">
            <div class="login-image-content">
              <img src="{{ asset('images/logoden.png') }}" alt="PTIT Ecommerce Logo" class="logo-image">
              <h2 style="font-weight: 700; margin-bottom: 12px; font-size: 1.7rem;">PTIT Ecommerce</h2>
              <p style="font-size: 0.95rem; opacity: 0.95; line-height: 1.5;">Quản lý thương mại điện tử dễ dàng với bảng điều khiển thân thiện.</p>
              <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 25px; font-weight: 500;">Cổng đăng nhập bảo mật</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-7">
          <div class="login-form-side">
            <div class="welcome-text">
              <h1>Chào mừng trở lại!</h1>
              <p>Vui lòng đăng nhập vào tài khoản của bạn</p>
            </div>

            <form method="POST" action="{{ route('auth.login') }}" id="loginForm">
              @csrf
              
              <div class="form-group">
                <input type="text" 
                       class="form-control @error('login') is-invalid @enderror" 
                       name="login" 
                       id="login-input"
                       value="{{ old('login') }}" 
                       placeholder="Email hoặc số điện thoại"
                       autocomplete="username" 
                       required
                       autofocus>
                <i class="fa fa-user input-icon"></i>
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       id="password-input"
                       placeholder="Mật khẩu"
                       required 
                       autocomplete="current-password">
                <i class="fa fa-lock input-icon"></i>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="remember-forgot">
                <div class="remember-checkbox">
                  <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">
                  Quên mật khẩu?
                </a>
              </div>

              <button type="submit" class="btn btn-login w-100">
                <i class="fa fa-sign-in-alt me-2"></i>
                Đăng nhập
              </button>
            </form>

            <div class="divider">
              <span>Hoặc đăng nhập bằng</span>
            </div>

            <div class="social-login">
              <a href="#" class="btn-social google">
                <i class="fab fa-google"></i>
                <span>Google</span>
              </a>
              <a href="#" class="btn-social facebook">
                <i class="fab fa-facebook-f"></i>
                <span>Facebook</span>
              </a>
            </div>

            <div class="register-link">
              <p>Chưa có tài khoản? <a href="{{ route('auth.register') }}">Đăng ký ngay</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
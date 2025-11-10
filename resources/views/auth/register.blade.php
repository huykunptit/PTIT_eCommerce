<!DOCTYPE html>
<html lang="vi">
<head>
  <title>PTIT || Register Page</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #667eea;
      --secondary-color: #764ba2;
      --accent-color: #f093fb;
      --text-dark: #2d3748;
      --text-light: #718096;
      --white: #ffffff;
      --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      --shadow-heavy: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="c" cx="50%" cy="0%" r="100%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><rect width="100%" height="100%" fill="url(%23c)"/></svg>');
      opacity: 0.3;
      animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(2deg); }
    }

    .register-container {
      width: 100%;
      max-width: 950px;
      margin: 0 auto;
      padding: 20px;
      position: relative;
      z-index: 10;
    }

    .register-card {
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-heavy);
      overflow: hidden;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transform: translateY(0);
      transition: all 0.3s ease;
    }

    .register-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.15);
    }

    .register-image-side {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 700px;
    }

    .register-image-side::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .register-image-content {
      text-align: center;
      color: var(--white);
      z-index: 2;
      position: relative;
    }

    .logo-icon {
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 30px;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .logo-icon i {
      font-size: 60px;
      color: var(--white);
    }

    .register-form-side {
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 700px;
    }

    .welcome-text {
      text-align: center;
      margin-bottom: 40px;
    }

    .welcome-text h1 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 10px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .welcome-text p {
      color: var(--text-light);
      font-size: 1.1rem;
      font-weight: 400;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-control {
      height: 55px;
      border: 2px solid #e2e8f0;
      border-radius: 15px;
      padding: 15px 20px 15px 60px;
      font-size: 16px;
      font-weight: 500;
      background: #f8fafc;
      transition: all 0.3s ease;
      color: var(--text-dark);
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: var(--white);
      outline: none;
    }

    .form-control.is-invalid {
      border-color: #e53e3e;
      box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 18px;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .form-control:focus + .input-icon {
      color: var(--primary-color);
    }

    .btn-register {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      height: 60px;
      border-radius: 15px;
      font-size: 18px;
      font-weight: 600;
      color: var(--white);
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .btn-register::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-register:hover::before {
      left: 100%;
    }

    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .login-link {
      text-align: center;
    }

    .login-link a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
    }

    .login-link a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 50%;
      background: var(--primary-color);
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    .login-link a:hover::after {
      width: 100%;
    }

    .login-link a:hover {
      color: var(--secondary-color);
    }

    .invalid-feedback {
      display: block;
      margin-top: 8px;
      font-size: 14px;
      color: #e53e3e;
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .register-image-side {
        display: none;
      }
      
      .register-form-side {
        padding: 40px 30px;
      }

      .welcome-text h1 {
        font-size: 2rem;
      }

      .register-container {
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="register-container">
    <div class="register-card">
      <div class="row g-0">
        <div class="col-lg-6">
          <div class="register-image-side">
            <div class="register-image-content">
              <div class="logo-icon">
                <i class="fas fa-user-plus"></i>
              </div>
              <h2 style="font-weight: 700; margin-bottom: 15px;">PTIT</h2>
              <p style="font-size: 1.1rem; opacity: 0.9;">Posts and Telecommunications Institute of Technology</p>
              <p style="font-size: 0.95rem; opacity: 0.7; margin-top: 20px;">Create Your Account</p>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="register-form-side">
            <div class="welcome-text">
              <h1>Create Account</h1>
              <p>Sign up to get started</p>
            </div>

            <form method="POST" action="{{ route('auth.register') }}">
              @csrf
              
              <div class="form-group">
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Họ và tên"
                       required 
                       autocomplete="name" 
                       autofocus>
                <i class="fas fa-user input-icon"></i>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div class="form-group">
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="Email"
                       required 
                       autocomplete="email">
                <i class="fas fa-envelope input-icon"></i>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div class="form-group">
                <input type="tel" 
                       class="form-control @error('phone_number') is-invalid @enderror" 
                       name="phone_number" 
                       value="{{ old('phone_number') }}" 
                       placeholder="Số điện thoại (tùy chọn)"
                       autocomplete="tel">
                <i class="fas fa-phone input-icon"></i>
                @error('phone_number')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div class="form-group">
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       placeholder="Mật khẩu"
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div class="form-group">
                <input type="password" 
                       class="form-control" 
                       name="password_confirmation" 
                       placeholder="Xác nhận mật khẩu"
                       required 
                       autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
              </div>

              <button type="submit" class="btn btn-register w-100">
                <i class="fas fa-user-plus me-2"></i>
                Đăng ký
              </button>
            </form>

            <div class="login-link">
              <p>Đã có tài khoản? <a href="{{ route('auth.login') }}">Đăng nhập ngay</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

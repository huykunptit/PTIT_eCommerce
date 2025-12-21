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
      border-radius: 8px;
      box-shadow: var(--shadow-medium);
      overflow: hidden;
      border: 1px solid #e5e5e5;
      transform: translateY(0);
      transition: all 0.3s ease;
    }

    .register-card:hover {
      box-shadow: var(--shadow-heavy);
    }

    .register-image-side {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
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
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
      opacity: 0.5;
    }

    .register-image-content {
      text-align: center;
      color: var(--white);
      z-index: 2;
      position: relative;
    }

    .logo-image {
      width: 200px;
      height: auto;
      margin: 0 auto 30px;
      display: block;
      filter: brightness(0) invert(1);
      opacity: 0.95;
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
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 10px;
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
      height: 50px;
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
      font-size: 18px;
      transition: all 0.3s ease;
      z-index: 10;
    }

    .form-control:focus + .input-icon {
      color: var(--primary-color);
    }

    .btn-register {
      background: var(--primary-color);
      border: none;
      height: 50px;
      border-radius: 4px;
      font-size: 16px;
      font-weight: 600;
      color: var(--white);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
    }


    .btn-register:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(197, 160, 89, 0.3);
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
              <img src="{{ asset('images/logoden.png') }}" alt="PTIT Ecommerce Logo" class="logo-image">
              <h2 style="font-weight: 700; margin-bottom: 15px; font-size: 2rem;">PTIT Ecommerce</h2>
              <p style="font-size: 1rem; opacity: 0.95; line-height: 1.6;">Simplify your e-commerce management with our user-friendly admin dashboard.</p>
              <p style="font-size: 0.9rem; opacity: 0.8; margin-top: 30px; font-weight: 500;">Create Your Account</p>
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
                <i class="fa fa-user input-icon"></i>
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
                <i class="fa fa-envelope input-icon"></i>
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
                <i class="fa fa-phone input-icon"></i>
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
                <i class="fa fa-lock input-icon"></i>
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
                <i class="fa fa-lock input-icon"></i>
              </div>

              <button type="submit" class="btn btn-register w-100">
                <i class="fa fa-user-plus me-2"></i>
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

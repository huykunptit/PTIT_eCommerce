<!DOCTYPE html>
<html lang="vi">
<head>
  <title>PTIT || Forgot Password</title>
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
    }

    .forgot-container {
      width: 100%;
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
      position: relative;
      z-index: 10;
    }

    .forgot-card {
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-heavy);
      padding: 50px 40px;
      text-align: center;
    }

    .forgot-icon {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 30px;
      color: var(--white);
      font-size: 50px;
    }

    .forgot-card h1 {
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 15px;
    }

    .forgot-card p {
      color: var(--text-light);
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-control {
      height: 60px;
      border: 2px solid #e2e8f0;
      border-radius: 15px;
      padding: 15px 20px 15px 60px;
      font-size: 16px;
      background: #f8fafc;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: var(--white);
      outline: none;
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 18px;
      z-index: 10;
    }

    .btn-submit {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      height: 60px;
      border-radius: 15px;
      font-size: 18px;
      font-weight: 600;
      color: var(--white);
      width: 100%;
      transition: all 0.3s ease;
      margin-bottom: 20px;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .back-link {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }

    .back-link:hover {
      color: var(--secondary-color);
    }

    .alert {
      border-radius: 10px;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="forgot-container">
    <div class="forgot-card">
      <div class="forgot-icon">
        <i class="fas fa-key"></i>
      </div>
      
      <h1>Quên mật khẩu?</h1>
      <p>Nhập email của bạn và chúng tôi sẽ gửi link reset mật khẩu</p>

      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            {{ $error }}
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="form-group">
          <input type="email" 
                 class="form-control @error('email') is-invalid @enderror" 
                 name="email" 
                 value="{{ old('email') }}" 
                 placeholder="Nhập email của bạn"
                 required 
                 autofocus>
          <i class="fas fa-envelope input-icon"></i>
        </div>

        <button type="submit" class="btn btn-submit">
          <i class="fas fa-paper-plane me-2"></i>
          Gửi link reset
        </button>
      </form>

      <a href="{{ route('auth.login') }}" class="back-link">
        <i class="fas fa-arrow-left me-1"></i>
        Quay lại đăng nhập
      </a>
    </div>
  </div>
</body>
</html>


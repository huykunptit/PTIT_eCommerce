@extends('frontend.layouts.master')
@section('title','Liên hệ - PTIT eCommerce')
@section('main-content')

@php
    $breadcrumbs = [
        ['title' => 'Trang chủ', 'url' => route('home')],
        ['title' => 'Liên hệ']
    ];
@endphp
@include('frontend.components.breadcrumbs')

<section class="contact-section" style="padding: 60px 0; background: #f8f9fa;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="section-title" style="font-size: 48px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">
                    Liên hệ với chúng tôi
                </h1>
                <p class="lead" style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto;">
                    Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn. Hãy liên hệ với chúng tôi qua các kênh sau
                </p>
            </div>
        </div>

        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-5 mb-4">
                <div class="contact-info-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: 100%;">
                    <h3 style="color: #1a1a1a; margin-bottom: 30px; font-weight: 600;">Thông tin liên hệ</h3>
                    
                    <div class="contact-item" style="display: flex; align-items: start; margin-bottom: 30px;">
                        <div class="contact-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fa fa-map-marker-alt" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 style="color: #1a1a1a; margin-bottom: 5px; font-weight: 600;">Địa chỉ</h5>
                            <p style="color: #666; margin: 0; line-height: 1.6;">
                                123 Đường ABC, Quận XYZ<br>
                                Hà Nội, Việt Nam
                            </p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: start; margin-bottom: 30px;">
                        <div class="contact-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fa fa-phone" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 style="color: #1a1a1a; margin-bottom: 5px; font-weight: 600;">Hotline</h5>
                            <p style="color: #666; margin: 0; line-height: 1.6;">
                                <a href="tel:0123456789" style="color: #D4AF37; text-decoration: none; font-weight: 600;">0123-456-789</a><br>
                                <span style="font-size: 14px;">Hỗ trợ 24/7</span>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: start; margin-bottom: 30px;">
                        <div class="contact-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fa fa-envelope" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 style="color: #1a1a1a; margin-bottom: 5px; font-weight: 600;">Email</h5>
                            <p style="color: #666; margin: 0; line-height: 1.6;">
                                <a href="mailto:support@ptit-ecommerce.com" style="color: #D4AF37; text-decoration: none;">support@ptit-ecommerce.com</a><br>
                                <span style="font-size: 14px;">Phản hồi trong 24h</span>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item" style="display: flex; align-items: start;">
                        <div class="contact-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fa fa-clock" style="color: white; font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 style="color: #1a1a1a; margin-bottom: 5px; font-weight: 600;">Giờ làm việc</h5>
                            <p style="color: #666; margin: 0; line-height: 1.6;">
                                Thứ 2 - Chủ nhật: 8:00 - 22:00<br>
                                <span style="font-size: 14px;">Hỗ trợ online 24/7</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h3 style="color: #1a1a1a; margin-bottom: 30px; font-weight: 600;">Gửi tin nhắn</h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" style="color: #1a1a1a; font-weight: 600; margin-bottom: 8px; display: block;">Họ và tên <span style="color: #dc3545;">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       style="padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; width: 100%; transition: all 0.3s;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" style="color: #1a1a1a; font-weight: 600; margin-bottom: 8px; display: block;">Email <span style="color: #dc3545;">*</span></label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       style="padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; width: 100%; transition: all 0.3s;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" style="color: #1a1a1a; font-weight: 600; margin-bottom: 8px; display: block;">Số điện thoại</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   style="padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; width: 100%; transition: all 0.3s;">
                        </div>
                        <div class="mb-3">
                            <label for="subject" style="color: #1a1a1a; font-weight: 600; margin-bottom: 8px; display: block;">Chủ đề</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   style="padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; width: 100%; transition: all 0.3s;">
                        </div>
                        <div class="mb-3">
                            <label for="message" style="color: #1a1a1a; font-weight: 600; margin-bottom: 8px; display: block;">Nội dung tin nhắn <span style="color: #dc3545;">*</span></label>
                            <textarea class="form-control" 
                                      id="message" 
                                      name="message" 
                                      rows="5" 
                                      required
                                      style="padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; width: 100%; resize: vertical; transition: all 0.3s;">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" 
                                class="btn btn-primary"
                                style="background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border: none; padding: 12px 40px; font-weight: 600; color: #1a1a1a; border-radius: 6px; transition: all 0.3s; cursor: pointer;">
                            <i class="fa fa-paper-plane mr-2"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.form-control:focus {
    border-color: #D4AF37;
    outline: none;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
}
</style>

@endsection

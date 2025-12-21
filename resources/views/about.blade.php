@extends('frontend.layouts.master')
@section('title','Về chúng tôi - PTIT eCommerce')
@section('main-content')

@php
    $breadcrumbs = [
        ['title' => 'Trang chủ', 'url' => route('home')],
        ['title' => 'Về chúng tôi']
    ];
@endphp
@include('frontend.components.breadcrumbs')

<section class="about-section" style="padding: 60px 0; background: #f8f9fa;">
    <div class="container">
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="section-title" style="font-size: 48px; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">
                    Về PTIT eCommerce
                </h1>
                <p class="lead" style="font-size: 20px; color: #666; max-width: 800px; margin: 0 auto;">
                    Chúng tôi tự hào là địa chỉ tin cậy cho những sản phẩm trang sức cao cấp, mang đến vẻ đẹp vượt thời gian
                </p>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="about-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: 100%;">
                    <div class="icon-wrapper" style="width: 80px; height: 80px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="fa fa-bullseye" style="font-size: 36px; color: white;"></i>
                    </div>
                    <h3 style="color: #1a1a1a; margin-bottom: 15px; font-weight: 600;">Sứ mệnh</h3>
                    <p style="color: #666; line-height: 1.8; margin: 0;">
                        Chúng tôi cam kết mang đến những sản phẩm trang sức cao cấp với chất lượng vàng 24K chính hãng, 
                        cùng dịch vụ chăm sóc khách hàng tận tâm và chuyên nghiệp.
                    </p>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="about-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: 100%;">
                    <div class="icon-wrapper" style="width: 80px; height: 80px; background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="fa fa-eye" style="font-size: 36px; color: white;"></i>
                    </div>
                    <h3 style="color: #1a1a1a; margin-bottom: 15px; font-weight: 600;">Tầm nhìn</h3>
                    <p style="color: #666; line-height: 1.8; margin: 0;">
                        Trở thành thương hiệu trang sức hàng đầu Việt Nam, được khách hàng tin tưởng và yêu mến 
                        nhờ chất lượng sản phẩm vượt trội và dịch vụ hoàn hảo.
                    </p>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-5" style="color: #1a1a1a; font-weight: 600;">Giá trị cốt lõi</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="value-item text-center" style="padding: 30px;">
                    <i class="fa fa-gem" style="font-size: 48px; color: #D4AF37; margin-bottom: 20px;"></i>
                    <h4 style="color: #1a1a1a; margin-bottom: 15px; font-weight: 600;">Chất lượng</h4>
                    <p style="color: #666; line-height: 1.8;">Vàng 24K chính hãng, đảm bảo độ tinh khiết và giá trị</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="value-item text-center" style="padding: 30px;">
                    <i class="fa fa-heart" style="font-size: 48px; color: #D4AF37; margin-bottom: 20px;"></i>
                    <h4 style="color: #1a1a1a; margin-bottom: 15px; font-weight: 600;">Tận tâm</h4>
                    <p style="color: #666; line-height: 1.8;">Chăm sóc khách hàng với sự nhiệt tình và chu đáo</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="value-item text-center" style="padding: 30px;">
                    <i class="fa fa-shield-alt" style="font-size: 48px; color: #D4AF37; margin-bottom: 20px;"></i>
                    <h4 style="color: #1a1a1a; margin-bottom: 15px; font-weight: 600;">Bảo hành</h4>
                    <p style="color: #666; line-height: 1.8;">Bảo hành trọn đời, cam kết chất lượng vàng</p>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row" style="background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); padding: 60px 0; border-radius: 10px; margin-top: 40px;">
            <div class="col-md-3 col-6 text-center mb-4">
                <div class="stat-number" style="font-size: 48px; font-weight: 700; color: white; margin-bottom: 10px;">1000+</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9); font-size: 16px;">Khách hàng hài lòng</div>
            </div>
            <div class="col-md-3 col-6 text-center mb-4">
                <div class="stat-number" style="font-size: 48px; font-weight: 700; color: white; margin-bottom: 10px;">500+</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9); font-size: 16px;">Sản phẩm cao cấp</div>
            </div>
            <div class="col-md-3 col-6 text-center mb-4">
                <div class="stat-number" style="font-size: 48px; font-weight: 700; color: white; margin-bottom: 10px;">24/7</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9); font-size: 16px;">Hỗ trợ khách hàng</div>
            </div>
            <div class="col-md-3 col-6 text-center mb-4">
                <div class="stat-number" style="font-size: 48px; font-weight: 700; color: white; margin-bottom: 10px;">100%</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.9); font-size: 16px;">Vàng chính hãng</div>
            </div>
        </div>
    </div>
</section>

@endsection

@extends('frontend.layouts.master')
@section('title','Contact')
@section('main-content')
<section class="section" style="padding:50px 0">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-3">Liên hệ</h2>
                <p>Email: support@example.com</p>
                <p>Hotline: 0123 456 789</p>
                <p>Địa chỉ: Hà Nội, Việt Nam</p>
            </div>
            <div class="col-md-6">
                <form method="post" action="#">
                    @csrf
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Nội dung</label>
                        <textarea class="form-control" rows="4" name="message" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Gửi</button>
                </form>
            </div>
        </div>
    </div>
    
</section>
@endsection


@extends('frontend.layouts.master')

@section('title','PTIT || Blog Detail')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('blog.index') }}">Tin tức<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">{{ $post->title }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    @php
        $photo = (string) ($post->photo ?? '');
        $imgSrc = $photo && \Illuminate\Support\Str::startsWith($photo, ['http://','https://'])
            ? $photo
            : ($photo ? asset($photo) : asset('backend/img/thumbnail-default.jpg'));
    @endphp

    <!-- Blog Detail -->
    <section class="blog-single section blog-detail-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-12">
                    <div class="blog-detail-card">
                        <div class="blog-detail-cover">
                            <img src="{{ $imgSrc }}" alt="{{ $post->title }}" referrerpolicy="no-referrer">
                        </div>

                        <div class="blog-detail-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
                                <a href="{{ route('blog.index') }}" class="blog-back-link">
                                    <i class="ti-arrow-left"></i> Quay lại tin tức
                                </a>
                                <div class="blog-meta-inline">
                                    <span><i class="fa fa-user"></i>{{ $post->author?->name ?? 'Admin' }}</span>
                                    <span><i class="fa fa-calendar"></i>{{ $post->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <h1 class="blog-detail-title">{{ $post->title }}</h1>

                            @if($post->quote)
                                <blockquote class="blog-detail-quote">
                                    <i class="fa fa-quote-left"></i>
                                    <div>{!! $post->quote !!}</div>
                                </blockquote>
                            @endif

                            <div class="blog-detail-content">
                                {!! $post->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .blog-detail-card{background:#fff;border:1px solid #eee;border-radius:10px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06);}
        .blog-detail-cover{width:100%;height:420px;background:#f7f7f7;}
        .blog-detail-cover img{width:100%;height:100%;object-fit:cover;display:block;}
        .blog-detail-body{padding:28px 26px;}
        .blog-back-link{display:inline-flex;align-items:center;gap:8px;color:#333;text-decoration:none;font-weight:600;}
        .blog-back-link:hover{color:#D4AF37;}
        .blog-meta-inline{display:flex;gap:14px;color:#666;font-size:13px;}
        .blog-meta-inline i{margin-right:6px;color:#D4AF37;}
        .blog-detail-title{margin:14px 0 18px 0;font-size:30px;line-height:1.25;font-weight:700;color:#111;}
        .blog-detail-quote{display:flex;gap:12px;align-items:flex-start;background:#fafafa;border-left:4px solid #D4AF37;padding:14px 16px;border-radius:6px;margin:0 0 18px 0;color:#333;}
        .blog-detail-quote i{color:#D4AF37;margin-top:2px;}
        .blog-detail-content{color:#222;font-size:16px;line-height:1.8;}
        .blog-detail-content p{margin-bottom:14px;}
        .blog-detail-content img{max-width:100%;height:auto;border-radius:8px;}
        .blog-detail-content a{color:#D4AF37;text-decoration:underline;}
        @media (max-width: 576px){
            .blog-detail-cover{height:240px;}
            .blog-detail-body{padding:18px 16px;}
            .blog-detail-title{font-size:22px;}
        }
    </style>
@endpush

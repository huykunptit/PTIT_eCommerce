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
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Blog Single Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Blog Detail -->
    <section class="blog-single section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="blog-single-main">
                        <div class="row">
                            <div class="col-12">
                                <div class="image">
                                    @php
                                        $photo = (string) ($post->photo ?? '');
                                        $imgSrc = $photo && \Illuminate\Support\Str::startsWith($photo, ['http://','https://'])
                                            ? $photo
                                            : ($photo ? asset($photo) : asset('backend/img/thumbnail-default.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" referrerpolicy="no-referrer">
                                </div>
                                <div class="blog-detail">
                                    <h2 class="blog-title">{{$post->title}}</h2>
                                    <div class="blog-meta">
                                        <span class="author">
                                            <a href="javascript:void(0);"><i class="fa fa-user"></i>{{ $post->author?->name ?? 'Admin' }}</a>
                                            <a href="javascript:void(0);"><i class="fa fa-calendar"></i>{{ $post->created_at->format('d/m/Y') }}</a>
                                        </span>
                                    </div>
                                    <div class="content">
                                        @if($post->quote)
                                        <blockquote> <i class="fa fa-quote-left"></i> {!! ($post->quote) !!}</blockquote>
                                        @endif
                                        <p>{!! ($post->description) !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
                                </div>

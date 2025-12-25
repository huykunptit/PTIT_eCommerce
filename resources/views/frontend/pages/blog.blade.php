@extends('frontend.layouts.master')

@section('title','PTIT  || Blog Page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Blog Grid Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Blog List -->
    <section class="blog-single shop-blog grid section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        @forelse($posts as $post)
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="shop-single-blog">
                                    @php
                                        $photo = (string) ($post->photo ?? '');
                                        $imgSrc = $photo && \Illuminate\Support\Str::startsWith($photo, ['http://','https://'])
                                            ? $photo
                                            : ($photo ? asset($photo) : asset('backend/img/thumbnail-default.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" referrerpolicy="no-referrer">
                                    <div class="content">
                                        <p class="date">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            {{ $post->created_at->format('d/m/Y') }}
                                            <span class="float-right">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                {{ $post->author?->name ?? 'Admin' }}
                                            </span>
                                        </p>
                                        <a href="{{ route('blog.show', $post->id) }}" class="title">{{ $post->title }}</a>
                                        <p>{!! html_entity_decode($post->summary) !!}</p>
                                        <a href="{{ route('blog.show', $post->id) }}" class="more-btn">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">Chưa có bài viết nào.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <style>
        .pagination{
            display:inline-flex;
        }
    </style>

@endpush

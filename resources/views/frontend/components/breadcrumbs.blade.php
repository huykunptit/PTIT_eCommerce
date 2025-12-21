@php
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

@if(!empty($breadcrumbs))
<nav aria-label="breadcrumb" class="breadcrumb-nav" style="background: #f8f9fa; padding: 15px 0; margin-bottom: 30px;">
    <div class="container">
        <ol class="breadcrumb" style="margin: 0; padding: 0; list-style: none; display: flex; align-items: center; flex-wrap: wrap;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">
                    <i class="fa fa-home"></i> Trang chủ
                </a>
            </li>
            @foreach($breadcrumbs as $index => $breadcrumb)
                @if($index < count($breadcrumbs) - 1)
                    <li class="breadcrumb-separator" style="margin: 0 10px; color: #999;">
                        <i class="fa fa-chevron-right" style="font-size: 12px;"></i>
                    </li>
                    <li class="breadcrumb-item">
                        @if(isset($breadcrumb['url']))
                            <a href="{{ $breadcrumb['url'] }}" style="color: #666; text-decoration: none;">
                                {{ $breadcrumb['title'] }}
                            </a>
                        @else
                            <span style="color: #666;">{{ $breadcrumb['title'] }}</span>
                        @endif
                    </li>
                @else
                    <li class="breadcrumb-separator" style="margin: 0 10px; color: #999;">
                        <i class="fa fa-chevron-right" style="font-size: 12px;"></i>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #D4AF37; font-weight: 600;">
                        {{ $breadcrumb['title'] }}
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>
@endif


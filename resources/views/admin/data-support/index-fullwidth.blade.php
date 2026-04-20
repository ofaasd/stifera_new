@extends('layouts.fullwidth', ['CurrentPage' => $CurrentPage])

@section('content')
<div class="content-body">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="cm-content-box box-primary">
                    <div class="content-title cm-title">
                        <div class="cpa">
                            <i class="fa-solid fa-download me-2"></i>{{ $title }}
                        </div>
                        <div class="tools">
                            <a href="javascript:void(0);" class="expand handle"><i class="fal fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="cm-content-body">
                        <div class="card-body">
                            <p class="text-muted mb-4">Pilih data yang ingin Anda download. Setiap submenu menyediakan data dalam format yang dapat diolah lebih lanjut.</p>

                            <div class="row g-4">
                                @foreach($menus as $menu)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ $menu['url'] }}" class="text-decoration-none">
                                            <div class="card h-100 border-0 shadow-sm menu-card transition-all">
                                                <div class="card-body text-center py-5">
                                                    <div class="menu-icon mb-3">
                                                        <i class="{{ $menu['icon'] }} fa-3x text-primary"></i>
                                                    </div>
                                                    <h5 class="card-title fw-semibold mb-2">{{ $menu['title'] }}</h5>
                                                    <p class="card-text text-muted small">{{ $menu['description'] }}</p>
                                                </div>
                                                <div class="card-footer bg-light border-0 text-center py-3">
                                                    <span class="text-primary small fw-semibold">
                                                        Buka <i class="fa-solid fa-arrow-right ms-1"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('local-css')
<style>
    .menu-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }

    .menu-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
    }

    .menu-card:hover .menu-icon {
        background: rgba(13, 110, 253, 0.15);
    }

    .card-title {
        color: #333;
    }

    .card-text {
        font-size: 0.875rem;
    }
</style>
@endsection

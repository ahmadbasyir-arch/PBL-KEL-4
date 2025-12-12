@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-center p-3 p-md-4" style="min-height: calc(100vh - 80px);">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <!-- Maximize width: col-12 col-xxl-10 -->
            <div class="col-12 col-xxl-10">
                <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                    <div class="row g-0">
                        <!-- Left Side: Info (Gradient) -->
                        <!-- Using col-lg-4 to ensure side-by-side on large screens, inner flex for content centering -->
                        <div class="col-lg-4 d-none d-lg-block text-white text-center" 
                             style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); position: relative; min-height: 500px;">
                            
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center p-5">
                                <!-- Decorative Circles -->
                                <div style="position: absolute; top: -60px; left: -60px; width: 180px; height: 180px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                                <div style="position: absolute; bottom: -40px; right: -40px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

                                <div class="mb-4 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm position-relative" style="width: 90px; height: 90px; color: #4f46e5; z-index: 2;">
                                    <i class="fas fa-bullhorn fa-3x"></i>
                                </div>
                                <h3 class="font-weight-bold mb-3 position-relative" style="z-index: 2;">Suara Anda Penting!</h3>
                                <p class="mb-0 px-3 position-relative" style="opacity: 0.95; font-size: 1.1rem; line-height: 1.6; z-index: 2;">
                                    Sampaikan kritik dan saran Anda untuk kemajuan fasilitas Sarana & Prasarana Teknik Informasi.
                                </p>
                            </div>
                        </div>

                        <!-- Right Side: Form -->
                        <!-- col-lg-8 fills the rest -->
                        <div class="col-12 col-lg-8 bg-white">
                            <div class="p-4 p-md-5 h-100 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="mr-3 d-lg-none bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <h4 class="font-weight-bold text-dark mb-0" style="font-size: 1.5rem;">Formulir Kritik & Saran</h4>
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" style="background-color: #dcfce7; color: #166534;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle mr-2 fa-lg"></i> 
                                            <span class="font-weight-bold">{{ session('success') }}</span>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('ulasan.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="form-group mb-4">
                                        <label class="text-secondary font-weight-bold small text-uppercase mb-3 d-block spacing-wide">Tingkat Kepuasan</label>
                                        <div class="rating-container p-4 rounded bg-light d-flex justify-content-center align-items-center">
                                            <!-- Star Rating -->
                                            <div class="rating-css">
                                                <div class="star-icon">
                                                    <input type="radio" value="1" name="rating" checked id="rating1">
                                                    <label for="rating1" class="fa fa-star" title="Sangat Buruk"></label>
                                                    <input type="radio" value="2" name="rating" id="rating2">
                                                    <label for="rating2" class="fa fa-star" title="Buruk"></label>
                                                    <input type="radio" value="3" name="rating" id="rating3">
                                                    <label for="rating3" class="fa fa-star" title="Cukup"></label>
                                                    <input type="radio" value="4" name="rating" id="rating4">
                                                    <label for="rating4" class="fa fa-star" title="Baik"></label>
                                                    <input type="radio" value="5" name="rating" id="rating5">
                                                    <label for="rating5" class="fa fa-star" title="Sangat Baik"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="text-secondary font-weight-bold small text-uppercase mb-2 spacing-wide">Pesan Anda</label>
                                        <textarea name="komentar" class="form-control border-light bg-light" rows="5" placeholder="Tuliskan pengalaman, keluhan, atau saran Anda secara detail..." style="border-radius: 12px; padding: 20px; font-size: 1rem; resize: none; line-height: 1.6;" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary d-block w-100 rounded-pill font-weight-bold shadow-lg py-3 transition-hover text-uppercase spacing-wide" style="letter-spacing: 1px;">
                                        <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Improved Rating CSS */
    .rating-css div {
        color: #fbbf24;
        font-family: sans-serif;
        text-transform: uppercase;
        padding: 0;
    }
    .rating-css input { display: none; }
    .rating-css input + label {
        font-size: 40px; /* Larger stars for better UX */
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        margin: 0 8px;
        color: #d1d5db; /* Gray-300 */
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .rating-css input:checked + label ~ label { color: #d1d5db; }
    .rating-css input + label:hover, 
    .rating-css input + label:hover ~ label { 
        color: #f59e0b; 
        transform: scale(1.15);
    }
    .rating-css .star-icon input:checked + label,
    .rating-css .star-icon input:checked ~ label {
        color: #f59e0b;
    }
    
    .star-icon {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
    }

    .rating-container {
        border: 2px dashed #e5e7eb;
        transition: border-color 0.3s;
    }
    .rating-container:hover {
        border-color: #3b82f6;
    }

    .transition-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3) !important;
    }

    .spacing-wide {
        letter-spacing: 0.5px;
    }
    
    /* Ensure Bootstrap 4/5 compatibility for width */
    .w-100 { width: 100% !important; }
</style>
@endsection

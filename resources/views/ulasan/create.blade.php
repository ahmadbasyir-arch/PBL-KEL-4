@extends('layouts.app')

@section('content')

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" style="background-color: #dcfce7; color: #166534;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle mr-2 fa-lg"></i> 
                <span class="font-weight-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="interactive-table">
        <div class="section-header" style="margin-bottom: 25px; border-bottom: 1px solid #f3f4f6; padding-bottom: 15px;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 12px; margin: 0;">
                <div style="background: #e0e7ff; padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bullhorn" style="color: #4f46e5; font-size: 1.2rem;"></i>
                </div>
                Formulir Kritik & Saran
            </h2>
            <p style="margin-top: 5px; color: #6b7280; font-size: 0.95rem;">
                Suara Anda penting! Sampaikan masukan untuk kemajuan fasilitas Sarana & Prasarana Teknik Informasi.
            </p>
        </div>

        <form action="{{ route('ulasan.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151; display: block; margin-bottom: 10px;">Tingkat Kepuasan</label>
                        <div class="rating-container p-3 rounded" style="background-color: #f9fafb; border: 1px dashed #d1d5db; display: inline-block; min-width: 300px;">
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
                </div>

                <div class="col-md-12">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Pesan Anda</label>
                        <textarea name="komentar" class="form-control" rows="6" placeholder="Tuliskan pengalaman, keluhan, atau saran Anda secara detail..." 
                        style="border-radius: 8px; border: 1px solid #d1d5db; padding: 15px; font-size: 1rem; line-height: 1.6;" required></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary px-5 py-2" style="background-color: #4f46e5; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
            </div>
        </form>
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
        font-size: 40px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        cursor: pointer;
        margin: 0 5px;
        color: #d1d5db; 
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .rating-css input:checked + label ~ label { color: #d1d5db; }
    .rating-css input + label:hover, 
    .rating-css input + label:hover ~ label { 
        color: #f59e0b; 
        transform: scale(1.1);
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
</style>
@endsection

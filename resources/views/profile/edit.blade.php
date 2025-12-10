@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="content-wrapper" style="padding: 0 30px;">
    <div class="welcome-banner">
        <h1>Pengaturan Profil</h1>
        <p>Kelola informasi data diri dan foto profil akun Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="interactive-table" style="max-width: 800px; margin: 0 auto;">
        <div class="section-header" style="margin-bottom: 25px; border-bottom: 1px solid #f3f4f6; padding-bottom: 15px;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px; margin: 0;">
                <i class="fas fa-user-cog" style="color: #3b82f6;"></i> Edit Profil
            </h2>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                {{-- Kolom Kiri: Foto Profil --}}
                <div style="flex: 1; min-width: 250px; text-align: center;">
                    <div style="margin-bottom: 15px;">
                        @if ($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        @else
                            <div style="width: 150px; height: 150px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #9ca3af; font-size: 3rem; font-weight: bold; border: 4px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                        <label for="foto_profil" style="display: inline-block; cursor: pointer; background: #eff6ff; color: #1d4ed8; padding: 8px 15px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; transition: background 0.2s;">
                            <i class="fas fa-camera"></i> Ganti Foto
                        </label>
                        @if($user->foto_profil)
                            <button type="button" onclick="hapusFoto()" style="background: #fee2e2; color: #b91c1c; border: none; padding: 8px 15px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                                <i class="fas fa-trash-alt"></i> Hapus Foto
                            </button>
                        @endif
                    </div>
                    
                    <input type="hidden" name="hapus_foto" id="input_hapus_foto" value="0">
                    <input type="file" name="foto_profil" id="foto_profil" style="display: none;" accept="image/*" onchange="previewImage(this)">
                    <p style="font-size: 0.8rem; color: #6b7280; margin-top: 10px;">Format: JPG, PNG. Maks: 2MB.</p>
                </div>

                {{-- Kolom Kanan: Form Data --}}
                <div style="flex: 2; min-width: 300px;">
                    {{-- Nama Lengkap --}}
                    <div style="margin-bottom: 20px;">
                        <label for="namaLengkap" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Nama Lengkap</label>
                        <input type="text" name="namaLengkap" id="namaLengkap" value="{{ old('namaLengkap', $user->namaLengkap ?? $user->name) }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border 0.2s;" required>
                        @error('namaLengkap') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                    </div>

                    {{-- Email (Readonly) --}}
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Email</label>
                        <input type="email" value="{{ $user->email }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; color: #6b7280;" readonly>
                    </div>

                    {{-- NIM / NIP (Readonly) --}}
                    @if($user->role === 'mahasiswa')
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">NIM</label>
                        <input type="text" value="{{ $user->nim ?? '-' }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; color: #6b7280;" readonly>
                    </div>
                    @endif

                    {{-- No Telepon --}}
                    <div style="margin-bottom: 20px;">
                        <label for="telepon" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">No. Telepon / WhatsApp</label>
                        <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $user->telepon) }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border 0.2s;" placeholder="08xxxxxxxxxx">
                        @error('telepon') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                    </div>

                    <div style="border-top: 1px solid #f3f4f6; margin: 20px 0;"></div>

                    {{-- Ganti Password (Optional) --}}
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 15px;">Ganti Password (Opsional)</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="password" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password') <small style="color: #ef4444;">{{ $message }}</small> @enderror
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label for="password_confirmation" style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" placeholder="Ulangi password baru">
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-primary" style="background-color: #3b82f6; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        document.getElementById('input_hapus_foto').value = "0"; // Reset hapus flag jika upload baru
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                updateAllAvatars(e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function hapusFoto() {
        if(confirm('Apakah Anda yakin ingin menghapus foto profil? Simpan perubahan untuk menerapkan.')) {
            document.getElementById('input_hapus_foto').value = "1";
            document.getElementById('foto_profil').value = ""; // Reset file input
            
            // Generate Placeholder dari inisial nama
            let nama = "{{ $user->name }}";
            let inisial = nama.charAt(0).toUpperCase(); /* Simple logic mainly for immediate feedback */
            
            // Create Placeholder Element String
            // Note: Better to match backend logic, but simple first char is okay for immediate preview
            let placeholderHTML = '<div class="avatar-placeholder" style="width: 150px; height: 150px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #9ca3af; font-size: 3rem; font-weight: bold; border: 4px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' + inisial + '</div>';

            let mainContainer = document.querySelector('.interactive-table .col-foto'); 
            // Note: I need to make sure I target the right container. The replacement replaced the inner image.
            
            // Let's use the helper function logic but specifically for resetting to placeholder
            updateAllAvatarsToPlaceholder(inisial);
        }
    }

    function updateAllAvatars(src) {
        // 1. Main Preview
        let mainContainer = document.querySelector('.interactive-table div[style*="text-align: center"] div[style*="margin-bottom: 15px"]');
        if(mainContainer) {
            mainContainer.innerHTML = '<img src="'+src+'" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
        }

        // 2. Sidebar & Header
        let sidebarAvatar = document.querySelector('.sidebar-user .user-avatar');
        let headerAvatar = document.querySelector('.header-right .profile-avatar');
        [sidebarAvatar, headerAvatar].forEach(container => {
            if (container) {
                container.innerHTML = '<img src="'+src+'" style="width: 100%; height: 100%; object-fit: cover;">';
            }
        });
    }

    function updateAllAvatarsToPlaceholder(char) {
        // 1. Main Preview
        let mainContainer = document.querySelector('.interactive-table div[style*="text-align: center"] div[style*="margin-bottom: 15px"]');
        if(mainContainer) {
            mainContainer.innerHTML = '<div style="width: 150px; height: 150px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #9ca3af; font-size: 3rem; font-weight: bold; border: 4px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' + char + '</div>';
        }

        // 2. Sidebar & Header
        let sidebarAvatar = document.querySelector('.sidebar-user .user-avatar');
        let headerAvatar = document.querySelector('.header-right .profile-avatar');
        [sidebarAvatar, headerAvatar].forEach(container => {
            if (container) {
                // Revert to original text based placeholder style or just simpler one
                container.innerHTML = '<div class="avatar-placeholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">' + char + '</div>';
            }
        });
    }
</script>
@endsection

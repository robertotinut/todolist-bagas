@extends('partials.layouts.master')

@section('title', 'Donasi & Dukungan | SLADA')
@section('title-sub', 'Dukungan')
@section('pagetitle', 'Donasi & Dukungan')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-heart-fill text-danger me-1"></i> Dukung Pengembangan SLADA</h5>
                <p class="text-secondary fs-13 mb-4">
                    Aplikasi SLADA ini sepenuhnya gratis digunakan untuk siapapun tanpa biaya lisensi. 
                    Namun, jika aplikasi ini bermanfaat dalam menunjang produktivitas dan pengerjaan agenda harian Anda, Anda dapat memberikan donasi sukarela bebas berapapun nominalnya untuk mendukung pemeliharaan dan pengembangan platform ini.
                </p>

                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="card border border-primary-subtle bg-light-subtle shadow-none" style="border-radius: 8px;">
                            <div class="card-body p-4 text-center">
                                <h6 class="fw-bold text-primary mb-3">Metode Pembayaran (GoPay)</h6>
                                
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-3 bg-white p-3 border rounded">
                                    <div class="text-start">
                                        <small class="text-muted d-block fs-11 text-uppercase fw-semibold">Nomor GoPay</small>
                                        <span class="fs-16 fw-bold text-dark" id="gopay-number">0859175756451</span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyGopayNumber()">
                                        <i class="bi bi-clipboard"></i> Salin
                                    </button>
                                </div>

                                <div class="text-start p-3 bg-white border rounded">
                                    <small class="text-muted d-block fs-11 text-uppercase fw-semibold">Nama Penerima</small>
                                    <span class="fs-15 fw-bold text-dark">Jalu Dwi Bagaskara</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted fs-12 mb-0">
                        Terima kasih banyak atas segala bentuk dukungan dan apresiasi yang Anda berikan!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyGopayNumber() {
        const numberText = document.getElementById('gopay-number').innerText;
        navigator.clipboard.writeText(numberText).then(() => {
            Swal.fire({
                title: 'Teks Berhasil Disalin',
                text: 'Nomor GoPay telah disalin ke clipboard Anda.',
                icon: 'success',
                confirmButtonColor: '#0d6efd',
                timer: 2000
            });
        }).catch(err => {
            console.error('Gagal menyalin teks: ', err);
        });
    }
</script>

@endsection

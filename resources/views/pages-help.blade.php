@extends('partials.layouts.master')

@section('title', 'Pusat Bantuan | SLADA')
@section('title-sub', 'Info')
@section('pagetitle', 'Pusat Bantuan & FAQ')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4">Pusat Bantuan & Tanya Jawab (FAQ)</h5>

                <div class="d-flex flex-column gap-4">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tanya: Bagaimana cara memindahkan status tugas di Kanban Board?</h6>
                        <p class="text-secondary fs-13 mb-0">
                            Pilih menu <strong>Kanban Board</strong> dari sidebar, klik salah satu kartu kategori, lalu klik tombol <strong>Mulai</strong> (untuk mengubah status menjadi <em>In Progress</em>) atau tombol <strong>Selesai</strong> (untuk mengubah status menjadi <em>Completed</em>).
                        </p>
                    </div>

                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tanya: Mengapa ada tombol hapus/ubah yang dinonaktifkan dengan icon gembok?</h6>
                        <p class="text-secondary fs-13 mb-0">
                            Itu menandakan kategori atau proyek default bawaan sistem. Untuk menjaga integritas data dasar workspace, kategori default dikunci dan hanya dapat diubah oleh administrator (Admin/Owner).
                        </p>
                    </div>

                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tanya: Bagaimana sistem menghitung laporan produktivitas harian?</h6>
                        <p class="text-secondary fs-13 mb-0">
                            Sistem secara otomatis mengkaji penyelesaian tugas setiap hari. Jika ada tugas selesai hari ini dan tidak ada tugas aktif yang melewati jatuh tempo, hari Anda dinilai <strong>Sangat Produktif</strong>. Sebaliknya, jika ada tugas yang tertunda melewati batas tenggat, sistem menandainya sebagai <strong>Kendala</strong>.
                        </p>
                    </div>

                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tanya: Berapa ukuran berkas lampiran yang diizinkan?</h6>
                        <p class="text-secondary fs-13 mb-0">
                            Maksimal ukuran unggahan file lampiran adalah <strong>10 Megabyte (10MB)</strong> per file. Anda bisa mengunggahnya langsung saat menambah tugas atau lewat modal detail tugas.
                        </p>
                    </div>
                </div>

                <div class="border-top mt-4 pt-3 text-center">
                    <p class="text-muted fs-12 mb-0">
                        Butuh bantuan lebih lanjut? Hubungi administrator Anda melalui WhatsApp: <a href="https://wa.me/6285854543488" target="_blank" class="fw-semibold text-success"><i class="bi bi-whatsapp"></i> +62 858-5454-3488</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

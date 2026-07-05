@extends('partials.layouts.master')

@section('title', 'Tentang SLADA | SLADA')
@section('title-sub', 'Info')
@section('pagetitle', 'Tentang SLADA')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Tentang Aplikasi SLADA</h5>
                <p class="text-secondary fs-13 mb-4">
                    SLADA (Sistem Layanan Asisten Daftar Agenda) adalah aplikasi manajemen tugas personal. 
                    Aplikasi ini dirancang untuk mempermudah pencatatan tugas harian, pengelompokan berdasarkan kategori kerja (area), dan pemantauan agenda kerja melalui papan Kanban, kalender terintegrasi, serta laporan harian otomatis.
                </p>

                <h6 class="fw-bold text-dark mb-2">Modul Utama:</h6>
                <ul class="text-secondary fs-13 mb-4 ps-3">
                    <li class="mb-2"><strong>Dashboard:</strong> Ringkasan visual statistik tugas aktif, progres proyek, dan jadwal jatuh tempo terdekat.</li>
                    <li class="mb-2"><strong>Kanban Board:</strong> Papan status tugas per kategori untuk melacak proses kerja (Rencana, Proses, Selesai).</li>
                    <li class="mb-2"><strong>Calendar:</strong> Pemetaan visual seluruh tugas secara bulanan berdasarkan tanggal jatuh tempo.</li>
                    <li class="mb-2"><strong>Jurnal Refleksi:</strong> Laporan produktivitas harian yang dihitung secara otomatis berdasarkan penyelesaian tugas dan kendala tenggat waktu.</li>
                </ul>

                <h6 class="fw-bold text-dark mb-2">Panduan Hak Akses Pengguna:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered fs-13 text-secondary mt-2 mb-0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 30%;">Peran Pengguna</th>
                                <th>Deskripsi Hak Akses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Owner / Admin</strong></td>
                                <td>Memiliki akses penuh untuk mengelola pengguna, workspace, proyek, kategori, serta memantau jurnal refleksi seluruh tim.</td>
                            </tr>
                            <tr>
                                <td><strong>Member</strong></td>
                                <td>Dapat membuat, mengedit, dan memindahkan tugas miliknya sendiri di papan Kanban, serta melihat jurnal refleksi pribadinya.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

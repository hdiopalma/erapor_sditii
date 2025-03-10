@extends('adminlte::page')

{{-- @section('title', 'Data Siswa') --}}

@section('content_header')

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="vendor/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="vendor/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="vendor/adminlte/dist/css/adminlte.min.css">
    {{-- <link rel="stylesheet" href="dist/css/styleIndex.css"> --}}

    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Data Kelas</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                {{ Breadcrumbs::render('dataKelas') }}
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-secondary card-tabs">
        <div class="card-header p-0 pt-0 bg-gradient-green">
            {{-- tab control --}}
            <ul class="nav nav-tabs" id="kategori-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="controller-tab-kelas-table" data-toggle="pill"
                        href="#content-tab-kelas-table" role="tab" aria-controls="content-tab-kelas-table"
                        aria-selected="true">
                        <i class="fas fa-xs fa-table fa-fw"></i>
                        Daftar Guru</a>
                </li>
                @if (Auth::user()->role->contains('role', 'Administrator'))
                    <li class="nav-item">
                        <a class="nav-link" id="controller-tab-kelas-add" data-toggle="pill" href="#content-tab-kelas-add"
                            role="tab" aria-controls="content-tab-kelas-add" aria-selected="false">
                            <i class="fas fa-xs fa-plus fa-fw"></i>
                            Tambah Kelas</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            {{-- tab daftar --}}
            <div class="tab-content" id="guruTabContent">
                <div class="tab-pane active show" id="content-tab-kelas-table" role="tabpanel"
                    aria-labelledby="controller-tab-kelas-table">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Sub Kelas</th>
                                <th>Wali Kelas</th>
                                {{-- <th>Peran</th> --}}
                                @if (Auth::user()->role->contains('role', 'Administrator'))
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        {{-- @forelse ($user as $u)
                  <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->user_name }}</td>
                    <td>{{ $u->role->role }}</td>
                    <td>Tombol</td>
                  </tr>
                  @empty
                  <td>-</td> 
                  @endforelse --}}
                    </table>
                </div>
                {{-- /tab daftar --}}
                @if (Auth::user()->role->contains('role', 'Administrator'))
                    {{-- tab tambah --}}
                    <div class="tab-pane fade" id="content-tab-kelas-add" role="tabpanel"
                        aria-labelledby="controller-tab-kelas-add">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bs-stepper-content">
                                        <form id="form_tambah_kelas">
                                            @csrf

                                            <div class="form-group">
                                                <label for="kelas" class="form-label">Kelas Perwalian</label>
                                                <select class="form-control @error('kelas') is-invalid @enderror"
                                                    id="kelas" name="kelas" data-placeholder="-pilih kelas perwalian-"
                                                    style="width: 100%;">
                                                    <option selected disabled>-Pilih Kelas Perwalian-</option>
                                                    @foreach ($data_kelas as $k)
                                                        <option value={{ $k->id }}>{{ $k->nama_kelas }}</option>
                                                    @endforeach
                                                </select>
                                                @error('role_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="nama_sub_kelas" class="form-label">Nama Sub Kelas</label>
                                                <input type="text"
                                                    class="form-control @error('nama_sub_kelas') is-invalid @enderror"
                                                    id="nama_sub_kelas" name="nama_sub_kelas"
                                                    placeholder="Masukkan nama sub kelas"
                                                    value="{{ old('nama_sub_kelas') }}" required>
                                                @error('nama_sub_kelas')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="wali_kelas" class="form-label">Wali Kelas</label>
                                                <select class="form-control @error('wali_kelas') is-invalid @enderror"
                                                    id="wali_kelas" name="wali_kelas" data-placeholder="-pilih guru-"
                                                    style="width: 100%;">
                                                    <option selected disabled>-Pilih Guru-</option>
                                                    <option value="0"><span class="text-red">---Belum Ada---</span></option>
                                                    @foreach ($data_guru as $g)
                                                        <option value={{ $g->id }}>{{ $g->nama_guru }}</option>
                                                    @endforeach
                                                        
                                                </select>
                                                @error('wali_kelas')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <x-adminlte-button type="submit" class="btn bg-gradient-green col-12 simpan"
                                                icon="fas fa fa-fw fa-save" label="Simpan Data" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- /tab tambah --}}
                @endif
            </div>
        </div>
    </div>
    </div>
@stop
@section('head_js')
    <!-- jQuery -->
    {{-- <script type="text/javascript"  src={{ asset('vendor/jquery/jquery.min.js') }}></script> --}}

    <!-- Bootstrap 4 -->
    <script type="text/javascript" src={{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}></script>
    <!-- DataTables  & Plugins -->
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}></script> --}}
    <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}></script>
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/jszip/jszip.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/pdfmake/pdfmake.min.js') }}></script> --}}
    <script src={{ asset('public/AdminLTE-3.2.0/plugins/pdfmake/vfs_fonts.js') }}></script>
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-buttons/js/buttons.html5.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-buttons/js/buttons.print.min.js') }}></script> --}}
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/plugins/datatables-buttons/js/buttons.colVis.min.js') }}></script> --}}
    {{-- sendiri, versi lama --}}{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> --}}
    {{-- sendiri --}}{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script> --}}

    <!-- AdminLTE App -->
    {{-- <script src="vendor/adminlte/dist/js/adminlte.min.js"></script> --}}
    <!-- AdminLTE for demo purposes -->
    {{-- <script src={{ asset('public/AdminLTE-3.2.0/dist/js/demo.js') }}></script> --}}
    <!-- Page specific script -->
@stop
@section('js')
    <script>
        $(document).ready(function() {
            //set csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        function resetForm() {
            $('#form_tambah_kelas').reset();
            $('#form_tambah_kelas').find('.is-invalid').removeClass('is-invalid');
            $('#form_tambah_kelas').find('.error').remove();
        }
    </script>
    <script>
        $(document).ready(function() {
            //DataTable
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                processing: true,
                serverSide: true,
                width: '100%',
                ajax: {
                    url: "{{ route('kelas.getTable') }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        sClass: 'text-center',
                        width: '5%'
                    },
                    {
                        data: 'kelas.nama_kelas',
                        name: 'kelas.nama_kelas'
                    },
                    {
                        data: 'nama_sub_kelas',
                        name: 'nama_sub_kelas'
                    },
                    {
                        data: 'guru.nama_guru',
                        name: 'guru.nama_guru',
                    },
                    @if (Auth::user()->role->contains('role', 'Administrator'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            sClass: 'text-center',
                            width: '25%',
                        }
                    @endif
                ]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            //Initialize Select2 Elements
        });
    </script>

    {{-- ajax tambah guru --}}
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
            $('#controller-tab-kelas-add').on('click', function() {
                $('#form_tambah_kelas')[0].reset();
            });
            $('#form_tambah_kelas').on('submit', function(e) {
                e.preventDefault();
                let kelas = $('#kelas').val();
                let nama_sub_kelas = $('#nama_sub_kelas').val();
                let wali_kelas = $('#wali_kelas').val();


                $.ajax({
                    type: "POST",
                    url: "{{ route('dataKelas.store') }}",
                    data: {
                        kelas: kelas,
                        nama_sub_kelas: nama_sub_kelas,
                        wali_kelas: wali_kelas,
                    },
                    dataType: "JSON",
                    success: function(response) {
                        // if (response.success) {
                        $('#example1').DataTable().ajax.reload();
                        $('#form_tambah_kelas')[0].reset();
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Data berhasil disimpan!',
                            icon: 'success',
                            iconColor: '#fff',
                            toast: true,
                            background: '#45FFCA',
                            position: 'top-center',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    },
                    error: function(err) {

                        if (err.status == 422) {
                            $('#form_tambah_kelas').find(".is-invalid").removeClass(
                                "is-invalid");
                            $('#form_tambah_kelas').find('.error').remove();

                            //send error to adminlte form
                            $.each(err.responseJSON.errors, function(i, error) {
                                var el = $(document).find('[name="' + i + '"]');
                                if (el.hasClass('is-invalid')) {
                                    el.removeClass('is-invalid');
                                    el.next().remove();
                                }
                                el.addClass('is-invalid');
                                el.after($('<span class="error invalid-feedback">' +
                                    error[0] + '</span>'));
                            });
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Mohon isi data dengan benar!',
                                icon: 'error',
                                iconColor: '#fff',
                                toast: true,
                                background: '#f8bb86',
                                position: 'top-center',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                        }
                    }
                });
            });
        });
    </script>

    <script>
        //delete via ajax
        $(document).on('click', '.delete', function() {
            let id = $(this).attr('data-id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Semua data yang berkaitan akan ikut terhapus dan tak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('dataKelas.destroy', '') }}" + '/' + id,
                        success: function(response) {
                            if (response.success != null) {
                                $('#example1').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data Berhasil Dihapus',
                                    icon: 'success',
                                    iconColor: '#fff',
                                    color: '#fff',
                                    toast: true,
                                    background: '#00FF00',
                                    position: 'top',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                });
                            } else {
                                Swal.fire({
                                    title: 'Data Gagal Dihapus!',
                                    text: ' Kelas masih memiliki siswa! Hapus atau pindahkan siswa terlebih dahulu!',
                                    icon: 'error',
                                    iconColor: '#fff',
                                    background: '#f8bb86',
                                    position: 'center',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop

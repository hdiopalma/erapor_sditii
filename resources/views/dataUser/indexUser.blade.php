@extends('adminlte::page')

{{-- @section('title', 'Data User') --}}

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
<!-- Select2 -->
<link rel="stylesheet" href="vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<div class="row mb-2">
  <div class="col-sm-6">
      <h1 class="m-0">Data User</h1>
  </div>
  <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
          {{ Breadcrumbs::render('dataUser')}}
      </ol>
  </div>
</div>

@stop
@section('content')
<div class="card card-tabs card-secondary">
  <div class="card-header card-secondary p-0 pt-0 bg-gradient-green">
    {{-- tab control --}}
    <ul class="nav nav-tabs" id="kategori-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="controller-tab-user-table" data-toggle="pill" href="#content-tab-user-table" role="tab" aria-controls="content-tab-user-table" aria-selected="true">
          <i class="fas fa-xs fa-table fa-fw"></i>
          Daftar User</a>
        </li>

        @if (Auth::user()->role->contains('role', 'Administrator'))
        <li class="nav-item">
          <a class="nav-link" id="controller-tab-user-add" data-toggle="pill" href="#content-tab-user-add" role="tab" aria-controls="content-tab-user-add" aria-selected="false">
            <i class="fas fa-xs fa-plus fa-fw"></i>
            Tambah User</a>
          </li>
        </ul>
        @endif

      </div>
      {{-- /tab control --}}
      <div class="card-body">
        {{-- tab daftar --}}
        <div class="tab-content" id="userTabContent">
          <div class="tab-pane active show" id="content-tab-user-table" role="tabpanel"
          aria-labelledby="controller-tab-user-table">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Peran</th>
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
          {{-- tab tambah --}}
          <div class="tab-pane fade" id="content-tab-user-add" role="tabpanel" aria-labelledby="controller-tab-user-add">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="bs-stepper-content">
                    <form id="form_tambah_user">
                      @csrf
                      <div class="form-group">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="-masukkan nama pengguna-">
                        @error('name')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="-masukkan nama pengguna-">
                        @error('email')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="user_name" class="form-label">Username</label>
                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" name="user_name" id="user_name" placeholder="-masukkan username pengguna-">
                        @error('user_name')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="-masukkan password pengguna-">
                        @error('password')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="role_id" class="form-label">Peran</label>
                        <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" data-placeholder="-pilih peran pengguna-" style="width: 100%;">
                          <option selected disabled>-pilih peran pengguna-</option>
                          <option value="1">Administrator</option>
                          <option value="3">Guru</option>
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">
                          {{ $message }}
                        </div>
                        @enderror
                      </div>
                      <x-adminlte-button type="submit" class="btn bg-gradient-green col-12 simpan" icon="fas fa fa-fw fa-save" label="Simpan Data"/>
                      {{-- <x-adminlte-button id="simpan" class="btn bg-purple col-12 simpan" type="submit" label="Simpan Data"
                      icon="fas fa fa-fw fa-save" hidden /> --}}
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- /tab tambah --}}
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal_update_user" tabindex="-1" role="dialog" aria-labelledby="updateModal"
  aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <form id="form_update_user">
          
          <div class="row">
            <div class="col-sm-12">
              <x-adminlte-input name="update_id" label="ID User" placeholder="ID"
              fgroup-class="col-md-12" disabled />
              <x-adminlte-input name="update_name" label="Nama" placeholder="Contoh : Ivan"
              fgroup-class="col-md-12" />
              
              <x-adminlte-textarea name="update_user_name" label="Username" rows=5 igroup-size="sm"
              placeholder="Masukan username..." fgroup-class="col-md-12">
              <x-slot name="prependSlot">
                <div class="input-group-text bg-purple">
                  <i class="fas fa-lg fa-location-dot text-light"></i>
                </div>
              </x-slot>
            </x-adminlte-textarea>
            
            <x-adminlte-input name="update_peran" label="Peran" placeholder="083xxxxxxx"
            fgroup-class="col-md-12" />
            
          </div>
          
        </div>
        <div class="row d-grid gap-2">
          <div class="col-md-6 d-grid gap-2">
            <x-adminlte-button class="btn col-12 bg-purple rounded-0" name="update_user"
            type="submit" label="Simpan Data" theme="primary" icon="fas fa-fw fa-sm fa-save" />
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
@stop
@section('head_js')
@push('head')
<!-- jQuery -->
<script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<!-- Select2 -->
<script type="text/javascript" src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script type="text/javascript" src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script type="text/javascript" src="{{ asset('vendor/pdfmake/vfs_fonts.js') }}"></script>
<script type="text/javascript" src="vendor/pdfmake/vfs_fonts.js"></script>
<!-- AdminLTE App -->
<script src="vendor/adminlte/dist/js/adminlte.min.js"></script>

@endpush
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
    $('#form_tambah_user').reset();
    $('#form_tambah_user').find('.is-invalid').removeClass('is-invalid');
    $('#form_tambah_user').find('.error').remove();
  }
</script>
<script>
  $(document).ready(function () {
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
        url: "{{ route('user.getTable') }}",
        type: 'GET',
      },
      columns: [
      {
        data: 'id',
        name: 'id',
        sClass: 'text-center',
        width: '5%'
      },
      {
        data: 'name',
        name: 'name'
      },
      {
        data: 'user_name',
        name: 'user_name'
      },
      {
        data: 'role',
        name: 'role'
      },
      // only show this column if user is admin
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

<script>
  //ajax tambah user
  $(document).ready(function() {
    $('.select2').select2();
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
    $('#controller-tab-user-add').on('click', function() {
      $('#form_tambah_user')[0].reset();
    });
    $('#form_tambah_user').on('submit', function(e) {
      e.preventDefault();
      let name = $('#name').val();
      let email = $('#email').val();
      let user_name = $('#user_name').val();
      let password = $('#password').val();
      let role_id = $('#role_id').val();
      // Ubah role_id menjadi array jika tidak sudah menjadi array
      // if (!Array.isArray(role_id)) {
        //   role_id = [role_id];
        // }
        
        $.ajax({
          type: "POST",
          url: "{{ route('dataUser.store') }}",
          data: {
            name: name,
            email: email,
            user_name: user_name,
            password: password,
            role_id: role_id,
            // role_id: JSON.stringify(role_id), // Mengubah array menjadi string JSON
          },
          dataType: "JSON",
          success: function(response) {
            // if (response.success) {
              $('#example1').DataTable().ajax.reload();
              $('#form_tambah_user')[0].reset();
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
                $('#form_tambah_user').find(".is-invalid").removeClass(
                "is-invalid");
                $('#form_tambah_user').find('.error').remove();
                
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
            text: "Data yang dihapus tak dapat dikembalikan!",
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
                url: "{{ route('dataUser.index') }}" + "/" + id,
                data: {
                  id: id
                },
                dataType: "JSON",
                success: function(response) {
                  if (response.success != null) {
                    $('#example1').DataTable().ajax.reload();
                    Swal.fire({
                      title: 'Berhasil!',
                      text: response.success,
                      icon: 'success',
                      iconColor: '#fff',
                      color: '#fff',
                      toast: true,
                      background: '#8D72E1',
                      position: 'top',
                      showConfirmButton: false,
                      timer: 3000,
                      timerProgressBar: true,
                    });
                  } else {
                    Swal.fire({
                      title: 'Gagal!',
                      text: response.error,
                      icon: 'error',
                      iconColor: '#fff',
                      toast: true,
                      background: '#f8bb86',
                      position: 'center-end',
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
      
      {{-- <script>
        //populate update form by ajax
        $(document).on('click', '.edit', function() {
          let id = $(this).attr('data-id');
          $.ajax({
            url: "{{ route('dataUser.edit') }}/" + id + "/edit",
            dataType: "json",
            success: function(data) {
              $('#update_id').val(data.user.id);
              $('#update_name').val(data.user.name);
              $('#update_user_name').val(data.user.user_name);
              $('#update_peran').val(data.userRole.role);
            }
          })
        });
      </script> --}}
      
      @stop
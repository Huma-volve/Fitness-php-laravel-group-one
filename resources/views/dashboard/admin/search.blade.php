@extends('master')

@section('title', 'All Bookings')

@section("content")
    
    <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Search</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="#">Dashboard</a></li>
                                    <li class="active">Search</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">

                            <div class="card-header">
                                <strong class="card-title">Search</strong>
                            </div>

                            <div class="card-body">
                                
                                    <div class="row">

                                        <div class="col-md-3">
                                            <input type="text" name="search" id="search_text" class="form-control"
                                                   placeholder="trainers, users, bookings, and payments "
                                                   value="{{ request('search') }}">
                                        </div>

                                        
                                        

                                       
                               

                                <table id="bootstrap-data-table" class="table table-striped table-bordered" style="margin-top: 10px;">
                                   
                                   

                                </table>

                                

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


@endsection

@section("js")

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#search_text').on('input', function() {
    var search_text = $(this).val();
     
    
     
    
    $.ajax({
        url: '/search/search_text',
        type: 'GET',
        data: { search_text: search_text },
        success: function(data) {
          var html = '';

         
               html += `
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
            `;
               $.each(data, function (index, user) {
               if(user.name){
                    html += `
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>
                              <a href="/search/search_text/user_info" class="btn btn-cuccess"> view</a>
                        </td>
                    </tr>
                `;
               }     
                
            });
          html += `</tbody>`;



         


                        
               

               

            
            $('#bootstrap-data-table').html(html);

            
            
       
     },
        
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
});
</script>

@endsection
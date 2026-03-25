@extends('master')

@section('title', 'trainers')

@section("content")

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong class="card-title">trainer Table</strong>
                
            </div>
              @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('delete'))
                    <div class="alert alert-success">{{ session('delete') }}</div>
                    @endif
                    @if(session('update'))
                    <div class="alert alert-success">{{ session('update') }}</div>
                    @endif
            <a href="{{ route('trainees.create') }}" 
   class="btn btn-primary btn-sm">
    Add trainer
</a>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>name</th>
                            <th>image</th>
                            <th>email</th>
                            <th>phone</th>
                            <th>status</th>
                            <th>opration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainers as $trainer )
                            
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                        <td>{{ $trainer->name }}</td>
 <td>
    @if(!empty($trainer->profile_image))
    <img src="{{ asset('storage/' . $trainer->profile_image) }}"
         width="100" height="100"
         class="rounded-circle border">
    @else

 <img src="{{ asset('dashboard/images/admin.jpg') }}" 
             width="100" height="100" 
             class="rounded-circle border">
    @endif

</td>
                        <td>{{ $trainer->email }}</td>
                        <td>{{ $trainer->phone }}</td>
    <td>
        @if($trainer->status == 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-danger">Inactive</span>
        @endif
    </td>
                        <td>

                            	<a  class="modal-effect btn btn-sm btn-info"
												 href="{{route('showdetails',$trainer->id)}}"
                                                       title="eye"><i class="fa fa-eye"></i></i>
													 </a>
                                                         <button type="button" class="btn btn-danger mb-1" data-toggle="modal" data-target="#deleteModal{{ $trainer->id }}">
                          <i class="fa fa-trash-o"></i>
                      </button>
                      <a href="{{ route('trainers.edit', $trainer->id) }}" class="btn btn-sm btn-warning">
    <i class="fa fa-edit"></i>
</a>
                        </td>
                        </tr>
                        
                        
                        @endforeach
                    </tbody>
                </table>
                @foreach ($trainers as $trainer)
                    
              
                   
                    <div class="modal fade"  id="deleteModal{{ $trainer->id }}" tabindex="-1" role="dialog" aria-labelledby="staticModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticModalLabel">Static Modal</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        Do you sure delete the trainerr 
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                      <form action="{{ route('deletetrainer', $trainer->id) }}" method="post">
                                         @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection


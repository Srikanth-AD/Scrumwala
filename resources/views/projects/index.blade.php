@extends('app')
@section('content')
    @if(count($projects) > 0)
        <div class="fluid-container">
            <h3>Projects</h3>
            <hr/>
            <div class="row fluid-container main-content">
                <div class="fluid-container col-sm-10">
                    @foreach(array_chunk($projects->all(),3) as $row)
                        <div class="row">
                            @foreach($row as $project)
                                <div class="col-sm-4">
                                    @include('projects.list.project-card')
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @else
                <h2>No projects found</h2>
                <h3><a href="/projects/create">Create your first project</a></h3> 
        </div>
    @endif
@endsection
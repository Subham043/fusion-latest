@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            Access Level
        </h1>
	@if ($editMode == true)
        <div class="pull-right">
            <a href="{{ route('access_level.index') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>
	@endif
        <div class="clearfix"></div>
    </section>
   


    <section class="content">

        @include('layouts._alerts')

	@if ($editMode == true)
        	{!! Form::model($itemLookup, ['route' => ['access_level.update', $itemLookup->id]]) !!}
    	@else
        	{!! Form::open(['route' => 'access_level.store']) !!}
    	@endif

	<div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body row">
	
			<div class="col-lg-12">
                        <div class="form-group">
                            <label class="">Access Level Name: </label>
                            {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control']) !!}
                        </div>
			<div class="form-group">
			   <label class="">Access Permission: </label></br>
			   @foreach($accessList as $key=>$value)
				@if ($editMode == true)
				<label for="access_{{$key}}" style="margin-right:10px"><input type="checkbox" id="access_{{$key}}" class="" value="{{$key}}" name="access[]"
				@if(in_array($key, json_decode($itemLookup->access))) checked @endif
				/> {{$value}}</label>
				@else
				<label for="access_{{$key}}" style="margin-right:10px"><input type="checkbox" id="access_{{$key}}" class="" value="{{$key}}" name="access[]" /> {{$value}}</label>
				@endif
			   @endforeach
			</div>

			<div class="pull-right">
            			<button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
        		</div>
			</div>


                    </div>

                </div>

            </div>

        </div>

	{!! Form::close() !!}

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
				<th>Access Permission</th>
                                <th>{{ trans('fi.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($itemLookups as $itemLookup)
                                <tr>
                                    <td>{{ $itemLookup->name }}</td>
				    <td>
					@foreach(json_decode($itemLookup->access) as $access)
					{{ $accessList[$access] }}, 
					@endforeach
				     </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{ route('access_level.edit', [$itemLookup->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                               <!-- <li><a href="{{ route('access_level.delete', [$itemLookup->id]) }}" onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li> -->
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="pull-right">
                    {!! $itemLookups->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop

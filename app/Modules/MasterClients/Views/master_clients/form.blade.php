@extends('layouts.master')

@section('content')

    @if ($editMode)
        {!! Form::model($client, ['route' => ['master_clients.update', $client->id]]) !!}
    @else
        {!! Form::open(['route' => 'master_clients.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">Master {{ trans('fi.client_form') }}</h1>

        <div class="pull-right">
            @if ($editMode)
                <a href="{{ $returnUrl }}" class="btn btn-default"><i class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
            @endif
            <button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab">{{ trans('fi.general') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            @include('master_clients._form')
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}

@stop
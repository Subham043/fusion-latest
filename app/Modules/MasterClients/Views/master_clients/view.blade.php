@extends('layouts.master')

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            Master {{ trans('fi.view_client') }}
        </h1>
        <div class="pull-right">
            <a href="{{ route('master_clients.edit', [$client->id]) }}" class="btn btn-default">{{ trans('fi.edit') }}</a>
           <!-- <a class="btn btn-default" href="{{ route('clients.delete', [$client->id]) }}" onclick="return confirm('{{ trans('fi.delete_client_warning') }}');"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a> -->
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-details">{{ trans('fi.details') }}</a></li>
                    </ul>
                    <div class="tab-content">

                        <div id="tab-details" class="tab-pane active">

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="pull-left">
                                        <h2>{!! $client->name !!}</h2>
                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <table class="table table-striped">
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.email') }}</td>
                                            <td class="col-md-10"><a href="mailto:{!! $client->email !!}">{!! $client->email !!}</a></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2">{{ trans('fi.mobile') }}</td>
                                            <td class="col-md-10">{!! $client->mobile !!}</td>
                                        </tr>
                                    </table>

                                </div>

                            </div>

                        </div>

                        
                    </div>
                </div>

            </div>

        </div>

    </section>

@stop

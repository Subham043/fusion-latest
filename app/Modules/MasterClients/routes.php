<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'master_clients', 'namespace' => 'FI\Modules\MasterClients\Controllers'], function ()
{
    Route::get('/', ['uses' => 'MasterClientController@index', 'as' => 'master_clients.index']);
    Route::get('create', ['uses' => 'MasterClientController@create', 'as' => 'master_clients.create']);
    Route::get('{id}/edit', ['uses' => 'MasterClientController@edit', 'as' => 'master_clients.edit']);
    Route::get('{id}', ['uses' => 'MasterClientController@show', 'as' => 'master_clients.show']);
    Route::get('{id}/delete', ['uses' => 'MasterClientController@delete', 'as' => 'master_clients.delete']);

    Route::post('create', ['uses' => 'MasterClientController@store', 'as' => 'master_clients.store']);
    Route::post('{id}/edit', ['uses' => 'MasterClientController@update', 'as' => 'master_clients.update']);


});
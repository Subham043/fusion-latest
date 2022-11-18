<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\AccessLevel\Controllers'], function ()
{
    Route::get('access-level', ['uses' => 'AccessLevelController@index', 'as' => 'access_level.index']);
    Route::get('access-level/create', ['uses' => 'AccessLevelController@create', 'as' => 'access_level.create']);
    Route::get('access-level/{itemLookup}/edit', ['uses' => 'AccessLevelController@edit', 'as' => 'access_level.edit']);
    Route::get('access-level/{itemLookup}/delete', ['uses' => 'AccessLevelController@delete', 'as' => 'access_level.delete']);
    Route::get('access-level/ajax/inventory_lookup', ['uses' => 'AccessLevelController@ajaxInventoryLookup', 'as' => 'access_level.ajax.inventoryLookup']);

    Route::post('access-level', ['uses' => 'AccessLevelController@store', 'as' => 'access_level.store']);
    Route::post('access-level/{itemLookup}', ['uses' => 'AccessLevelController@update', 'as' => 'access_level.update']);
    Route::post('access-level/ajax/process', ['uses' => 'AccessLevelController@process', 'as' => 'access_level.ajax.process']);
});
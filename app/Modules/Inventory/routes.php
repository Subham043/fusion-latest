<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Inventory\Controllers'], function ()
{
    Route::get('inventory', ['uses' => 'InventoryController@index', 'as' => 'inventory.index']);
    Route::get('inventory/create', ['uses' => 'InventoryController@create', 'as' => 'inventory.create']);
    Route::get('inventory/{itemLookup}/edit', ['uses' => 'InventoryController@edit', 'as' => 'inventory.edit']);
    Route::get('inventory/{itemLookup}/delete', ['uses' => 'InventoryController@delete', 'as' => 'inventory.delete']);
    Route::get('inventory/ajax/inventory_lookup', ['uses' => 'InventoryController@ajaxInventoryLookup', 'as' => 'inventory.ajax.inventoryLookup']);

    Route::post('inventory', ['uses' => 'InventoryController@store', 'as' => 'inventory.store']);
    Route::post('inventory/{itemLookup}', ['uses' => 'InventoryController@update', 'as' => 'inventory.update']);
    Route::post('inventory/ajax/process', ['uses' => 'InventoryController@process', 'as' => 'inventory.ajax.process']);
});
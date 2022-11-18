<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Inventory\Models;

use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use File;


class InventoryImage extends Model
{
    use Sortable;

    /**
     * Guarded properties
     * @var array
     */
     
    protected $table = 'item_lookup_images';
    
    protected $guarded = ['id'];

	public static function boot()
    {
        parent::boot();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    public function inventory()
    {
        return $this->belongsTo('FI\Modules\Inventory\Models\Inventory', 'item_lookup_id');
    }

  }
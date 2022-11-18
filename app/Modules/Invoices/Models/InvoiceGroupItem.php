<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Models;

use FI\Events\InvoiceItemSaving;
use FI\Events\InvoiceModified;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;
use FI\Modules\InventoryGroupList\Models\InventoryGroupList;
use Illuminate\Support\Facades\DB;

class InvoiceGroupItem extends Model
{
    protected $guarded = ['id', 'item_group_id'];
	protected $table = 'invoice_group_lists';

    public static function boot()
    {
        parent::boot();

	static::saved(function($invoiceGroupItem)
        {
		$invoiceAmount = 	$invoiceGroupItem->invoice->amount;
		$invoiceAmount->subtotal = $invoiceAmount->subtotal+$invoiceGroupItem->total;
		$invoiceAmount->total = $invoiceAmount->total+$invoiceGroupItem->total;
		$invoiceAmount->balance = $invoiceAmount->balance+$invoiceGroupItem->total;
		$invoiceAmount->save();

        });

	static::deleted(function($invoiceGroupItem)
        {
            if ($invoiceGroupItem->invoice)
            {
                $invoiceAmount = 	$invoiceGroupItem->invoice->amount;
		$invoiceAmount->subtotal = $invoiceAmount->subtotal-$invoiceGroupItem->total;
		$invoiceAmount->total = $invoiceAmount->total-$invoiceGroupItem->total;
		$invoiceAmount->balance = $invoiceAmount->balance-$invoiceGroupItem->total;
		$invoiceAmount->save();
            }
        });

     }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */    

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }
    
    public function inventorygrouplist()
    {
        return $this->belongsTo('FI\Modules\InventoryGroupList\Models\InventoryGroupList', 'inventory_group_list_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedQuantityAttribute()
    {
        //return NumberFormatter::format($this->attributes['quantity'],null,0.0);
        return NumberFormatter::format2($this->attributes['quantity'],null,0.0);
    }


    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->attributes['description']);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereIn('invoice_id', function ($query) use ($from, $to)
        {
            $query->select('id')
                ->from('invoices')
                ->where('invoice_date', '>=', $from)
                ->where('invoice_date', '<=', $to);
        });
    }
    
}

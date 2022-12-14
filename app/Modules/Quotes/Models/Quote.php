<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Models;

use Carbon\Carbon;
use FI\Events\QuoteCreated;
use FI\Events\QuoteCreating;
use FI\Events\QuoteDeleted;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\FileNames;
use FI\Support\HTML;
use FI\Support\NumberFormatter;
use FI\Support\Statuses\QuoteStatuses;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Inventory\Models\Inventory;
use Illuminate\Support\Facades\Storage;
use File;


class Quote extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = [
        'number' => ['LENGTH(number)', 'number'],
        'quote_date',
        'expires_at',
        'event_date',
        'clients.name',
        'summary',
        'quote_amounts.total',
        'quote_amounts.tax',
        'quote_amounts.subtotal',
    ];

    protected $dates = ['expires_at', 'quote_date', 'event_date'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($quote)
        {
            event(new QuoteCreating($quote));
        });

        static::created(function ($quote)
        {
            event(new QuoteCreated($quote));
	
	    $url = "CHK-".$quote->id;

	    $barcode = new \FI\Modules\Inventory\Barcode\Barcode();
	    $bobj = $barcode->getBarcodeObj('C128', $url, 0, -30, 'black', array(0, 0, 0, 0));
  	    Storage::put('public/barcode/quote-item-checklist-'.$quote->id.'-barcode.png', $bobj->getPngData());
	    File::move(storage_path('app/public/barcode/quote-item-checklist-'.$quote->id.'-barcode.png'), public_path('assets/barcode/quote-item-checklist-'.$quote->id.'-barcode.png'));
        });

        static::deleted(function ($quote)
        {
            event(new QuoteDeleted($quote));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function activities()
    {
        return $this->morphMany('FI\Modules\Activity\Models\Activity', 'audit');
    }

    public function amount()
    {
        return $this->hasOne('FI\Modules\Quotes\Models\QuoteAmount');
    }

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function clientAttachments()
    {
        $relationship = $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');

        $relationship->where('client_visibility', 1);

        return $relationship;
    }

    public function companyProfile()
    {
        return $this->belongsTo('FI\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function currency()
    {
        return $this->belongsTo('FI\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\QuoteCustom');
    }

    public function group()
    {
        return $this->hasOne('FI\Modules\Groups\Models\Group');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    public function mailQueue()
    {
        return $this->morphMany('FI\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function items()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\QuoteItem')
            ->orderBy('display_order');
    }

    public function groupitems()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\QuoteGroupItem')
            ->orderBy('display_order');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    // This and items() are the exact same. This is added to appease the IDE gods
    // and the fact that Laravel has a protected items property.
    public function quoteItems()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\QuoteItem')
            ->orderBy('display_order');
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('quotes/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return ['0' => trans('fi.not_visible'), '1' => trans('fi.visible')];
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->formatted_quote_date;
    }

    public function getFormattedQuoteDateAttribute()
    {
        return DateFormatter::format($this->attributes['quote_date']);
    }
    
    public function getFormattedEventDateAttribute()
    {
        return DateFormatter::format($this->attributes['event_date']);
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['updated_at']);
    }

    public function getFormattedExpiresAtAttribute()
    {
        return DateFormatter::format($this->attributes['expires_at']);
    }

    public function getFormattedTermsAttribute()
    {
        return nl2br($this->attributes['terms']);
    }

    public function getFormattedFooterAttribute()
    {
        return nl2br($this->attributes['footer']);
    }

    public function getStatusTextAttribute()
    {
        $statuses = QuoteStatuses::statuses();

        return $statuses[$this->attributes['quote_status_id']];
    }

    public function getPdfFilenameAttribute()
    {
        return FileNames::quote($this);
    }

    public function getPublicUrlAttribute()
    {
        return route('clientCenter.public.quote.show', [$this->url_key]);
    }

    public function getIsForeignCurrencyAttribute()
    {
        if ($this->attributes['currency_code'] == config('fi.baseCurrency'))
        {
            return false;
        }

        return true;
    }

    public function getHtmlAttribute()
    {
        return HTML::quote($this);
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount']);
    }

    /**
     * Gathers a summary of both invoice and item taxes to be displayed on invoice.
     *
     * @return array
     */
    public function getSummarizedTaxesAttribute()
    {
        $taxes = [];

        foreach ($this->items as $item)
        {
            if ($item->taxRate)
            {
                $key = $item->taxRate->name;

                if (!isset($taxes[$key]))
                {
                    $taxes[$key]              = new \stdClass();
                    $taxes[$key]->name        = $item->taxRate->name;
                    $taxes[$key]->percent     = $item->taxRate->formatted_percent;
                    $taxes[$key]->total       = $item->amount->tax_1;
                    $taxes[$key]->raw_percent = $item->taxRate->percent;
                }
                else
                {
                    $taxes[$key]->total += $item->amount->tax_1;
                }
            }

            if ($item->taxRate2)
            {
                $key = $item->taxRate2->name;

                if (!isset($taxes[$key]))
                {
                    $taxes[$key]              = new \stdClass();
                    $taxes[$key]->name        = $item->taxRate2->name;
                    $taxes[$key]->percent     = $item->taxRate2->formatted_percent;
                    $taxes[$key]->total       = $item->amount->tax_2;
                    $taxes[$key]->raw_percent = $item->taxRate2->percent;
                }
                else
                {
                    $taxes[$key]->total += $item->amount->tax_2;
                }
            }
        }

        foreach ($taxes as $key => $tax)
        {
            $taxes[$key]->total = CurrencyFormatter::format($tax->total, $this->currency);
        }

        return $taxes;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeClientId($query, $clientId = null)
    {
        if ($clientId)
        {
            $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId)
        {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('draft'));
    }

    public function scopeSent($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('sent'));
    }

    public function scopeApproved($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('approved'));
    }

    public function scopeRejected($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('rejected'));
    }

    public function scopeCanceled($query)
    {
        return $query->where('quote_status_id', '=', QuoteStatuses::getStatusId('canceled'));
    }

    public function scopeStatus($query, $status = null)
    {
        switch ($status)
        {
            case 'draft':
                $query->draft();
                break;
            case 'sent':
                $query->sent();
                break;
            case 'viewed':
                $query->viewed();
                break;
            case 'approved':
                $query->approved();
                break;
            case 'rejected':
                $query->rejected();
                break;
            case 'canceled':
                $query->canceled();
                break;
        }

        return $query;
    }

    public function scopeYearToDate($query)
    {
        return $query->where('quote_date', '>=', date('Y') . '-01-01')
            ->where('quote_date', '<=', date('Y') . '-12-31');
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('quote_date', '>=', Carbon::now()->firstOfQuarter())
            ->where('quote_date', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->where('quote_date', '>=', $fromDate)
            ->where('quote_date', '<=', $toDate);
    }

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords)
        {

            $keywords = strtolower($keywords);
	    $num = substr($keywords, (strpos($keywords, '-') ?: -1) + 1);
            $query->where(DB::raw('lower(number)'), 'like', '%' . $keywords . '%');
                
            if(preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $keywords) === 1) {
                echo DateFormatter::unformat($keywords);
                $query->orWhere('quotes.quote_date', 'like', '%' . DateFormatter::unformat($keywords) . '%');
                $query->orWhere('quotes.event_date', 'like', '%' . DateFormatter::unformat($keywords) . '%');
                $query->orWhere('expires_at', 'like', '%' . DateFormatter::unformat($keywords) . '%');
            }
            
            $query->orWhere('summary', 'like', '%' . $keywords . '%')->orWhere('quotes.id', 'LIKE', "%$num%");
            $query->orWhereIn('client_id', function ($query) use ($keywords)
            {
                $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name),LOWER(unique_name))"), 'like', '%' . $keywords . '%');
            });
        }

        return $query;
    }
    
    public function getAllocatedAndAvailableQuantity($item)
    {
        $inventory = Inventory::where('name',$item)->first();

        $query = DB::table('invoices')
            ->select('invoice_items.quantity','invoice_items.name', DB::raw("sum(invoice_items.quantity) as sum"))
            ->join('quotes', 'quotes.invoice_id', '=', 'invoices.id')
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('quotes.event_date',$this->event_date)
            ->where('invoice_items.name',$item)
            ->where('invoices.id','<>',$this->invoice_id)
            ->whereIn('invoices.invoice_status_id', array(3,4))
            ->get();
        if($query[0]->sum==null){
            return array('allocated'=>0,'available'=>$inventory->total);
        }
        return array('allocated'=>$query[0]->sum,'available'=>(int)$inventory->total-(int)$query[0]->sum);
    }
    
    public function getReservedQuantity($item)
    {
        $inventory = Inventory::where('name',$item)->first();

        $query = DB::table('invoices')
            ->select('invoice_items.quantity','invoice_items.name', DB::raw("sum(invoice_items.quantity) as sum"))
            ->join('quotes', 'quotes.invoice_id', '=', 'invoices.id')
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('quotes.event_date',$this->event_date)
            ->where('invoice_items.name',$item)
            ->where('invoices.id','<>',$this->invoice_id)
            ->where('invoices.invoice_status_id', 1)
            ->orWhere('invoices.invoice_status_id', 2)
            ->orWhere('quotes.quote_status_id', 2)
            ->orWhere('quotes.quote_status_id', 3)
            ->get();
        if($query[0]->sum==null){
            return array('reserved'=>0);
        }
        return array('reserved'=>$query[0]->sum);
    }
}
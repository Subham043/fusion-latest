<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Inventory\Requests;

use FI\Support\NumberFormatter;
use Illuminate\Foundation\Http\FormRequest;

class InventoryImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'image'  => 'Image',
        ];
    }


    public function rules()
    {
        return [
            'image'  => 'required|array|max:5|min:1',
		'image.*'  => 'required|image|mimes:jpeg,png,jpg,webp',
        ];
    }
}
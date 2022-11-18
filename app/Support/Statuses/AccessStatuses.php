<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\Statuses;

class AccessStatuses extends AbstractStatuses
{
    protected static $statuses = [
        '0' => 'all_statuses',
        '1' => 'Clients',
        '2' => 'Quotes',
        '3' => 'Invoices',
        '4' => 'Load In Load Out Calendar',
        '5' => 'Event Calendar',
	'6' => 'Payments',
	'7' => 'Inventory',
	'8' => 'Barcode Printer',
	'9' => 'Expenses',
	'10' => 'Reports',
	'11' => 'Schedule',
	'12' => 'Settings',
    ];
}
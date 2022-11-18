<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Reports\Reports\QuotesReport;
use FI\Modules\Reports\Requests\SalesRequest;
use FI\Support\PDF\PDFFactory;
use FI\Modules\Exports\Support\Export;

class QuotesReportController extends Controller
{
    private $report;

    public function __construct(QuotesReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.quotes');
    }

    public function validateOptions(SalesRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );
        return view('reports.output.quotes')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        $html = view('reports.output.quotes')
            ->with('results', $results)->render();

        $pdf->download($html, 'quotes_report' . '.pdf');
    }

    public function csv()
    {
	$export = new Export('QuotesReport', 'CsvWriter');

        $export->writeFile();

        return response()->download($export->getDownloadPath());

    }
}
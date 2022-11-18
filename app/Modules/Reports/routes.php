<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'report', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Reports\Controllers'], function ()
{
    Route::get('client_statement', ['uses' => 'ClientStatementReportController@index', 'as' => 'reports.clientStatement']);
    Route::post('client_statement/validate', ['uses' => 'ClientStatementReportController@validateOptions', 'as' => 'reports.clientStatement.validate']);
    Route::get('client_statement/html', ['uses' => 'ClientStatementReportController@html', 'as' => 'reports.clientStatement.html']);
    Route::get('client_statement/pdf', ['uses' => 'ClientStatementReportController@pdf', 'as' => 'reports.clientStatement.pdf']);
    
    Route::get('event_statement', ['uses' => 'EventStatementReportController@index', 'as' => 'reports.eventStatement']);
    Route::post('event_statement/validate', ['uses' => 'EventStatementReportController@validateOptions', 'as' => 'reports.eventStatement.validate']);
    Route::get('event_statement/html', ['uses' => 'EventStatementReportController@html', 'as' => 'reports.eventStatement.html']);
    Route::get('event_statement/pdf', ['uses' => 'EventStatementReportController@pdf', 'as' => 'reports.eventStatement.pdf']);

    Route::get('item_sales', ['uses' => 'ItemSalesReportController@index', 'as' => 'reports.itemSales']);
    Route::post('item_sales/validate', ['uses' => 'ItemSalesReportController@validateOptions', 'as' => 'reports.itemSales.validate']);
    Route::get('item_sales/html', ['uses' => 'ItemSalesReportController@html', 'as' => 'reports.itemSales.html']);
    Route::get('item_sales/pdf', ['uses' => 'ItemSalesReportController@pdf', 'as' => 'reports.itemSales.pdf']);

    Route::get('payments_collected', ['uses' => 'PaymentsCollectedReportController@index', 'as' => 'reports.paymentsCollected']);
    Route::post('payments_collected/validate', ['uses' => 'PaymentsCollectedReportController@validateOptions', 'as' => 'reports.paymentsCollected.validate']);
    Route::get('payments_collected/html', ['uses' => 'PaymentsCollectedReportController@html', 'as' => 'reports.paymentsCollected.html']);
    Route::get('payments_collected/pdf', ['uses' => 'PaymentsCollectedReportController@pdf', 'as' => 'reports.paymentsCollected.pdf']);

    Route::get('revenue_by_client', ['uses' => 'RevenueByClientReportController@index', 'as' => 'reports.revenueByClient']);
    Route::post('revenue_by_client/validate', ['uses' => 'RevenueByClientReportController@validateOptions', 'as' => 'reports.revenueByClient.validate']);
    Route::get('revenue_by_client/html', ['uses' => 'RevenueByClientReportController@html', 'as' => 'reports.revenueByClient.html']);
    Route::get('revenue_by_client/pdf', ['uses' => 'RevenueByClientReportController@pdf', 'as' => 'reports.revenueByClient.pdf']);

    Route::get('tax_summary', ['uses' => 'TaxSummaryReportController@index', 'as' => 'reports.taxSummary']);
    Route::post('tax_summary/validate', ['uses' => 'TaxSummaryReportController@validateOptions', 'as' => 'reports.taxSummary.validate']);
    Route::get('tax_summary/html', ['uses' => 'TaxSummaryReportController@html', 'as' => 'reports.taxSummary.html']);
    Route::get('tax_summary/pdf', ['uses' => 'TaxSummaryReportController@pdf', 'as' => 'reports.taxSummary.pdf']);

    Route::get('profit_loss', ['uses' => 'ProfitLossReportController@index', 'as' => 'reports.profitLoss']);
    Route::post('profit_loss/validate', ['uses' => 'ProfitLossReportController@validateOptions', 'as' => 'reports.profitLoss.validate']);
    Route::get('profit_loss/html', ['uses' => 'ProfitLossReportController@html', 'as' => 'reports.profitLoss.html']);
    Route::get('profit_loss/pdf', ['uses' => 'ProfitLossReportController@pdf', 'as' => 'reports.profitLoss.pdf']);

    Route::get('expense_list', ['uses' => 'ExpenseListReportController@index', 'as' => 'reports.expenseList']);
    Route::post('expense_list/validate', ['uses' => 'ExpenseListReportController@validateOptions', 'as' => 'reports.expenseList.validate']);
    Route::get('expense_list/html', ['uses' => 'ExpenseListReportController@html', 'as' => 'reports.expenseList.html']);
    Route::get('expense_list/pdf', ['uses' => 'ExpenseListReportController@pdf', 'as' => 'reports.expenseList.pdf']);

    Route::get('sales_report', ['uses' => 'SalesReportController@index', 'as' => 'reports.sales']);
    Route::post('sales_report/validate', ['uses' => 'SalesReportController@validateOptions', 'as' => 'reports.sales.validate']);
    Route::get('sales_report/html', ['uses' => 'SalesReportController@html', 'as' => 'reports.sales.html']);
    Route::get('sales_report/pdf', ['uses' => 'SalesReportController@pdf', 'as' => 'reports.sales.pdf']);
    Route::get('sales_report/csv', ['uses' => 'SalesReportController@csv', 'as' => 'reports.sales.csv']);

    Route::get('quotes_report', ['uses' => 'QuotesReportController@index', 'as' => 'reports.quotes']);
    Route::post('quotes_report/validate', ['uses' => 'QuotesReportController@validateOptions', 'as' => 'reports.quotes.validate']);
    Route::get('quotes_report/html', ['uses' => 'QuotesReportController@html', 'as' => 'reports.quotes.html']);
    Route::get('quotes_report/pdf', ['uses' => 'QuotesReportController@pdf', 'as' => 'reports.quotes.pdf']);
    Route::get('quotes_report/csv', ['uses' => 'QuotesReportController@csv', 'as' => 'reports.quotes.csv']);

    Route::get('paid_report', ['uses' => 'PaidReportController@index', 'as' => 'reports.paid']);
    Route::post('paid_report/validate', ['uses' => 'PaidReportController@validateOptions', 'as' => 'reports.paid.validate']);
    Route::get('paid_report/html', ['uses' => 'PaidReportController@html', 'as' => 'reports.paid.html']);
    Route::get('paid_report/pdf', ['uses' => 'PaidReportController@pdf', 'as' => 'reports.paid.pdf']);
    Route::get('paid_report/csv', ['uses' => 'PaidReportController@csv', 'as' => 'reports.paid.csv']);

    Route::get('aging_report', ['uses' => 'AgingReportController@index', 'as' => 'reports.aging']);
    Route::post('aging_report/validate', ['uses' => 'AgingReportController@validateOptions', 'as' => 'reports.aging.validate']);
    Route::get('aging_report/html', ['uses' => 'AgingReportController@html', 'as' => 'reports.aging.html']);
    Route::get('aging_report/pdf', ['uses' => 'AgingReportController@pdf', 'as' => 'reports.aging.pdf']);
    Route::get('aging_report/csv', ['uses' => 'AgingReportController@csv', 'as' => 'reports.aging.csv']);



});
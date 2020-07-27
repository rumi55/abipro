<?php

Auth::routes();

Route::group(['middleware'=>['auth', 'owner']],function(){
    Route::get('/company/register', 'CompanyController@register')->name('company.register');
    Route::post('/company', 'CompanyController@create')->name('company.create');
});

Route::group(['middleware'=>['auth', 'role', 'company']],function(){
    Route::get('/', 'HomeController@index')->name('home');
    //


    Route::get('/company/profile', 'CompanyController@profile')->name('company.profile');
    Route::get('/company/profile/edit', 'CompanyController@profileEdit')->name('company.profile.edit');
    Route::put('/company/profile', 'CompanyController@profileUpdate')->name('company.profile.update');

    Route::get('/companies', 'CompanyController@index')->name('companies.index');
    Route::put('/companies/{id}', 'CompanyController@setActive')->name('companies.active');
    Route::delete('/companies/{id}/delete', 'CompanyController@delete')->name('companies.delete');
    Route::get('/companies/{id}/delete', 'CompanyController@confirmDelete')->name('companies.delete.confirm');
    Route::get('/companies/transfer', 'CompanyController@transfer')->name('companies.transfer');
    Route::post('/companies/transfer', 'ImportDataController@transfer')->name('companies.transfer.save');
    Route::get('/companies/import', 'CompanyController@import')->name('companies.import');
    Route::post('/companies/import', 'ImportDataController@import')->name('companies.import.save');
    Route::get('/companies/export', 'CompanyController@export')->name('companies.export');
    Route::get('/companies/convert', 'CompanyController@convert')->name('companies.convert');
    Route::post('/companies/convert', 'CompanyController@convertUpload')->name('companies.convert.upload');
    Route::get('/companies/{name}/export', 'ExportDataController@excel')->name('companies.export.excel');

    //conversion
    Route::get('/conversion', 'ConvertAbiproController@index')->name('convert.index');
    Route::post('/conversion/upload/{name}', 'ConvertAbiproController@upload')->name('convert.upload');
    Route::post('/conversion/execute/{id}', 'ConvertAbiproController@execute')->name('convert.execute');
    Route::get('/conversion/departments', 'ConvertAbiproController@departmentConversion')->name('convert.departments');
    Route::get('/conversion/sortirs', 'ConvertAbiproController@sortirConversion')->name('convert.sortirs');
    Route::get('/conversion/chart_of_accounts', 'ConvertAbiproController@accountConversion')->name('convert.accounts');
    Route::get('/conversion/journals', 'ConvertAbiproController@journalConversion')->name('convert.journals');
    Route::get('/conversion/account_type_mapping', 'ConvertAbiproController@accountTypeMapping')->name('convert.account_type_mapping');
    Route::put('/conversion/account_type_mapping', 'ConvertAbiproController@accountTypeMappingSave')->name('convert.account_type_mapping.save');

    //departments
    Route::get('/departments', 'DepartmentController@index')->name('departments.index');
    Route::get('/departments/create', 'DepartmentController@create')->name('departments.create');
    Route::post('/departments', 'DepartmentController@save')->name('departments.create.save');
    Route::get('/departments/{id}/edit', 'DepartmentController@edit')->name('departments.edit');
    Route::put('/departments/{id}/update', 'DepartmentController@update')->name('departments.edit.update');
    Route::delete('/departments/{id}', 'DepartmentController@delete')->name('departments.delete');

    //contacts
    Route::get('/contacts', 'ContactController@index')->name('contacts.index');
    Route::get('/contacts/create', 'ContactController@create')->name('contacts.create');
    Route::post('/contacts', 'ContactController@save')->name('contacts.create.save');
    Route::get('/contacts/{id}/edit', 'ContactController@edit')->name('contacts.edit');
    Route::get('/contacts/{id}/duplicate', 'ContactController@duplicate')->name('contacts.create.duplicate');
    Route::get('/contacts/{id}', 'ContactController@view')->name('contacts.view');
    Route::put('/contacts/{id}/update', 'ContactController@update')->name('contacts.edit.update');
    Route::delete('/contacts/{id}', 'ContactController@delete')->name('contacts.delete');

    //products
    Route::get('/products/create', 'ProductController@create')->name('products.create');
    Route::post('/products', 'ProductController@save')->name('products.create.save');
    Route::get('/products/{id}/edit', 'ProductController@edit')->name('products.edit');
    Route::get('/products/{id}/duplicate', 'ProductController@duplicate')->name('products.create.duplicate');
    Route::put('/products/{id}/update', 'ProductController@update')->name('products.edit.update');
    Route::delete('/products/{id}', 'ProductController@delete')->name('products.delete');

    //tags
    Route::get('/sortirs', 'TagController@index')->name('tags.index');
    Route::get('/sortirs/create', 'TagController@create')->name('tags.create');
    Route::post('/sortirs', 'TagController@save')->name('tags.create.save');
    Route::get('/sortirs/{id}/duplicate', 'TagController@duplicate')->name('tags.create.duplicate');
    Route::get('/sortirs/{id}/edit', 'TagController@edit')->name('tags.edit');
    Route::put('/sortirs/{id}/update', 'TagController@update')->name('tags.edit.update');
    Route::delete('/sortirs/{id}', 'TagController@delete')->name('tags.delete');
    Route::get('/sortirs/groups', 'TagController@groups')->name('tags.groups');

    //reports
    Route::get('/reports', 'ReportController@index')->name('reports.index');
    Route::get('/reports/journals', 'JournalReportController@journal')->name('reports.journals');
    Route::get('/reports/vouchers', 'JournalReportController@voucher')->name('reports.vouchers');
    Route::get('/reports/ledgers', 'LedgerReportController@index')->name('reports.ledgers');
    Route::get('/reports/trial_balance', 'TrialBalanceReportController@index')->name('reports.trial_balance');
    Route::get('/reports/balance', 'BalanceReportController@index')->name('reports.balance');
    Route::get('/reports/profit', 'ProfitReportController@index')->name('reports.profit');
    Route::get('/reports/hpp', 'HPPReportController@index')->name('reports.hpp');
    Route::get('/reports/cashflow', 'CashflowReportController@index')->name('reports.cashflow');
    Route::get('/reports/sortir', 'SortirReportController@index')->name('reports.sortirs');

    //voucher
    Route::get('/vouchers', 'TransactionController@index')->name('vouchers.index');
    Route::get('/vouchers/create', 'JournalController@createVoucher')->name('vouchers.create');
    Route::get('/vouchers/create/{type}', 'TransactionController@create')->name('vouchers.create.single');
    Route::post('/vouchers/create/{type}', 'TransactionController@save')->name('vouchers.create.single.save');
    Route::post('/vouchers', 'JournalController@save')->name('vouchers.create.save');
    Route::post('/vouchers/{id}/approve', 'TransactionController@approve')->name('vouchers.approve');
    Route::post('/vouchers/{id}/submit', 'TransactionController@approve')->name('vouchers.create.submit');
    Route::get('/vouchers/{id}/edit', 'TransactionController@edit')->name('vouchers.edit');
    Route::get('/vouchers/{id}/duplicate', 'TransactionController@duplicate')->name('vouchers.create.duplicate');
    Route::get('/vouchers/{type}/{id}/edit', 'TransactionController@edit')->name('vouchers.edit.single');
    Route::put('/vouchers/{type}/{id}/edit', 'TransactionController@update')->name('vouchers.edit.single.update');
    Route::delete('/vouchers/{id}', 'TransactionController@delete')->name('vouchers.delete');
    Route::put('/vouchers/{id}', 'JournalController@update')->name('vouchers.edit.update');
    // Route::get('/vouchers/{id}/duplicate', 'JournalController@duplicate')->name('vouchers.create.duplicate');
    Route::get('/vouchers/{id}', 'TransactionController@view')->name('vouchers.view');
    Route::get('/vouchers/{id}/report', 'JournalController@report')->name('vouchers.report');
    Route::get('/vouchers/{id}/receipt', 'JournalController@receipt')->name('vouchers.receipt');
    Route::post('/vouchers/{id}/journal', 'JournalController@toJournal')->name('vouchers.tojournal');
    Route::post('/vouchers/journals', 'JournalController@toJournalBatch')->name('vouchers.tojournal.batch');

    //transactions
    // Route::get('/transactions/create', 'TransactionController@create')->name('transactions.create');
    // Route::post('/transactions', 'TransactionController@save')->name('transactions.create.save');
    // Route::get('/transactions/{id}/edit', 'TransactionController@edit')->name('transactions.edit');
    // Route::put('/transactions/{id}', 'TransactionController@update')->name('transactions.edit.update');
    // Route::get('/transactions/{id}/duplicate', 'TransactionController@duplicate')->name('transactions.create.duplicate');
    // Route::get('/transactions/{id}', 'TransactionController@view')->name('transactions.view');
    // Route::get('/transactions/{id}/report', 'TransactionController@report')->name('transactions.report');
    // Route::post('/transactions/{id}/lock', 'TransactionController@lockJournal')->name('transactions.lock');
    // Route::post('/transactions/{id}/voucher', 'TransactionController@toVoucher')->name('transactions.tovoucher');
    // Route::post('/transactions/vouchers', 'TransactionController@toVoucherBatch')->name('transactions.tovoucher.batch');

    //sales invoices
    Route::get('/sales_invoices/create', 'SalesInvoiceController@create')->name('sales_invoices.create');
    Route::post('/sales_invoices', 'SalesInvoiceController@save')->name('sales_invoices.create.save');
    Route::get('/sales_invoices/{id}/edit', 'SalesInvoiceController@edit')->name('sales_invoices.edit');
    Route::put('/sales_invoices/{id}', 'SalesInvoiceController@update')->name('sales_invoices.edit.update');
    Route::get('/sales_invoices/{id}/duplicate', 'SalesInvoiceController@duplicate')->name('sales_invoices.create.duplicate');
    Route::get('/sales_invoices/{order_id}/orders', 'SalesInvoiceController@createFromOrder')->name('sales_invoices.create.orders');
    Route::get('/sales_invoices/{quote_id}/quotes', 'SalesInvoiceController@createFromQuote')->name('sales_invoices.create.quotes');
    Route::get('/sales_invoices/{id}', 'SalesInvoiceController@view')->name('sales_invoices.view');
    Route::delete('/sales_invoices/{id}', 'SalesInvoiceController@delete')->name('sales_invoices.delete');
    //sales orders
    Route::get('/sales_orders/create', 'SalesOrderController@create')->name('sales_orders.create');
    Route::post('/sales_orders', 'SalesOrderController@save')->name('sales_orders.create.save');
    Route::get('/sales_orders/{id}/edit', 'SalesOrderController@edit')->name('sales_orders.edit');
    Route::put('/sales_orders/{id}', 'SalesOrderController@update')->name('sales_orders.edit.update');
    Route::get('/sales_orders/{id}/duplicate', 'SalesOrderController@duplicate')->name('sales_orders.create.duplicate');
    Route::get('/sales_orders/{quote_id}/quotes', 'SalesOrderController@createFromQuote')->name('sales_orders.create.quotes');
    Route::get('/sales_orders/{id}', 'SalesOrderController@view')->name('sales_orders.view');
    Route::delete('/sales_orders/{id}', 'SalesOrderController@delete')->name('sales_orders.delete');
    //sales quotes
    Route::get('/sales_quotes/create', 'SalesQuoteController@create')->name('sales_quotes.create');
    Route::post('/sales_quotes', 'SalesQuoteController@save')->name('sales_quotes.create.save');
    Route::get('/sales_quotes/{id}/edit', 'SalesQuoteController@edit')->name('sales_quotes.edit');
    Route::put('/sales_quotes/{id}', 'SalesQuoteController@update')->name('sales_quotes.edit.update');
    Route::get('/sales_quotes/{id}/duplicate', 'SalesQuoteController@duplicate')->name('sales_quotes.create.duplicate');
    Route::get('/sales_quotes/{id}', 'SalesQuoteController@view')->name('sales_quotes.view');
    Route::delete('/sales_quotes/{id}', 'SalesQuoteController@delete')->name('sales_quotes.delete');

    //journals
    Route::get('/journals/create', 'JournalController@createJournal')->name('journals.create');
    Route::post('/journals', 'JournalController@save')->name('journals.create.save');
    Route::get('/journals/import', 'JournalController@import')->name('journals.import');
    Route::post('/journals/import', 'JournalController@importSave')->name('journals.import.save');
    Route::get('/journals/{id}/edit', 'JournalController@edit')->name('journals.edit');
    Route::delete('/journals/{id}', 'JournalController@delete')->name('journals.delete');
    Route::put('/journals/{id}', 'JournalController@update')->name('journals.edit.update');
    Route::get('/journals/{id}/duplicate', 'JournalController@duplicate')->name('journals.create.duplicate');
    Route::get('/journals/{id}', 'JournalController@view')->name('journals.view');
    Route::get('/journals/{id}/report', 'JournalController@report')->name('journals.report');
    Route::post('/journals/{id}/lock', 'JournalController@lockJournal')->name('journals.lock');
    Route::post('/journals/{id}/voucher', 'JournalController@toVoucher')->name('journals.tovoucher');
    Route::post('/journals/vouchers', 'JournalController@toVoucherBatch')->name('journals.tovoucher.batch');

    //accounts
    Route::get('/accounts', 'AccountController@index')->name('accounts.index');
    Route::get('/accounts/opening_balance', 'AccountController@openingBalance')->name('accounts.opening_balance');
    Route::post('/accounts/opening_balance', 'AccountController@saveOpeningBalance')->name('accounts.opening_balance.save');
    Route::get('/accounts/budgets', 'AccountController@budget')->name('accounts.budgets');
    Route::post('/accounts/budgets', 'AccountController@saveBudget')->name('accounts.budgets.save');
    Route::get('/accounts/create', 'AccountController@create')->name('accounts.create');
    Route::post('/accounts/create', 'AccountController@save')->name('accounts.create.save');
    Route::get('/accounts/import', 'AccountController@import')->name('accounts.import');
    Route::post('/accounts/import', 'AccountController@saveImport')->name('accounts.import.save');
    Route::get('/accounts/{id}', 'AccountController@view')->name('accounts.view');
    Route::get('/accounts/{id}/edit', 'AccountController@edit')->name('accounts.edit');
    Route::put('/accounts/{id}', 'AccountController@update')->name('accounts.edit.update');
    Route::delete('/accounts/{id}', 'AccountController@delete')->name('accounts.delete');

    //settings
    Route::get('/settings', 'SettingController@index')->name('settings.index');
    Route::get('/settings/account_mapping', 'SettingController@accountMapping')->name('settings.account_mapping');
    Route::post('/settings/account_mapping', 'SettingController@accountMappingSave')->name('settings.account_mapping.save');

    //numberings
    Route::get('/numberings', 'NumberingController@index')->name('numberings.index');
    Route::get('/numberings/create', 'NumberingController@create')->name('numberings.create');
    Route::post('/numberings', 'NumberingController@save')->name('numberings.create.save');
    Route::get('/numberings/{id}/duplicate', 'NumberingController@duplicate')->name('numberings.create.duplicate');
    Route::get('/numberings/{id}/edit', 'NumberingController@edit')->name('numberings.edit');
    Route::put('/numberings/{id}/update', 'NumberingController@update')->name('numberings.edit.update');
    Route::delete('/numberings/{id}', 'NumberingController@delete')->name('numberings.delete');
    Route::get('/numberings/{id}', 'NumberingController@view')->name('numberings.view');

    //journal_types
    Route::get('/journal_types', 'JournalController@journalType')->name('journal_types.index');
    Route::get('/journal_types/create', 'JournalController@createJournalType')->name('journal_types.create');
    Route::post('/journal_types', 'JournalController@saveJournalType')->name('journal_types.create.save');
    Route::get('/journal_types/{id}/duplicate', 'JournalController@duplicateJournalType')->name('journal_types.create.duplicate');
    Route::get('/journal_types/{id}/edit', 'JournalController@editJournalType')->name('journal_types.edit');
    Route::put('/journal_types/{id}/update', 'JournalController@updateJournalType')->name('journal_types.edit.update');
    Route::get('/journal_types/{id}', 'JournalController@viewJournalType')->name('journal_types.view');
    Route::delete('/journal_types/{id}', 'JournalController@deleteJournalType')->name('journal_types.delete');
    //user_groups
    Route::get('/user_groups', 'UserGroupController@index')->name('user_groups.index');
    Route::get('/user_groups/create', 'UserGroupController@create')->name('user_groups.create');
    Route::post('/user_groups', 'UserGroupController@save')->name('user_groups.create.save');
    Route::get('/user_groups/{id}/edit', 'UserGroupController@edit')->name('user_groups.edit');
    Route::put('/user_groups/{id}/update', 'UserGroupController@update')->name('user_groups.edit.update');
    Route::delete('/user_groups/{id}', 'UserGroupController@delete')->name('user_groups.delete');

    //user
    Route::post('/users/lang/{id}', 'UserController@setLang')->name('users.language');
    Route::get('/users/profile', 'UserController@profile')->name('users.profile');
    Route::get('/users/profile/edit', 'UserController@profileEdit')->name('users.profile.edit');
    Route::put('/users/profile', 'UserController@profileUpdate')->name('users.profile.update');
    Route::get('/users', 'UserController@index')->name('users.index');
    Route::get('/users/create', 'UserController@create')->name('users.create');
    Route::post('/users', 'UserController@save')->name('users.create.save');
    Route::get('/users/actions', 'UserController@action')->name('users.actions');
    Route::post('/users/actions', 'UserController@saveAction')->name('users.actions.save');
    Route::get('/users/password', 'UserController@action')->name('user.password');

    Route::get('/users/{id}', 'UserController@view')->name('users.view');
    Route::get('/users/{id}/edit', 'UserController@edit')->name('users.edit');
    Route::put('/users/{id}/update', 'UserController@update')->name('users.edit.update');
    Route::delete('/users/{id}', 'UserController@delete')->name('users.delete');


    //logs
    Route::get('/logs', 'LogController@index')->name('logs.index');
    Route::get('/logs/{id}', 'LogController@view')->name('logs.view');
    Route::delete('/logs/{id}', 'LogController@delete')->name('logs.delete');
    Route::post('/logs/delete/all', 'LogController@deleteBatch')->name('logs.delete.batch');

    //select2
    Route::get('/select2/{name}', 'Select2OutputController@get')->name('select2');
    //JSON Output
    Route::get('/json/{name}', 'JSONOutputController@index')->name('json.output');

    Route::get('/notifications', 'NotificationController@latest')->name('notifications.latest');
    Route::get('/notifications/{id}', 'NotificationController@read')->name('notifications.read');

    //CRUD
    Route::get('/dt/{name}', 'DcruController@dt')->name('dcru.index.dt');
    Route::get('/{name}', 'DcruController@index')->name('dcru.index');
    Route::get('/{name}/create', 'DcruController@create')->name('dcru.create');
    Route::get('/{name}/{id}/edit', 'DcruController@edit')->name('dcru.edit');
    Route::get('/{name}/{id}/duplicate', 'DcruController@duplicate')->name('dcru.create.duplicate');
    Route::get('/{name}/{id}', 'DcruController@view')->name('dcru.view');
    Route::post('/download', 'DcruController@download')->name('dcru.download');
    Route::post('/{name}', 'DcruController@save')->name('dcru.create.save');
    Route::put('/{name}/{id}', 'DcruController@update')->name('dcru.edit.update');
    Route::delete('/{name}/{id}', 'DcruController@deletePermanent')->name('dcru.delete');
    Route::post('/{name}/delete/all', 'DcruController@deleteAll')->name('dcru.delete.batch');
    Route::delete('/{name}/delete/file', 'DcruController@deleteFile')->name('dcru.delete.file');
    Route::delete('/{name}/delete/{id}', 'DcruController@deletePermanent')->name('dcru.delete.permanent');


});


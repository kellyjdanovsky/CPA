<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth::routes();

// Routes d'authentification personnalisées
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Routes de réinitialisation de mot de passe
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Route pour un second type de login (exemple)
// Route::get('admin/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
// Route::post('admin/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');





//Route::get('/test', 'TestController@index')->name('test');
Route::get('/privacy-policy', 'HomeController@privacy_policy')->name('privacy_policy');
Route::get('/terms-of-use', 'HomeController@terms_of_use')->name('terms_of_use');



Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'HomeController@dashboard')->name('home');
    Route::get('/home', 'HomeController@dashboard')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::group(['prefix' => 'my_account'], function() {
        Route::get('/', 'MyAccountController@edit_profile')->name('my_account');
        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password', 'MyAccountController@change_pass')->name('my_account.change_pass');
    });

    /*************** Session Management (accessible à tous) *****************/
    Route::post('sessions/change', 'SupportTeam\SessionController@changeSession')->name('sessions.change');
    Route::get('sessions/get', 'SupportTeam\SessionController@getSessions')->name('sessions.get');

    /*************** Support Team *****************/
    Route::group(['namespace' => 'SupportTeam',], function(){

        /*************** Analytics *****************/
        Route::get('analytics', 'AnalyticsController@index')->name('analytics');

        /*************** Students *****************/
        Route::group(['prefix' => 'students'], function(){
            Route::get('reset_pass/{st_id}', 'StudentRecordController@reset_pass')->name('st.reset_pass');
            Route::get('graduated', 'StudentRecordController@graduated')->name('students.graduated');
            Route::put('not_graduated/{id}', 'StudentRecordController@not_graduated')->name('st.not_graduated');
            Route::get('list/{class_id}', 'StudentRecordController@listByClass')->name('students.list')->middleware('teamSAT');
            Route::get('list-all', 'StudentRecordController@listAll')->name('students.list_all')->middleware('teamSAT');
            Route::get('export', 'StudentRecordController@export')->name('students.export')->middleware('teamSAT');
            Route::get('print-attendance/{class_id}/{section_id?}', 'StudentRecordController@printAttendanceSheet')->name('students.print_attendance')->middleware('teamSAT');
            Route::get('statistics/detailed', 'StudentStatisticsController@getDetailedStatistics')->name('students.statistics.detailed')->middleware('teamSAT');
            Route::get('statistics/export', 'StudentStatisticsController@exportStatistics')->name('students.statistics.export')->middleware('teamSAT');
            Route::get('statistics/print-report', 'StudentStatisticsController@printStatisticsReport')->name('students.statistics.print_report')->middleware('teamSAT');
            Route::post('bulk_action', 'StudentRecordController@bulkAction')->name('students.bulk_action');

            /* Promotions */
            Route::post('promote_selector', 'PromotionController@selector')->name('students.promote_selector');
            Route::get('promotion/manage', 'PromotionController@manage')->name('students.promotion_manage');
            Route::delete('promotion/reset/{pid}', 'PromotionController@reset')->name('students.promotion_reset');
            Route::delete('promotion/reset_all', 'PromotionController@reset_all')->name('students.promotion_reset_all');
            Route::delete('promotion/reset_graduated/{student_id}', 'PromotionController@reset_graduated')->name('students.promotion_reset_graduated');
            Route::delete('promotion/reset_all_graduated', 'PromotionController@reset_all_graduated')->name('students.promotion_reset_all_graduated');
            Route::get('promotion/{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion')->name('students.promotion');
            Route::post('promote/{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote')->name('students.promote');



        });

        /*************** Users *****************/
        Route::group(['prefix' => 'users'], function(){
            Route::get('reset_pass/{id}', 'UserController@reset_pass')->name('users.reset_pass');
        });

        /*************** TimeTables *****************/
        Route::group(['prefix' => 'timetables'], function(){
            Route::get('/', 'TimeTableController@index')->name('tt.index');

            Route::group(['middleware' => 'teamSA'], function() {
                Route::post('/', 'TimeTableController@store')->name('tt.store');
                Route::put('/{tt}', 'TimeTableController@update')->name('tt.update');
                Route::delete('/{tt}', 'TimeTableController@delete')->name('tt.delete');
            });

            /*************** TimeTable Records *****************/
            Route::group(['prefix' => 'records'], function(){

                Route::group(['middleware' => 'teamSA'], function(){
                    Route::get('manage/{ttr}', 'TimeTableController@manage')->name('ttr.manage');
                    Route::post('/', 'TimeTableController@store_record')->name('ttr.store');
                    Route::get('edit/{ttr}', 'TimeTableController@edit_record')->name('ttr.edit');
                    Route::put('/{ttr}', 'TimeTableController@update_record')->name('ttr.update');
                });

                Route::get('show/{ttr}', 'TimeTableController@show_record')->name('ttr.show');
                Route::get('print/{ttr}', 'TimeTableController@print_record')->name('ttr.print');
                Route::delete('/{ttr}', 'TimeTableController@delete_record')->name('ttr.destroy');

            });

            /*************** Time Slots *****************/
            Route::group(['prefix' => 'time_slots', 'middleware' => 'teamSA'], function(){
                Route::post('/', 'TimeTableController@store_time_slot')->name('ts.store');
                Route::post('/use/{ttr}', 'TimeTableController@use_time_slot')->name('ts.use');
                Route::get('edit/{ts}', 'TimeTableController@edit_time_slot')->name('ts.edit');
                Route::delete('/{ts}', 'TimeTableController@delete_time_slot')->name('ts.destroy');
                Route::put('/{ts}', 'TimeTableController@update_time_slot')->name('ts.update');
            });

        });

        /*************** Payments *****************/
        Route::group(['prefix' => 'payments'], function(){

            Route::get('verified/{class_id?}', 'PaymentController@verified')->name('payments.verified');
            Route::get('selectpaymetns', 'PaymentController@select')->name('payments.select');
            Route::get('check_unpaid', 'PaymentController@checkUnpaid')->name('payments.check_unpaid');
            Route::get('export_unpaid', 'PaymentController@exportUnpaidExcel')->name('payments.export_unpaid');
            Route::post('generate_notifications', 'PaymentController@generatePaymentNotifications')->name('payments.generate_notifications');
            Route::post('export_notifications_excel', 'PaymentController@exportExcelForNotifications')->name('payments.export_notifications_excel');
            Route::post('export_notifications_word', 'PaymentController@exportWordForNotifications')->name('payments.export_notifications_word');
            Route::get('filter', 'PaymentController@select')->name('payments.filter');

            // ADRA & TEAM 3 Payment Management
            Route::get('adra-team3/filter', 'PaymentController@adraTeam3Filter')->name('payments.adra_team3.filter');
            Route::get('adra-team3/get-payments', 'PaymentController@getClassPayments')->name('payments.adra_team3.get_payments');
            Route::get('adra-team3/get-students', 'PaymentController@getPaymentStudents')->name('payments.adra_team3.get_students');
            Route::post('adra-team3/update-reference', 'PaymentController@updateReference')->name('payments.adra_team3.update_reference');
            Route::post('adra-team3/print-receipt/{student_id}', 'PaymentController@printAdraTeam3Receipt')->name('payments.adra_team3.print_receipt');
            Route::post('adra-team3/print-batch', 'PaymentController@printBatchReceipts')->name('payments.adra_team3.print_batch');
            Route::get('adra-team3/export-excel', 'PaymentController@exportAdraTeam3Excel')->name('payments.adra_team3.export_excel');

            Route::get('manage/{class_id?}', 'PaymentController@manage')->name('payments.manage');
            Route::get('invoice/{id}/{year?}', 'PaymentController@invoice')->name('payments.invoice');
            Route::get('receipts/{id}', 'PaymentController@receipts')->name('payments.receipts');
            Route::get('pdf_receipts/{id}', 'PaymentController@pdf_receipts')->name('payments.pdf_receipts');
            Route::post('select_year', 'PaymentController@select_year')->name('payments.select_year');
            Route::post('select_class', 'PaymentController@select_class')->name('payments.select_class');
            Route::delete('reset_record/{id}', 'PaymentController@reset_record')->name('payments.reset_record');
            Route::post('pay_now/{id}', 'PaymentController@pay_now')->name('payments.pay_now');
            
            // Grouped payment routes
            Route::post('process-grouped-payment', 'PaymentController@processGroupedPayment')->name('payments.process_grouped_payment');
            Route::get('get-class-payments-ajax', 'PaymentController@getClassPaymentsAjax')->name('payments.get_class_payments_ajax');

            Route::get('journal', 'PaymentController@journal')->name('payments.journal');
            Route::get('journal/filter', 'PaymentController@journalFilter')->name('payments.journal.filter');
            Route::get('journal/export/excel', 'PaymentController@journalExportExcel')->name('payments.journal.export.excel');
            Route::get('journal/print-cloture', 'PaymentController@journalPrintCloture')->name('payments.journal.print_cloture');
            
            /*************** Encaissements *****************/
            Route::group(['prefix' => 'encaissements'], function(){
                Route::get('/', 'EncaissementController@index')->name('payments.encaissements.index');
                Route::get('create', 'EncaissementController@create')->name('payments.encaissements.create');
                Route::post('process', 'EncaissementController@processEncaissement')->name('payments.encaissements.process');
                Route::get('show/{id}', 'EncaissementController@show')->name('payments.encaissements.show');
                Route::get('edit/{id}', 'EncaissementController@edit')->name('payments.encaissements.edit');
                Route::put('update/{id}', 'EncaissementController@update')->name('payments.encaissements.update');
                Route::delete('destroy/{id}', 'EncaissementController@destroy')->name('payments.encaissements.destroy');
                
                // Ajax routes
                Route::get('get-class-payments', 'EncaissementController@getClassPayments')->name('payments.encaissements.get_class_payments');
                Route::get('get-eligible-students', 'EncaissementController@getEligibleStudents')->name('payments.encaissements.get_eligible_students');
                
                // Export and print
                Route::get('export/excel', 'EncaissementController@exportExcel')->name('payments.encaissements.export_excel');
                Route::post('print/receipts', 'EncaissementController@printReceipts')->name('payments.encaissements.print_receipts');
                
                // Statistics and journal
                Route::get('statistics', 'EncaissementController@getStatistics')->name('payments.encaissements.statistics');
                Route::get('journal', 'EncaissementController@journal')->name('payments.encaissements.journal');
            });

            /*************** Recettes *****************/
            Route::group(['prefix' => 'recettes'], function(){
                Route::get('/', 'RecetteController@index')->name('payments.recettes.index');
                Route::get('create', 'RecetteController@create')->name('payments.recettes.create');
                Route::post('store', 'RecetteController@store')->name('payments.recettes.store');
                Route::get('show/{id}', 'RecetteController@show')->name('payments.recettes.show');
                Route::get('edit/{id}', 'RecetteController@edit')->name('payments.recettes.edit');
                Route::put('update/{id}', 'RecetteController@update')->name('payments.recettes.update');
                Route::delete('destroy/{id}', 'RecetteController@destroy')->name('payments.recettes.destroy');
                
                // Synchronization
                Route::post('sync-receipts', 'RecetteController@syncWithReceipts')->name('payments.recettes.sync_receipts');
                
                // Export
                Route::get('export/excel', 'RecetteController@exportExcel')->name('payments.recettes.export_excel');
                Route::get('export/pdf', 'RecetteController@exportPdf')->name('payments.recettes.export_pdf');
                
                // Statistics and dashboard
                Route::get('statistics', 'RecetteController@getStatistics')->name('payments.recettes.statistics');
                Route::get('chart-data', 'RecetteController@getChartData')->name('payments.recettes.chart_data');
                Route::get('dashboard', 'RecetteController@dashboard')->name('payments.recettes.dashboard');
            });

            /*************** Décaissements *****************/
            Route::group(['prefix' => 'decaissements'], function(){
                Route::get('/', 'DecaissementController@index')->name('payments.decaissements.index');
                Route::get('create', 'DecaissementController@create')->name('payments.decaissements.create');
                Route::post('store', 'DecaissementController@store')->name('payments.decaissements.store');
                Route::get('show/{id}', 'DecaissementController@show')->name('payments.decaissements.show');
                Route::get('edit/{id}', 'DecaissementController@edit')->name('payments.decaissements.edit');
                Route::put('update/{id}', 'DecaissementController@update')->name('payments.decaissements.update');
                Route::delete('destroy/{id}', 'DecaissementController@destroy')->name('payments.decaissements.destroy');
                
                // Workflow actions
                Route::post('approve/{id}', 'DecaissementController@approve')->name('payments.decaissements.approve');
                Route::post('mark-paid/{id}', 'DecaissementController@markAsPaid')->name('payments.decaissements.mark_paid');
                Route::post('cancel/{id}', 'DecaissementController@cancel')->name('payments.decaissements.cancel');
                
                // Piece justificative
                Route::post('validate-piece/{id}', 'DecaissementController@validatePieceJustificative')->name('payments.decaissements.validate_piece');
                Route::get('download-piece/{id}', 'DecaissementController@downloadPieceJustificative')->name('payments.decaissements.download_piece');
                
                // Print and export
                Route::get('print-op/{id}', 'DecaissementController@printOP')->name('payments.decaissements.print_op');
                Route::get('print-thermal/{id}', 'DecaissementController@printThermal')->name('payments.decaissements.print_thermal');
                Route::post('print-multiple', 'DecaissementController@printMultipleOP')->name('payments.decaissements.print_multiple');
                Route::get('export/excel', 'DecaissementController@exportExcel')->name('payments.decaissements.export_excel');
                
                // Statistics and journal
                Route::get('statistics', 'DecaissementController@getStatistics')->name('payments.decaissements.statistics');
                Route::get('journal', 'DecaissementController@journal')->name('payments.decaissements.journal');
            });

        });

        /*************** Pins *****************/
        Route::group(['prefix' => 'pins'], function(){
            Route::get('create', 'PinController@create')->name('pins.create');
            Route::get('/', 'PinController@index')->name('pins.index');
            Route::post('/', 'PinController@store')->name('pins.store');
            Route::get('enter/{id}', 'PinController@enter_pin')->name('pins.enter');
            Route::post('verify/{id}', 'PinController@verify')->name('pins.verify');
            Route::delete('/', 'PinController@destroy')->name('pins.destroy');
        });

        /*************** Marks *****************/
        Route::group(['prefix' => 'marks'], function(){

           // FOR teamSA
            Route::group(['middleware' => 'teamSA'], function(){
                Route::get('batch_fix', 'MarkController@batch_fix')->name('marks.batch_fix');
                Route::put('batch_update', 'MarkController@batch_update')->name('marks.batch_update');
                Route::get('tabulation/{exam?}/{class?}/{sec_id?}', 'MarkController@tabulation')->name('marks.tabulation');
                Route::post('tabulation', 'MarkController@tabulation_select')->name('marks.tabulation_select');
                Route::get('tabulation/print/{exam}/{class}/{sec_id}', 'MarkController@print_tabulation')->name('marks.print_tabulation');
                
                // Weighted Grades Tabulation
                Route::get('weighted-grades/{exam?}/{class?}/{sec_id?}', 'MarkController@weighted_grades')->name('marks.weighted_grades');
                Route::post('weighted-grades', 'MarkController@weighted_grades_select')->name('marks.weighted_grades_select');
                Route::get('weighted-grades/print/{exam}/{class}/{sec_id}', 'MarkController@print_weighted_grades')->name('marks.print_weighted_grades');
                Route::get('weighted-grades/export/{exam}/{class}/{sec_id}', 'MarkController@export_weighted_grades')->name('marks.export_weighted_grades');
            });

            // FOR teamSAT
            Route::group(['middleware' => 'teamSAT'], function(){
                Route::get('/', 'MarkController@index')->name('marks.index');
                Route::get('manage/{exam}/{class}/{section}/{subject}', 'MarkController@manage')->name('marks.manage');
                Route::put('update/{exam}/{class}/{section}/{subject}', 'MarkController@update')->name('marks.update');
                Route::put('comment_update/{exr_id}', 'MarkController@comment_update')->name('marks.comment_update');
                Route::put('remark_update/{mark_id}', 'MarkController@remark_update')->name('marks.remark_update');
                Route::put('skills_update/{skill}/{exr_id}', 'MarkController@skills_update')->name('marks.skills_update');
                Route::post('selector', 'MarkController@selector')->name('marks.selector');
                Route::get('bulk/{class?}/{section?}', 'MarkController@bulk')->name('marks.bulk');
                Route::post('bulk', 'MarkController@bulk_select')->name('marks.bulk_select');
                
                // Real-time editing AJAX routes
                Route::post('comment/update', 'MarkController@ajaxCommentUpdate')->name('marks.comment.update');
                Route::post('comment/delete', 'MarkController@ajaxCommentDelete')->name('marks.comment.delete');
                Route::post('general-comment/update', 'MarkController@ajaxGeneralCommentUpdate')->name('marks.general.comment.update');
                Route::post('general-comment/delete', 'MarkController@ajaxGeneralCommentDelete')->name('marks.general.comment.delete');
            });

            Route::get('select_year/{id}', 'MarkController@year_selector')->name('marks.year_selector');
            Route::post('select_year/{id}', 'MarkController@year_selected')->name('marks.year_select');
            Route::get('show/{id}/{year}', 'MarkController@show')->name('marks.show');
            Route::get('print/{id}/{exam_id}/{year}', 'MarkController@print_view')->name('marks.print');
            Route::get('print_multiple/{id}/{year}', 'MarkController@print_multiple')->name('marks.print_multiple');
            Route::post('save_decision', 'MarkController@save_decision')->name('marks.save_decision');

        });

        Route::resource('students', 'StudentRecordController');
        Route::resource('users', 'UserController');
        Route::resource('classes', 'MyClassController');
        Route::resource('sections', 'SectionController');
        Route::resource('subjects', 'SubjectController');
        Route::resource('grades', 'GradeController');
        Route::resource('exams', 'ExamController');
        Route::resource('dorms', 'DormController');
        Route::resource('payments', 'PaymentController');

        // Routes pour la gestion des sessions
        Route::resource('sessions', 'SessionController');
        Route::post('sessions/{id}/set-active', 'SessionController@setActive')->name('sessions.set_active');

        /*************** PASCOMA - Attestations d'Assurance *****************/
        Route::group(['prefix' => 'pascoma'], function(){
            Route::get('/', 'PascomaController@index')->name('pascoma.index');
            Route::get('export', 'PascomaController@export')->name('pascoma.export');
        });



// Vous pouvez ajouter ici d'autres routes si nécessaire pour le nouveau login













        /*************** Projets *****************/
        Route::resource('projets', 'ProjetController');

        /*************** Print Test *****************/
        Route::get('marks/test-print', function () {
            return view('pages.support_team.marks.test-print');
        })->name('marks.test-print');

    });

    /************************ AJAX ****************************/
    Route::group(['prefix' => 'ajax'], function() {
        Route::get('get_lga/{state_id}', 'AjaxController@get_lga')->name('get_lga');
        Route::get('get_class_sections/{class_id}', 'AjaxController@get_class_sections')->name('get_class_sections');
        Route::get('get_class_subjects/{class_id}', 'AjaxController@get_class_subjects')->name('get_class_subjects');
        Route::get('get_available_years', 'AjaxController@get_available_years')->name('ajax.get_available_years');
        Route::post('update_student_field', 'AjaxController@update_student_field')->name('ajax.update_student_field');
        Route::get('search_students', 'AjaxController@search_students')->name('ajax.search_students');
        Route::get('global-search', 'AjaxController@globalSearch')->name('ajax.global_search');
    });

});

/************************ SUPER ADMIN ****************************/
Route::group(['namespace' => 'SuperAdmin','middleware' => 'super_admin', 'prefix' => 'super_admin'], function(){

    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::put('/settings', 'SettingController@update')->name('settings.update');

    /*************** Duplicate Management *****************/
    Route::group(['prefix' => 'duplicate_management'], function(){
        Route::get('dashboard', 'DuplicateManagementController@dashboard')->name('duplicate.dashboard');
        Route::get('logs', 'DuplicateManagementController@logs')->name('duplicate.logs');
        Route::get('locks', 'DuplicateManagementController@locks')->name('duplicate.locks');
        Route::get('report', 'DuplicateManagementController@generateReport')->name('duplicate.report');
        Route::get('statistics', 'DuplicateManagementController@getStatistics')->name('duplicate.statistics');
        Route::get('search', 'DuplicateManagementController@searchDuplicates')->name('duplicate.search');
        Route::get('export/logs', 'DuplicateManagementController@exportLogs')->name('duplicate.export.logs');
        
        Route::post('locks/{id}/release', 'DuplicateManagementController@releaseLock')->name('duplicate.locks.release');
        Route::post('locks/cleanup', 'DuplicateManagementController@cleanupLocks')->name('duplicate.locks.cleanup');
        Route::post('logs/cleanup', 'DuplicateManagementController@cleanupLogs')->name('duplicate.logs.cleanup');
        Route::post('remove', 'DuplicateManagementController@removeDuplicates')->name('duplicate.remove');
        
        Route::get('settings', 'DuplicateManagementController@getSettings')->name('duplicate.settings');
        Route::put('settings', 'DuplicateManagementController@updateSettings')->name('duplicate.settings.update');
    });

});

/************************ PARENT ****************************/
Route::group(['namespace' => 'MyParent','middleware' => 'my_parent',], function(){

    Route::get('/my_children', 'MyController@children')->name('my_children');

});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SessionsController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\SpecialCategoryController;
use App\Http\Controllers\Admin\FeeManagementController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ExamManagementController;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UserManagementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return redirect('login');
});

Route::get('php-info', function () {
    phpinfo();
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'module.access'])->prefix('admin')->group(function () {
    Route::get('/course', [CoursesController::class, 'index'])->name('admin.course.index');
    Route::post('/course', [CoursesController::class, 'store'])->name('admin.course.store');
    Route::put('/course/{id}', [CoursesController::class, 'update'])->name('admin.course.update');
    Route::delete('/course/{id}', [CoursesController::class, 'destroy'])->name('admin.course.destroy');
// Course - Subject Routes Start

Route::post('/course/{courseId}/subject', [CoursesController::class, 'storeSubject'])->name('admin.subject.store');
Route::put('/course/{courseId}/subject/{subjectId}', [CoursesController::class, 'updateSubject'])->name('admin.subject.update');
Route::delete('/course/{courseId}/subject/{subjectId}', [CoursesController::class, 'destroySubject'])->name('admin.subject.destroy');
// Course - Subject Routes End
    // Profile Routes Start
  Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile.show');
    Route::post('/profile', [ProfileController::class, 'storeOrUpdate'])->name('admin.profile.storeOrUpdate');
    // ?Profile Routes End

    // Session Routes Start
    Route::get('/sessions', [SessionsController::class, 'index'])->name('admin.sessions.index');
    Route::post('/sessions/create', [SessionsController::class, 'store'])->name('admin.sessions.store');
    Route::put('/sessions/{id}', [SessionsController::class, 'update'])->name('admin.sessions.update');
    Route::delete('/sessions/{id}', [SessionsController::class, 'destroy'])->name('admin.sessions.destroy');
    Route::post('/sessions/activate/{id}', [SessionsController::class, 'activate'])->name('admin.sessions.activate');
    Route::post('/sessions/merge-duplicates', [SessionsController::class, 'mergeDuplicates'])->name('admin.sessions.merge');
    // Session Routes End
    // Students Routes Start
    Route::get('/students/strength', [CoursesController::class, 'studentStrength'])->name('admin.students.strength');
    Route::get('/students', [CoursesController::class, 'students'])->name('admin.students.index');
    Route::get('/students/download-csv', [CoursesController::class, 'download_csv'])->name('admin.students.download');
    Route::post('/certificate-create/{student_id}/{type}', [CoursesController::class, 'certificate_certificate'])->name('admin.students.certificate_certificate');
    Route::get('/students/upload', [CoursesController::class, 'upload'])->name('admin.students.upload');
    Route::get('/students/uploadAKS', [CoursesController::class, 'uploadAKS'])->name('admin.students.uploadAKS');
    Route::post('/students/upload_saveAKS', [CoursesController::class, 'upload_saveAKS'])->name('admin.students.uploadAKS.save');
    Route::post('/students/upload_save', [CoursesController::class, 'upload_save'])->name('admin.students.upload.save');
    Route::get('/students/run-import-2026', [CoursesController::class, 'runImport2026'])->name('admin.students.run_import_2026');
    // Student Export Routes (filtered)
    Route::get('/students/export/excel', [\App\Http\Controllers\Admin\StudentExportController::class, 'excel'])->name('admin.students.export.excel');
    Route::get('/students/export/pdf',   [\App\Http\Controllers\Admin\StudentExportController::class, 'pdf'])->name('admin.students.export.pdf');

    Route::get('/students/create', [CoursesController::class, 'createStudent'])->name('admin.students.create');
    Route::post('/students', [CoursesController::class, 'storeStudent'])->name('admin.students.store');
    Route::get('/students/{id}', [CoursesController::class, 'studentDetails'])->name('admin.students.show');
    Route::get('/students/{id}/edit', [CoursesController::class, 'editStudent'])->name('admin.students.edit');
    Route::put('/students/{id}', [CoursesController::class, 'updateStudent'])->name('admin.students.update');
    Route::delete('/students/{id}', [CoursesController::class, 'destroyStudent'])->name('admin.students.destroy');
    Route::get('/courses/subjects', [CoursesController::class, 'getSubjects'])->name('admin.courses.subjects');
    Route::post('/students/add_session/{id}', [CoursesController::class, 'add_session'])->name('admin.students.add_session');

 //    Documents
Route::get('/students/{id}/clc', [CoursesController::class, 'clc'])->name('admin.students.clc');
Route::get('/students/{id}/character', [CoursesController::class, 'character'])->name('admin.students.character');
Route::get('/students/{id}/bonafied', [CoursesController::class, 'bonafied'])->name('admin.students.bonafied');

    // Students Routes End



    // Fees Routes

    Route::get('fee/structure', [FeeStructureController::class, 'index2'])->name('fee_structure.index');
    Route::post('fee/structure', [FeeStructureController::class, 'store2'])->name('fee_structure.store');



 Route::get('fee/structure2', [FeeStructureController::class, 'index2'])->name('fee_structure2.index');
    Route::post('fee/structure2', [FeeStructureController::class, 'store2'])->name('fee_structure2.store');


        Route::get('fee/special-category', [SpecialCategoryController::class, 'index'])->name('special_category.index');
    Route::post('fee/special-category', [SpecialCategoryController::class, 'store'])->name('special_category.store');
    //
     Route::get('fee/management', [FeeManagementController::class, 'index'])->name('fee_management.index');
    Route::post('fee/management/generate', [FeeManagementController::class, 'generate'])->name('fee_management.generate');
    Route::get('fee/management/view/{student_id}/{course_id}', [FeeManagementController::class, 'view'])->name('fee_management.view');
    Route::post('fee/management/add-fee', [FeeManagementController::class, 'addFee'])->name('fee_management.add_fee');
    Route::post('fee/management/regenerate', [FeeManagementController::class, 'regenerate'])->name('fee_management.regenerate');
    Route::post('fee/management/pay', [FeeManagementController::class, 'pay'])->name('fee_management.pay');
    Route::post('fee/management/pay-multi', [FeeManagementController::class, 'payMulti'])->name('fee_management.pay_multi');
    Route::get('fee/management/fee-heads', [FeeManagementController::class, 'getFeeHeads'])->name('fee_management.fee_heads');
    Route::get('fee/management/receipt/{student_id}/{course_id}', [FeeManagementController::class, 'receipt'])->name('fee_management.receipt');
    Route::get('fee/management/receipt-batch/{student_id}/{course_id}', [FeeManagementController::class, 'receiptBatch'])->name('fee_management.receipt_batch');
    Route::get('fee/management/receipt/{student_id}/{course_id}/{receipt_id}', [FeeManagementController::class, 'receipt_single'])->name('fee_management.receipt_single');
    Route::get('fee/management/delete_receipt/{student_id}/{course_id}/{receipt_id}', [FeeManagementController::class, 'delete_receipt'])->name('fee_management.delete_receipt');

    Route::get('fee/report', [FeeManagementController::class, 'report'])->name('fee_management.report');
    Route::get('fee/report/export/csv',   [FeeManagementController::class, 'reportExport'])->name('fee_management.report_export');
    Route::get('fee/report/export/excel', [FeeManagementController::class, 'reportExcel'])->name('fee_management.report_excel');
    Route::get('fee/report/export/pdf',   [FeeManagementController::class, 'reportPDF'])->name('fee_management.report_pdf');

    // Staff Routes
    Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('admin.staff.show');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');
    Route::get('/staff/{staff}/transfer-certificate', [StaffController::class, 'generateTransferCertificate'])->name('admin.staff.transfer_certificate');
    Route::get('/staff/{staff}/id-card', [StaffController::class, 'generateIdCard'])->name('admin.staff.id_card');
    Route::match(['get', 'post'], '/staff/attendance/mark', [StaffController::class, 'markAttendance'])->name('admin.staff.attendance.mark');
    Route::match(['get', 'post'], '/staff/attendance/Pre_mark', [StaffController::class, 'markAttendancePre'])->name('admin.staff.attendance.markAttendancePre');
    Route::get('/staff/attendance/view/{staff?}', [StaffController::class, 'viewAttendance'])->name('admin.staff.attendance.view');
    Route::get('/staff/attendance/report', [StaffController::class, 'generateAttendanceReport'])->name('admin.staff.attendance.report');
    Route::get('/staff/attendance/export-csv', [StaffController::class, 'exportAttendanceCsv'])->name('admin.staff.attendance.export_csv');
    Route::match(['get', 'post'], '/staff/{staff}/salary/structure', [StaffController::class, 'manageSalaryStructure'])->name('admin.staff.salary.structure');
    Route::match(['get', 'post'], '/staff/salary/payments/{staff?}', [StaffController::class, 'manageSalaryPayments'])->name('admin.staff.salary.payments')->withTrashed();
    Route::match(['get', 'post'], '/staff/salary/payments_view/{staff?}', [StaffController::class, 'manageSalaryPayments_view'])->name('admin.staff.salary.payments_view')->withTrashed();
    Route::get('/staff/{staff}/salary/payslip/{month_year}', [StaffController::class, 'generatePayslip'])->name('admin.staff.salary.payslip')->withTrashed();
    Route::get('/staff/salary/report', [StaffController::class, 'generateSalaryReport'])->name('admin.staff.salary.report');
    Route::get('/staff/salary/export-csv', [StaffController::class, 'exportSalaryCsv'])->name('admin.staff.salary.export_csv');
    Route::post('/staff/salary/calculate', [StaffController::class, 'calculateSalaryAjax'])->name('admin.staff.salary.calculate');
    // Staff Routes End
    // Exam
       Route::get('marks-entry', [ExamManagementController::class, 'marksEntry'])->name('exam_management.marks_entry');
    Route::post('marks-entry', [ExamManagementController::class, 'saveMarks'])->name('exam_management.save_marks');
    Route::get('grading-scale', [ExamManagementController::class, 'gradingScale'])->name('exam_management.grading_scale');
    Route::post('grading-scale', [ExamManagementController::class, 'saveGradingScale'])->name('exam_management.save_grading_scale');
    Route::get('exams', [ExamManagementController::class, 'exams'])->name('exam_management.exams');
    Route::post('exams', [ExamManagementController::class, 'saveExam'])->name('exam_management.save_exam');
    Route::get('report-cards', [ExamManagementController::class, 'reportCards'])->name('exam_management.report_cards');
    Route::get('report-card/{student_id}/{exam_id}', [ExamManagementController::class, 'reportCard'])->name('exam_management.report_card');
    Route::get('report-card-print/{student_id}/{exam_id}', [ExamManagementController::class, 'reportCardPrint'])->name('exam_management.report_card_print');

    //

    // Accounts Routes
    Route::get('accounts/income', [AccountsController::class, 'income'])->name('accounts.income');
    Route::post('accounts/income', [AccountsController::class, 'saveIncome'])->name('accounts.save_income');
        Route::get('accounts/income/delete/{id}', [AccountsController::class, 'income_delete'])->name('accounts.income.delete');

    Route::get('accounts/expense', [AccountsController::class, 'expense'])->name('accounts.expense');
    Route::get('accounts/expense/delete/{id}', [AccountsController::class, 'expense_delete'])->name('accounts.expense.delete');
    Route::post('accounts/expense', [AccountsController::class, 'saveExpense'])->name('accounts.save_expense');
    Route::get('accounts/daybook', [AccountsController::class, 'daybook'])->name('accounts.daybook');
    Route::get('accounts/daybook/export/csv', [AccountsController::class, 'exportDaybookCsv'])->name('accounts.daybook.export_csv');
    Route::get('accounts/daybook/export/pdf', [AccountsController::class, 'exportDaybookPdf'])->name('accounts.daybook.export_pdf');

    // Inventary Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('admin.inventory.store');
    Route::get('/inventory/{inventoryItem}/edit', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
    Route::put('/inventory/{inventoryItem}', [InventoryController::class, 'update'])->name('admin.inventory.update');
    Route::delete('/inventory/{inventoryItem}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
    Route::get('/inventory/export_csv', [InventoryController::class, 'exportCsv'])->name('admin.inventory.export_csv');

    // Stock Routes
    Route::get('/stock', [InventoryController::class, 'stock_index'])->name('admin.stock.index');
    Route::post('/stock/store', [InventoryController::class, 'stock_store'])->name('admin.stock.store');
});

    // Route::get('public_result'   , [ExamManagementController::class, 'resultPublic'])->name('public_result');




// LIBRARY START

Route::any('/library/books', function () {
    return view('/library/books');
});
Route::any('admin/library/add', [App\Http\Controllers\HomeController::class,'library_add']);
Route::any('admin/library/view', [App\Http\Controllers\HomeController::class,'library_view']);
Route::any('/admin/library/issue_book', [App\Http\Controllers\HomeController::class, 'library_issue_book']);
Route::get('admin/library/categories', [App\Http\Controllers\Admin\LibraryCategoryController::class, 'index']);
Route::post('admin/library/categories', [App\Http\Controllers\Admin\LibraryCategoryController::class, 'store']);
Route::get('admin/library/categories/edit/{id}', [App\Http\Controllers\Admin\LibraryCategoryController::class, 'edit']);
Route::post('admin/library/categories/edit/{id}', [App\Http\Controllers\Admin\LibraryCategoryController::class, 'editStore']);



 Route::get('admin/library/rack', [App\Http\Controllers\Admin\LibraryRackController::class, 'index']);
Route::post('admin/library/rack', [App\Http\Controllers\Admin\LibraryRackController::class, 'store']);
Route::get('admin/library/rack/edit/{id}', [App\Http\Controllers\Admin\LibraryRackController::class, 'edit']);
Route::post('admin/library/rack/edit/{id}', [App\Http\Controllers\Admin\LibraryRackController::class, 'editStore']);

    //

 Route::get('admin/library/publisher', [App\Http\Controllers\Admin\LibraryPublisherController::class, 'index']);
Route::post('admin/library/publisher', [App\Http\Controllers\Admin\LibraryPublisherController::class, 'store']);
Route::get('admin/library/publisher/edit/{id}', [App\Http\Controllers\Admin\LibraryPublisherController::class, 'edit']);
Route::post('admin/library/publisher/edit/{id}', [App\Http\Controllers\Admin\LibraryPublisherController::class, 'editStore']);


 Route::get('admin/library/author', [App\Http\Controllers\Admin\LibraryAuthorController::class, 'index']);
Route::post('admin/library/author', [App\Http\Controllers\Admin\LibraryAuthorController::class, 'store']);
Route::get('admin/library/author/edit/{id}', [App\Http\Controllers\Admin\LibraryAuthorController::class, 'edit']);
Route::post('admin/library/author/edit/{id}', [App\Http\Controllers\Admin\LibraryAuthorController::class, 'editStore']);


 Route::get('admin/library/book', [App\Http\Controllers\Admin\LibraryBookController::class, 'index']);
 Route::get('admin/library/book_add', [App\Http\Controllers\Admin\LibraryBookController::class, 'add']);
 Route::post('admin/library/book_add', [App\Http\Controllers\Admin\LibraryBookController::class, 'store']);
 Route::get('admin/library/book/edit/{id}', [App\Http\Controllers\Admin\LibraryBookController::class, 'edit']);
 Route::post('admin/library/book/edit/{id}', [App\Http\Controllers\Admin\LibraryBookController::class, 'editSave']);
        Route::any('/admin/library/borrower', [App\Http\Controllers\HomeController::class, 'borrower'])->name('borrower');


        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('users');
        Route::post('/admin/users', [UserManagementController::class, 'storeUser'])->name('users.store');
        Route::post('/admin/users/{user}', [UserManagementController::class, 'updateUser'])->name('users.update');
        Route::post('/admin/users/{user}/delete', [UserManagementController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/admin/roles', [UserManagementController::class, 'storeRole'])->name('roles.store');
        Route::post('/admin/roles/{role}', [UserManagementController::class, 'updateRole'])->name('roles.update');
        Route::get('/admin/activity-logs', [App\Http\Controllers\HomeController::class, 'activity_logs'])->name('admin.activity_logs');


if (!function_exists('get_book_cat')) {
 function get_book_cat($id){
$ger=DB::select("select * from library_books where id='$id'");
        if(count($ger)>0){
        foreach($ger as $ger2){
        $cat_id=$ger2->library_categories_id;
                $get_cat_name=DB::Select("select * from library_categories where id='$cat_id' ");
                if(count($get_cat_name)>0){
                foreach($get_cat_name as $get_cat_name2){
                return $get_cat_name2->name;
                }
                }
        }
        }

}
 function get_book($id){
$ger=DB::select("select * from library_books where id='$id'");
        if(count($ger)>0){
        foreach($ger as $ger2){
 return $ger2->BookTitle;
        }
        }

}
function Get_borrower_id($id){
                $ger=DB::select("SELECT * FROM `borrower` where students_id='$id' ");
        if(count($ger)>0){
        foreach($ger as $ger2){
 return $ger2->borrower_id ;
        }
        }
           }
 function get_student2($id){
$ger=DB::select("select * from students where id='$id'");
        if(count($ger)>0){
        foreach($ger as $ger2){
 return $ger2->first_name.' '.$ger2->last_name;
        }
        }

}

 function get_student($id){
$ger=DB::select("select * from students where id='$id'");
        if(count($ger)>0){
        foreach($ger as $ger2){
 return $ger2->first_name.' '.$ger2->last_name;
        }
        }

}
//LIBRARY END

function success($val="Opreation is completed successfully!") {
?>
<div class="alert alert-success"><b>Success!</b> <?php echo $val; ?> </div>
<?php
}
}

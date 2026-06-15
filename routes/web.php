<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuardianPortalController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Tenant;
use App\Http\Controllers\TestAttemptController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Brand kit (logos + palette).
Route::view('brand', 'brand')->name('brand');

// Public, token-based parent portal (read-only).
Route::get('p/{token}', [GuardianPortalController::class, 'show'])->name('portal.show');
Route::post('p/{token}/reminders', [GuardianPortalController::class, 'toggleReminders'])->name('portal.reminders');

// Public, token-based online test taking.
Route::get('t/{token}', [TestAttemptController::class, 'show'])->name('test.show');
Route::post('t/{token}/start', [TestAttemptController::class, 'start'])->name('test.start');
Route::get('t/{token}/a/{attempt}', [TestAttemptController::class, 'take'])->name('test.take');
Route::post('t/{token}/a/{attempt}', [TestAttemptController::class, 'submit'])->name('test.submit');
Route::get('t/{token}/a/{attempt}/result', [TestAttemptController::class, 'result'])->name('test.result');

Route::put('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

/*
 * Guest authentication routes.
 */
Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

/*
 * Authenticated routes.
 */
Route::middleware('auth')->group(function (): void {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    /*
     * Super admin area — manages tenants, plans and subscriptions.
     */
    Route::middleware('role:super_admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function (): void {
            Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');
            Route::get('reports', Admin\ReportController::class)->name('reports');

            Route::get('subscription-payments', [Admin\SubscriptionPaymentController::class, 'index'])->name('subscription-payments.index');
            Route::post('subscription-payments/{payment}/approve', [Admin\SubscriptionPaymentController::class, 'approve'])->name('subscription-payments.approve');
            Route::post('subscription-payments/{payment}/reject', [Admin\SubscriptionPaymentController::class, 'reject'])->name('subscription-payments.reject');

            Route::resource('clients', Admin\ClientController::class);
            Route::put('clients/{client}/whatsapp', [Admin\WhatsAppController::class, 'update'])->name('clients.whatsapp.update');
            Route::resource('plans', Admin\PlanController::class)->except('show');

            Route::post('subscriptions/{subscription}/renew', [Admin\SubscriptionController::class, 'renew'])
                ->name('subscriptions.renew');
            Route::post('subscriptions/{subscription}/cancel', [Admin\SubscriptionController::class, 'cancel'])
                ->name('subscriptions.cancel');
            Route::resource('subscriptions', Admin\SubscriptionController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);
        });

    /*
     * Tenant area — client admins and their users.
     */
    Route::middleware('role:client_admin,client_user')
        ->prefix('app')
        ->name('tenant.')
        ->group(function (): void {
            Route::get('subscription/inactive', [Tenant\SubscriptionController::class, 'inactive'])
                ->name('subscription.inactive');

            // Billing is reachable even when the subscription is inactive, so a
            // center admin can pay to (re)activate. Limited to center admins.
            Route::middleware('role:client_admin')->group(function (): void {
                Route::get('billing', [Tenant\BillingController::class, 'index'])->name('billing.index');
                Route::post('billing', [Tenant\BillingController::class, 'store'])->name('billing.store');
                Route::get('billing/{payment}/receipt', [Tenant\BillingController::class, 'receipt'])->name('billing.receipt');
            });

            Route::middleware('tenant.active')->group(function (): void {
                Route::get('dashboard', Tenant\DashboardController::class)->name('dashboard');
                Route::get('search', Tenant\SearchController::class)->name('search');
                Route::get('subscription', [Tenant\SubscriptionController::class, 'index'])->name('subscription.index');

                // Core tutoring management — available on every plan.
                Route::resource('subjects', Tenant\SubjectController::class)->except('show')->middleware('permission:subjects');
                Route::resource('teachers', Tenant\TeacherController::class)->except('show')->middleware('permission:teachers');

                Route::middleware('permission:students')->group(function (): void {
                    Route::get('students/import', [Tenant\StudentController::class, 'importForm'])->name('students.import');
                    Route::post('students/import', [Tenant\StudentController::class, 'import'])->name('students.import.store');
                    Route::get('students/import/template', [Tenant\StudentController::class, 'importTemplate'])->name('students.import.template');
                    Route::resource('students', Tenant\StudentController::class);
                    Route::post('students/{student}/portal/regenerate', [Tenant\StudentController::class, 'regeneratePortal'])->name('students.portal.regenerate');
                    Route::get('exports/students', [Tenant\ExportController::class, 'students'])->name('exports.students');
                });

                Route::middleware('permission:groups')->group(function (): void {
                    Route::resource('groups', Tenant\GroupController::class);
                    Route::post('groups/{group}/students', [Tenant\EnrollmentController::class, 'store'])->name('groups.students.store');
                    Route::delete('groups/{group}/students/{enrollment}', [Tenant\EnrollmentController::class, 'destroy'])->name('groups.students.destroy');
                });

                // Attendance.
                Route::middleware(['feature:attendance', 'permission:attendance'])->group(function (): void {
                    Route::get('groups/{group}/attendance', [Tenant\AttendanceController::class, 'create'])->name('groups.attendance.create');
                    Route::post('groups/{group}/attendance', [Tenant\AttendanceController::class, 'store'])->name('groups.attendance.store');
                    Route::get('groups/{group}/attendance/scan', [Tenant\AttendanceController::class, 'scan'])->name('groups.attendance.scan');
                    Route::post('groups/{group}/attendance/scan', [Tenant\AttendanceController::class, 'scanStore'])->name('groups.attendance.scan.store');
                    Route::get('attendance/{session}', [Tenant\AttendanceController::class, 'show'])->name('attendance.show');
                    Route::get('qr-cards', [Tenant\StudentController::class, 'qrCards'])->name('attendance.cards');
                    Route::get('students/{student}/card', [Tenant\StudentController::class, 'card'])->name('students.card');
                });

                // Exams & grades.
                Route::middleware(['feature:exams', 'permission:exams'])->group(function (): void {
                    Route::post('groups/{group}/exams', [Tenant\ExamController::class, 'store'])->name('exams.store');
                    Route::get('exams/{exam}', [Tenant\ExamController::class, 'show'])->name('exams.show');
                    Route::delete('exams/{exam}', [Tenant\ExamController::class, 'destroy'])->name('exams.destroy');
                    Route::post('exams/{exam}/grades', [Tenant\GradeController::class, 'store'])->name('grades.store');
                });

                // Timetable.
                Route::middleware(['feature:timetable', 'permission:timetable'])->group(function (): void {
                    Route::get('timetable', [Tenant\TimetableController::class, 'index'])->name('timetable.index');
                    Route::post('groups/{group}/timetable', [Tenant\TimetableController::class, 'store'])->name('timetable.store');
                    Route::delete('timetable/{slot}', [Tenant\TimetableController::class, 'destroy'])->name('timetable.destroy');
                });

                // Payments.
                Route::middleware(['feature:payments', 'permission:payments'])->group(function (): void {
                    Route::resource('payments', Tenant\PaymentController::class)->only(['index', 'create', 'store', 'destroy']);
                    Route::get('payments/{payment}/receipt', [Tenant\PaymentController::class, 'receipt'])->name('payments.receipt');
                    Route::get('exports/payments', [Tenant\ExportController::class, 'payments'])->name('exports.payments');

                    Route::post('charges', [Tenant\ChargeController::class, 'store'])->name('charges.store');
                    Route::delete('charges/{charge}', [Tenant\ChargeController::class, 'destroy'])->name('charges.destroy');
                });

                // Expenses.
                Route::middleware(['feature:expenses', 'permission:expenses'])->group(function (): void {
                    Route::resource('expenses', Tenant\ExpenseController::class)->except('show');
                });

                // Online tests.
                Route::middleware(['feature:online_tests', 'permission:online_tests'])->group(function (): void {
                    Route::resource('tests', Tenant\TestController::class);
                    Route::post('tests/{test}/publish', [Tenant\TestController::class, 'togglePublish'])->name('tests.publish');
                    Route::post('tests/{test}/questions', [Tenant\QuestionController::class, 'store'])->name('tests.questions.store');
                    Route::delete('tests/{test}/questions/{question}', [Tenant\QuestionController::class, 'destroy'])->name('tests.questions.destroy');
                });

                // Reports.
                Route::middleware(['feature:reports', 'permission:reports'])->group(function (): void {
                    Route::get('reports', [Tenant\ReportController::class, 'index'])->name('reports.index');
                    Route::get('reports/financial', [Tenant\ReportController::class, 'financial'])->name('reports.financial');
                    Route::get('reports/attendance', [Tenant\ReportController::class, 'attendance'])->name('reports.attendance');
                    Route::get('reports/collection', [Tenant\ReportController::class, 'collection'])->name('reports.collection');
                    Route::get('reports/payroll', [Tenant\ReportController::class, 'payroll'])->name('reports.payroll');
                });

                // SMS reminders + outbox.
                Route::middleware(['feature:messages', 'permission:messages'])->group(function (): void {
                    Route::post('attendance/{session}/remind', [Tenant\ReminderController::class, 'absence'])->name('reminders.absence');
                    Route::post('reminders/payment', [Tenant\ReminderController::class, 'payment'])->name('reminders.payment');
                    Route::get('messages', [Tenant\MessageController::class, 'index'])->name('messages.index');
                });

                // User management is limited to center admins.
                Route::middleware('role:client_admin')->group(function (): void {
                    Route::resource('roles', Tenant\RoleController::class)->except('show');
                    Route::resource('users', Tenant\UserController::class)->except('show');

                    // WhatsApp number linking (QR pairing) for sending reminders.
                    Route::middleware('feature:whatsapp')->group(function (): void {
                        Route::get('whatsapp', [Tenant\WhatsAppController::class, 'show'])->name('whatsapp.show');
                        Route::get('whatsapp/qr', [Tenant\WhatsAppController::class, 'qr'])->name('whatsapp.qr');
                    });

                    Route::get('settings', [Tenant\SettingsController::class, 'edit'])->name('settings.edit');
                    Route::put('settings', [Tenant\SettingsController::class, 'update'])->name('settings.update');
                    Route::get('activity', [Tenant\ActivityController::class, 'index'])
                        ->middleware('feature:activity')->name('activity.index');
                });
            });
        });
});

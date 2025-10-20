<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ZATCA Invoice QR - Arabic Language File
 */

// Module Information
$lang['zatca_qr_module_name'] = 'رمز الاستجابة السريعة للفاتورة الإلكترونية';
$lang['zatca_qr_module_description'] = 'إنشاء رموز QR متوافقة مع هيئة الزكاة والضريبة والجمارك';

// Settings
$lang['zatca_settings'] = 'إعدادات الفاتورة الإلكترونية';
$lang['zatca_module_enabled'] = 'تفعيل الوحدة';
$lang['zatca_phase'] = 'المرحلة';
$lang['zatca_phase1'] = 'المرحلة الأولى (مبسطة)';
$lang['zatca_phase2'] = 'المرحلة الثانية (موقعة)';
$lang['zatca_environment'] = 'البيئة';
$lang['zatca_sandbox'] = 'بيئة الاختبار';
$lang['zatca_production'] = 'بيئة الإنتاج';

// Seller Information
$lang['zatca_seller_information'] = 'معلومات البائع';
$lang['zatca_seller_name'] = 'اسم البائع/الشركة';
$lang['zatca_seller_name_help'] = 'اسم شركتك أو نشاطك التجاري كما هو مسجل';
$lang['zatca_vat_number'] = 'الرقم الضريبي';
$lang['zatca_vat_number_help'] = 'الرقم الضريبي المكون من 15 رقماً';
$lang['zatca_company_address'] = 'عنوان الشركة';
$lang['zatca_company_address_help'] = 'عنوان الشركة الكامل (مطلوب للمرحلة الثانية)';

// QR Code Settings
$lang['zatca_qr_settings'] = 'إعدادات رمز QR';
$lang['zatca_qr_position'] = 'موضع رمز QR في ملف PDF';
$lang['zatca_qr_position_top_right'] = 'أعلى اليمين';
$lang['zatca_qr_position_top_left'] = 'أعلى اليسار';
$lang['zatca_qr_position_bottom_right'] = 'أسفل اليمين';
$lang['zatca_qr_position_bottom_left'] = 'أسفل اليسار';
$lang['zatca_qr_size'] = 'حجم رمز QR (بكسل)';
$lang['zatca_qr_size_help'] = 'حجم رمز QR في الفاتورة (الموصى به: 150-200 بكسل)';
$lang['zatca_auto_generate'] = 'إنشاء تلقائي لرموز QR';
$lang['zatca_auto_generate_help'] = 'إنشاء رموز QR تلقائياً عند إنشاء أو تحديث الفواتير';

// Actions
$lang['zatca_save_settings'] = 'حفظ الإعدادات';
$lang['zatca_test_qr'] = 'اختبار إنشاء رمز QR';
$lang['zatca_batch_generate'] = 'إنشاء رموز QR دفعة واحدة';
$lang['zatca_regenerate_qr'] = 'إعادة إنشاء رمز QR';
$lang['zatca_delete_qr'] = 'حذف رمز QR';
$lang['zatca_view_qr'] = 'عرض تفاصيل رمز QR';

// Statistics
$lang['zatca_statistics'] = 'الإحصائيات';
$lang['zatca_total_invoices'] = 'إجمالي الفواتير';
$lang['zatca_invoices_with_qr'] = 'الفواتير بها رمز QR';
$lang['zatca_invoices_without_qr'] = 'الفواتير بدون رمز QR';
$lang['zatca_failed_generations'] = 'محاولات فاشلة';
$lang['zatca_success_rate'] = 'معدل النجاح';

// Messages
$lang['zatca_settings_saved'] = 'تم حفظ الإعدادات بنجاح';
$lang['zatca_settings_not_saved'] = 'فشل في حفظ الإعدادات';
$lang['zatca_qr_generated'] = 'تم إنشاء رمز QR بنجاح';
$lang['zatca_qr_regenerated'] = 'تم إعادة إنشاء رمز QR بنجاح';
$lang['zatca_qr_regeneration_failed'] = 'فشل في إعادة إنشاء رمز QR';
$lang['zatca_qr_deleted'] = 'تم حذف رمز QR بنجاح';
$lang['zatca_qr_delete_failed'] = 'فشل في حذف رمز QR';
$lang['zatca_batch_generated'] = 'تم إنشاء %d رمز QR بنجاح، %d فشل';
$lang['zatca_no_invoices_to_generate'] = 'لا توجد فواتير بدون رموز QR';

// Errors
$lang['zatca_module_not_configured'] = 'الوحدة غير مُعدّة بشكل صحيح';
$lang['zatca_module_disabled'] = 'الوحدة معطلة حالياً';
$lang['zatca_invalid_vat_number'] = 'صيغة الرقم الضريبي غير صحيحة. يجب أن يكون 15 رقماً';
$lang['zatca_missing_seller_name'] = 'اسم البائع مطلوب';
$lang['zatca_missing_vat_number'] = 'الرقم الضريبي مطلوب';
$lang['zatca_invoice_not_found'] = 'الفاتورة غير موجودة';
$lang['zatca_qr_generation_failed'] = 'فشل إنشاء رمز QR';

// QR Code Details
$lang['zatca_qr_code'] = 'رمز QR للفاتورة الإلكترونية';
$lang['zatca_qr_data'] = 'بيانات رمز QR (Base64)';
$lang['zatca_qr_decoded'] = 'البيانات المفكّكة';
$lang['zatca_qr_tag1'] = 'العلامة 1: اسم البائع';
$lang['zatca_qr_tag2'] = 'العلامة 2: الرقم الضريبي';
$lang['zatca_qr_tag3'] = 'العلامة 3: تاريخ ووقت الفاتورة';
$lang['zatca_qr_tag4'] = 'العلامة 4: إجمالي الفاتورة';
$lang['zatca_qr_tag5'] = 'العلامة 5: مبلغ الضريبة';
$lang['zatca_qr_tag6'] = 'العلامة 6: تجزئة الفاتورة';
$lang['zatca_qr_tag7'] = 'العلامة 7: التوقيع الرقمي';
$lang['zatca_qr_tag8'] = 'العلامة 8: المفتاح العام';
$lang['zatca_qr_tag9'] = 'العلامة 9: توقيع الشهادة';

// Status
$lang['zatca_status_pending'] = 'قيد الانتظار';
$lang['zatca_status_generated'] = 'تم الإنشاء';
$lang['zatca_status_failed'] = 'فشل';

// Configuration
$lang['zatca_configuration_status'] = 'حالة الإعداد';
$lang['zatca_configured'] = 'مُعدّ';
$lang['zatca_not_configured'] = 'غير مُعدّ';
$lang['zatca_missing_fields'] = 'حقول مطلوبة مفقودة';

// Help Text
$lang['zatca_help_phase1'] = 'المرحلة الأولى تنشئ رموز QR مبسطة بـ 5 حقول أساسية';
$lang['zatca_help_phase2'] = 'المرحلة الثانية تنشئ رموز QR موقعة بتوقيعات رقمية (تتطلب شهادة)';
$lang['zatca_help_batch'] = 'إنشاء رموز QR لجميع الفواتير الموجودة التي لا تحتوي عليها';
$lang['zatca_help_auto_generate'] = 'عند التفعيل، سيتم إنشاء رموز QR تلقائياً للفواتير الجديدة والمحدثة';

// Batch Operations
$lang['zatca_batch_limit'] = 'عدد الفواتير المراد معالجتها';
$lang['zatca_batch_processing'] = 'جارٍ المعالجة...';
$lang['zatca_batch_complete'] = 'اكتمل الإنشاء الدفعي';

// Tooltips
$lang['zatca_tooltip_test'] = 'اختبار إنشاء رمز QR بالإعدادات الحالية';
$lang['zatca_tooltip_batch'] = 'إنشاء رموز QR للفواتير التي لا تحتوي عليها';
$lang['zatca_tooltip_regenerate'] = 'إعادة إنشاء رمز QR لهذه الفاتورة';
$lang['zatca_tooltip_view'] = 'عرض تفاصيل رمز QR والبيانات المفكّكة';

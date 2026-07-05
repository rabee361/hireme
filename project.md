<div dir="rtl" align="right">

# شرح مشروع Hiremee

## مقدمة سريعة

مشروع **Hiremee** هو فكرة منصة تجمع الأطراف المرتبطة بسوق الفرص والعمل داخل مساحة واحدة منظّمة، بحيث يصبح الوصول إلى الفرص، عرض الاحتياجات، والتواصل بين الجهات المختلفة أكثر وضوحًا وسهولة.

يركّز المشروع على بناء بيئة تخدم أكثر من نوع من المستخدمين، مثل الشركات والأفراد والطلاب، مع منح كل فئة مساحة مناسبة للتفاعل بحسب دورها، سواء في نشر الفرص، متابعة الطلبات، أو إدارة البيانات المرتبطة بها.

الفكرة الأساسية في **Hiremee** لا تقوم فقط على عرض فرص أو خدمات، بل على تنظيم العلاقة بين المستخدمين بطريقة تساعد على تقليل التشتت، تسهيل الوصول، وخلق تجربة موحّدة تجعل إدارة الفرص والمتابعة واتخاذ القرار أكثر سلاسة.

---

## أولًا: شرح التقنيات المستخدمة في المشروع

### 1) PHP

لغة **PHP** هي اللغة الأساسية التي تم بناء منطق المشروع بها. داخل هذا المشروع، PHP ليست فقط لغة لكتابة صفحات ويب، بل هي الأساس الذي يشغّل:

- منطق تسجيل الدخول والتسجيل والتحقق.
- التعامل مع قاعدة البيانات.
- تعريف المسارات Routes.
- تنفيذ الخدمات Services.
- تشغيل الـ Jobs في الطوابير Queue.
- إدارة الإشعارات والبريد الإلكتروني وFirebase.

كيف ظهرت PHP في المشروع؟

- جميع ملفات Laravel الأساسية مكتوبة بـ PHP.
- مجلد [app](app) يحتوي على أغلب منطق النظام.
- ملفات [routes/api.php](routes/api.php) و[routes/web.php](routes/web.php) مكتوبة بـ PHP.
- ملفات الهجرات داخل [database/migrations](database/migrations) مكتوبة بـ PHP وتبني الجداول.

بمعنى أبسط: PHP هنا هي المحرك الرئيسي الذي يدير كل شيء من الخلف.

---

### 2) Laravel

**Laravel** هو إطار العمل الأساسي للمشروع، وهو الذي رتّب المشروع إلى أقسام واضحة مثل Controllers وModels وRequests وJobs وServices وRoutes.

من خلال ملفات المشروع يمكننا معرفة أن النظام يعتمد على Laravel 12، وهذا واضح من ملف [composer.json](composer.json).

كيف استخدمنا Laravel في المشروع؟

- استخدمناه لبناء **REST API** في [routes/api.php](routes/api.php).
- استخدمناه لبناء **لوحة تحكم إدارية** عبر المسارات في [routes/web.php](routes/web.php).
- استخدمناه لإدارة قاعدة البيانات عبر **Migrations** و**Seeders**.
- استخدمناه للتحقق من البيانات عبر **Form Requests** داخل [app/Http/Requests](app/Http/Requests).
- استخدمناه لإدارة المصادقة Authentication.
- استخدمناه لتشغيل الإشعارات والمهام المؤجلة عبر **Jobs**.

أمثلة واضحة من المشروع:

- توجد مسارات API للمصادقة والإعلانات والمشاريع والمستخدمين.
- توجد لوحة تحكم تبدأ من المسار `/dashboard`.
- توجد نماذج Models مثل [app/Models/User.php](app/Models/User.php) و[app/Models/Project.php](app/Models/Project.php) و[app/Models/Ad.php](app/Models/Ad.php).
- توجد وظائف مؤجلة مثل [app/Jobs/SendPushNotificationJob.php](app/Jobs/SendPushNotificationJob.php) لإرسال الإشعارات.

Laravel هنا أعطى المشروع:

- تنظيمًا واضحًا.
- سرعة في التطوير.
- حماية أفضل.
- سهولة في ربط قاعدة البيانات.
- سهولة في توسيع النظام لاحقًا.

---

### 3) HTML

**HTML** هي الطبقة الأساسية لبناء شكل الصفحات داخل لوحة التحكم والواجهات المعروضة في المتصفح.

في هذا المشروع، HTML لا تظهر عادة كملفات خام فقط، بل تظهر غالبًا داخل ملفات **Blade** مثل:

- [resources/views/layouts/dashboard.blade.php](resources/views/layouts/dashboard.blade.php)
- [resources/views/livewire/dashboard/home-page.blade.php](resources/views/livewire/dashboard/home-page.blade.php)
- [resources/views/partials/head.blade.php](resources/views/partials/head.blade.php)

كيف استخدمنا HTML؟

- لبناء هيكل الصفحة.
- لتقسيم الأقسام مثل الشريط الجانبي، الشريط العلوي، الجداول، البطاقات، والنماذج.
- لعرض الإحصائيات داخل لوحة التحكم.
- لإنشاء عناصر الإدخال مثل `input` و`select` و`button`.

مثال واضح:

ملف [resources/views/layouts/dashboard.blade.php](resources/views/layouts/dashboard.blade.php) يحدد هيكل صفحة لوحة التحكم، ويضبط اتجاه الصفحة إلى العربية من خلال `dir="rtl"`.

---

### 4) CSS

**CSS** مسؤولة عن تنسيق الواجهة، الألوان، المسافات، الخطوط، الاستجابة للشاشات المختلفة، والمظهر العام.

في هذا المشروع، CSS مرتبطة بشكل كبير مع **Tailwind CSS**، وهذا واضح من ملف [package.json](package.json) وملف [resources/css/app.css](resources/css/app.css).

كيف استخدمنا CSS؟

- لتنسيق لوحة التحكم.
- لبناء هوية بصرية للمشروع.
- لدعم الوضع الفاتح والوضع الداكن.
- لتحسين عرض البطاقات والجداول والنماذج.
- لجعل التصميم مناسبًا للعربية والاتجاه من اليمين إلى اليسار.

أمثلة من المشروع:

- تم تعريف ألوان مخصصة للمشروع داخل [resources/css/app.css](resources/css/app.css).
- تم استخدام خط **Cairo** لدعم العربية بشكل جميل وواضح.
- توجد أصناف مخصصة مثل `dashboard-shell` و`dashboard-panel` و`dashboard-table`.

هذا يعني أن CSS لم تُستخدم فقط للتجميل، بل لتنظيم تجربة المستخدم داخل لوحة التحكم.

---

### 5) JavaScript

**JavaScript** استُخدمت في المشروع لإضافة السلوك التفاعلي داخل الواجهة، خصوصًا في لوحة التحكم.

كيف استخدمنا JavaScript؟

- لتشغيل الرسوم البيانية.
- للتعامل مع تبديل الثيم بين الفاتح والداكن.
- لإعادة تهيئة الرسوم البيانية بعد التنقل داخل Livewire.

أهم ملف يوضح ذلك هو [resources/js/app.js](resources/js/app.js).

من خلال هذا الملف نلاحظ:

- استخدام مكتبة **Chart.js** لرسم الإحصائيات.
- قراءة البيانات من عناصر الصفحة ثم تحويلها إلى رسوم بيانية.
- إعادة بناء الرسوم عند تحميل الصفحة أو عند التنقل داخل Livewire.

وهذا يعني أن JavaScript هنا لم تُستخدم لبناء تطبيق كامل من الصفر، بل لدعم التفاعل داخل واجهة Laravel وLivewire.

---

### 6) Livewire

**Livewire** هي من أهم التقنيات في هذا المشروع، لأنها سمحت ببناء صفحات تفاعلية في لوحة التحكم بدون الحاجة إلى كتابة كمية كبيرة من JavaScript اليدوي.

من ملف [composer.json](composer.json) يتضح أن المشروع يستخدم **Livewire 4**.

كيف استخدمنا Livewire؟

- لبناء صفحات لوحة التحكم كمكوّنات تفاعلية.
- لتحديث البيانات مباشرة بدون إعادة تحميل الصفحة بالكامل.
- لربط الواجهة بالمنطق الخلفي داخل مكوّن واحد.

أمثلة من المشروع:

- [app/Livewire/Dashboard/HomePage.php](app/Livewire/Dashboard/HomePage.php)
- [resources/views/livewire/dashboard/home-page.blade.php](resources/views/livewire/dashboard/home-page.blade.php)

في صفحة HomePage مثلًا:

- يتم حساب عدد الشركات والعملاء والطلاب.
- يتم تحديد فترة زمنية مثل آخر أسبوع أو آخر شهر.
- يتم تجهيز بيانات الرسوم البيانية.
- ثم تُرسل هذه البيانات إلى الواجهة لعرضها مباشرة.

ميزة Livewire في هذا المشروع أنها جعلت لوحة التحكم:

- أسرع في التطوير.
- أسهل في الصيانة.
- أقل اعتمادًا على JavaScript المعقد.

---

### 7) Firebase

**Firebase** استُخدمت هنا بشكل أساسي في **الإشعارات الفورية Push Notifications**.

من الأدلة الموجودة في المشروع:

- وجود الحزمة `kreait/firebase-php` في [composer.json](composer.json).
- وجود إعدادات Firebase في [config/services.php](config/services.php).
- وجود ملف مفاتيح خدمة Firebase داخل المشروع.
- وجود جداول خاصة بتخزين رموز الأجهزة والإشعارات في [database/migrations/2026_05_15_000100_create_push_notifications_tables.php](database/migrations/2026_05_15_000100_create_push_notifications_tables.php).
- وجود Job للإرسال في [app/Jobs/SendPushNotificationJob.php](app/Jobs/SendPushNotificationJob.php).

كيف استخدمنا Firebase عمليًا؟

- نخزّن **Device Tokens** لكل مستخدم.
- عند إنشاء إشعار جديد، يتم تحديد الجمهور المستهدف.
- يتم إرسال الإشعار إلى أجهزة معينة أو إلى Topic معين.
- إذا كان بعض الـ Tokens غير صالح، يتم تعطيله داخل قاعدة البيانات.

هذه الفكرة مهمة جدًا في المشاريع التي تحتوي على تطبيق موبايل أو تحتاج إشعارات لحظية للمستخدمين.

إذن Firebase هنا لم تُستخدم كقاعدة بيانات، بل كخدمة لإرسال الإشعارات إلى الأجهزة.

---

### 8) JWT

المشروع يستخدم **JWT** للمصادقة داخل الـ API، وهذا واضح من وجود الحزمة `php-open-source-saver/jwt-auth` في [composer.json](composer.json).

كيف استخدمناه؟

- عند تسجيل الدخول عبر الـ API يتم إنشاء Token.
- هذا الـ Token يُستخدم للوصول إلى المسارات المحمية.
- توجد مسارات داخل [routes/api.php](routes/api.php) محمية عبر `auth:api` و`jwt.access`.

هذا مناسب جدًا عندما يكون عندنا:

- تطبيق موبايل.
- واجهات API خارجية.
- عميل Frontend منفصل عن الخادم.

---

### 9) Vite

**Vite** هو الأداة المسؤولة عن بناء وتجهيز ملفات الواجهة الأمامية مثل CSS وJavaScript.

من الأدلة:

- [package.json](package.json)
- [vite.config.js](vite.config.js)
- استخدام `@vite` داخل [resources/views/partials/head.blade.php](resources/views/partials/head.blade.php)

كيف استخدمناه؟

- لتجميع ملفات [resources/css/app.css](resources/css/app.css) و[resources/js/app.js](resources/js/app.js).
- لتشغيل بيئة تطوير سريعة عبر `npm run dev`.
- لبناء النسخة النهائية عبر `npm run build`.

فائدة Vite في المشروع:

- سرعة أثناء التطوير.
- تحديث فوري للواجهة.
- تنظيم ملفات الواجهة الأمامية بشكل حديث.

---

### 10) Tailwind CSS

رغم أن المطلوب ذكر CSS، إلا أن من المهم توضيح أن المشروع فعليًا يعتمد على **Tailwind CSS** كطريقة كتابة التنسيقات.

هذا يظهر في:

- [package.json](package.json)
- [resources/css/app.css](resources/css/app.css)

كيف ساعدتنا Tailwind؟

- سرعة بناء الواجهة.
- توحيد التنسيق.
- تقليل الحاجة إلى ملفات CSS طويلة جدًا.
- تسهيل بناء تصميم متجاوب.

---

### 11) Chart.js

المشروع يستخدم **Chart.js** داخل لوحة التحكم لعرض الإحصائيات والرسوم البيانية.

هذا واضح من:

- [package.json](package.json)
- [resources/js/app.js](resources/js/app.js)
- [resources/views/livewire/dashboard/home-page.blade.php](resources/views/livewire/dashboard/home-page.blade.php)

كيف استخدمناه؟

- عرض نمو المستخدمين.
- عرض توزيع أنواع المستخدمين.
- عرض توزيع أنواع الفرص بين الإعلانات والمشاريع.

وهذا ساعد المشروع في إعطاء لوحة التحكم بُعدًا تحليليًا وليس فقط عرض بيانات خام.

---

## ثانيًا: شرح الأدوات التي ساعدتنا في إنجاز المشروع

### 1) Visual Studio Code

**VS Code** هو بيئة التطوير الأساسية التي يمكن من خلالها بناء هذا المشروع وإدارته.

كيف استخدمناه في المشروع؟

- كتابة الكود في PHP وBlade وCSS وJavaScript.
- التنقل بين المجلدات مثل [app](app) و[routes](routes) و[resources](resources) و[database](database).
- تشغيل أوامر Laravel مثل `php artisan migrate` و`php artisan serve`.
- تشغيل أوامر الواجهة مثل `npm install` و`npm run dev`.
- مراجعة الأخطاء وتتبّع الملفات بسرعة.
- إدارة التكامل بين أكثر من تقنية داخل مشروع واحد.

لماذا كان VS Code مناسبًا؟

- خفيف وسريع.
- يدعم Laravel وPHP وBlade وJS بشكل ممتاز.
- يسمح بإدارة المشروع كاملًا من مكان واحد.

---

### 2) Postman

**Postman** أداة مهمة جدًا لاختبار الـ API، والمشروع نفسه يحتوي على ملف [hireme.postman_collection.json](hireme.postman_collection.json)، وهذا دليل واضح على أن Postman دخل ضمن دورة العمل.

كيف استخدمناه؟

- تجربة تسجيل المستخدم.
- تجربة تسجيل الدخول.
- تجربة التحقق من OTP.
- تجربة تحديث الـ Token.
- تجربة جلب الإعلانات والمشاريع.
- تجربة المسارات المحمية.

الفائدة العملية من Postman:

- التأكد من أن كل Endpoint يعمل قبل ربطه بالموبايل أو الواجهة الأمامية.
- فحص شكل الطلبات Requests والردود Responses.
- اختبار الـ Headers مثل Authorization Token.
- اكتشاف أخطاء الـ Validation أو Authentication مبكرًا.

بما أن المشروع يحتوي على API للمصادقة والمحتوى والإشعارات، فإن Postman كان أداة أساسية لإنهاء المشروع بثقة.

---

### 3) DB Visualizer

**DB Visualizer** أو أي أداة مشابهة لإدارة قواعد البيانات ورسم العلاقات كانت مفيدة جدًا في الجزء الخاص بالهيكل البياناتي للمشروع، خصوصًا في استخراج **ERD** أو مخطط العلاقات بين الجداول.

كيف استخدمناه في هذا النوع من المشاريع؟

- الاتصال بقاعدة البيانات بعد تشغيل الـ Migrations.
- استعراض الجداول الناتجة مثل `users` و`student_profiles` و`company_profiles` و`ads` و`projects` و`notifications` و`device_tokens`.
- فهم العلاقات بين الجداول مثل One-to-One وOne-to-Many.
- إنشاء ERD يوضح الربط بين المستخدمين والملفات الشخصية والإعلانات والمشاريع والتقديمات والإشعارات.

لماذا هذا مهم؟

- يساعد في فهم قاعدة البيانات بصريًا.
- يجعل شرح المشروع أسهل أمام الفريق أو المشرف.
- يوضّح أين توجد المفاتيح الأجنبية Foreign Keys.
- يسهّل اكتشاف المشاكل في التصميم أو التكرار.

في هذا المشروع تحديدًا، DB Visualizer يفيد جدًا لأن قاعدة البيانات ليست مجرد جدول مستخدمين فقط، بل تحتوي على أنواع مستخدمين متعددة، ملفات شخصية، فرص، تقديمات، وإشعارات.

---

### 4) Composer

**Composer** هو مدير الحزم الخاص بـ PHP.

كيف استخدمناه؟

- تثبيت Laravel نفسه.
- تثبيت Livewire.
- تثبيت JWT.
- تثبيت Firebase PHP SDK.
- تشغيل سكربتات المشروع مثل `composer run dev` و`composer run setup`.

بدون Composer لن نستطيع إدارة مكتبات PHP داخل المشروع بشكل منظم.

---

### 5) npm

**npm** هو مدير الحزم الخاص بالواجهة الأمامية.

كيف استخدمناه؟

- تثبيت Vite.
- تثبيت Tailwind CSS.
- تثبيت Chart.js.
- تشغيل بيئة التطوير الأمامية.
- بناء ملفات الإنتاج.

وبذلك صار عندنا فصل واضح بين حزم الـ Backend وحزم الـ Frontend.

---

### 6) Artisan

**Artisan** هو سطر أوامر Laravel، وكان من أهم الأدوات العملية أثناء تنفيذ المشروع.

أمثلة على استخدامه:

- `php artisan migrate`
- `php artisan db:seed`
- `php artisan serve`
- `php artisan queue:listen`
- `php artisan key:generate`

كيف ساعدنا؟

- تجهيز قاعدة البيانات.
- إدخال بيانات تجريبية.
- تشغيل السيرفر المحلي.
- تشغيل الطوابير الخاصة بالإشعارات أو الوظائف المؤجلة.

---

### 7) Docker وNginx

وجود ملفات مثل Dockerfile وnginx.conf يدل على أن المشروع يمكن تشغيله داخل بيئة حاويات Containerized Environment أو على خادم منظم.

هذا يفيد في:

- توحيد بيئة التشغيل.
- تقليل مشاكل اختلاف الإعدادات بين الأجهزة.
- تسهيل النشر لاحقًا.

حتى لو لم تكن هذه الأدوات هي محور التطوير اليومي، فهي تدعم المشروع في مرحلة التشغيل أو النشر.

---

## ثالثًا: كيف استخدمنا هذه التقنيات والأدوات لإنهاء المشروع

لفهم الصورة الكاملة، يمكن تلخيص سير العمل بهذا الشكل:

1. بدأنا ببناء **الهيكل الخلفي** باستخدام PHP وLaravel.
2. أنشأنا الجداول والعلاقات باستخدام **Migrations**.
3. بنينا **Models** للتعامل مع البيانات داخل التطبيق.
4. أنشأنا **API** للمصادقة والمحتوى للموبايل أو أي عميل خارجي.
5. أنشأنا **لوحة تحكم** باستخدام Livewire وBlade.
6. استخدمنا HTML وTailwind CSS وJavaScript لجعل لوحة التحكم واضحة وتفاعلية.
7. استخدمنا Firebase لإرسال الإشعارات للأجهزة.
8. استخدمنا Postman لاختبار الـ API.
9. استخدمنا DB Visualizer لفهم قاعدة البيانات واستخراج مخطط ERD.
10. استخدمنا VS Code لإدارة الكود والأوامر والملفات في مكان واحد.

بهذا الشكل، كل تقنية لم تعمل وحدها، بل كانت جزءًا من سلسلة مترابطة حتى يخرج المشروع بصورة عملية وقابلة للاستخدام.

---

## رابعًا: شرح أهم المجلدات الرئيسية في المشروع

### 1) مجلد app

مجلد [app](app) هو **قلب المشروع**، لأنه يحتوي على المنطق البرمجي الأساسي.

أهم ما بداخله:

### app/Http

هذا المجلد مسؤول عن استقبال الطلبات من المستخدم أو من الـ API.

يحتوي على:

- **Controllers** داخل [app/Http/Controllers](app/Http/Controllers): تستقبل الطلبات وتنفذ المنطق المناسب أو تستدعي الخدمات.
- **Requests** داخل [app/Http/Requests](app/Http/Requests): تتحقق من صحة البيانات المدخلة قبل تنفيذ العملية.
- **Middleware** داخل [app/Http/Middleware](app/Http/Middleware): تتحكم في المرور إلى المسارات، مثل التحقق من التوثيق أو الصلاحيات.

فائدته:

- تنظيم التعامل مع الطلبات.
- فصل التحقق من البيانات عن منطق التنفيذ.
- جعل النظام أكثر أمانًا ووضوحًا.

### app/Models

هذا المجلد يحتوي على النماذج التي تمثل جداول قاعدة البيانات.

أمثلة مهمة:

- [app/Models/User.php](app/Models/User.php)
- [app/Models/Student.php](app/Models/Student.php)
- [app/Models/Company.php](app/Models/Company.php)
- [app/Models/Customer.php](app/Models/Customer.php)
- [app/Models/Ad.php](app/Models/Ad.php)
- [app/Models/Project.php](app/Models/Project.php)
- [app/Models/PushNotification.php](app/Models/PushNotification.php)

فائدة الـ Models:

- التعامل مع الجداول بطريقة كائنية.
- تعريف العلاقات بين الجداول.
- تسهيل عمليات القراءة والإضافة والتعديل والحذف.

### app/Livewire

هذا المجلد يحتوي على مكوّنات Livewire المستخدمة في الواجهة التفاعلية.

أقسامه مثل:

- [app/Livewire/Auth](app/Livewire/Auth)
- [app/Livewire/Dashboard](app/Livewire/Dashboard)
- [app/Livewire/Settings](app/Livewire/Settings)

وظيفته:

- بناء صفحات ديناميكية داخل لوحة التحكم.
- ربط الواجهة بالبيانات مباشرة.
- تقليل الحاجة إلى JavaScript كبير ومعقد.

### app/Services

هذا المجلد يحتوي على الخدمات التي تنفذ منطقًا متخصصًا.

من الأمثلة الموجودة:

- [app/Services/Auth](app/Services/Auth)
- [app/Services/Notifications](app/Services/Notifications)
- [app/Services/N8nEmailService.php](app/Services/N8nEmailService.php)

فائدته:

- نقل المنطق المعقد من Controller إلى طبقة منظمة.
- تسهيل إعادة الاستخدام.
- جعل الكود أنظف وأسهل للاختبار.

### app/Jobs

يحتوي على الوظائف المؤجلة التي تعمل في الخلفية، مثل:

- [app/Jobs/SendPushNotificationJob.php](app/Jobs/SendPushNotificationJob.php)

هذا مهم عندما نريد تنفيذ عمليات قد تكون بطيئة أو تحتاج معالجة مستقلة، مثل إرسال إشعارات إلى عدد كبير من المستخدمين.

### app/Mail

هذا المجلد خاص برسائل البريد الإلكتروني، مثل:

- [app/Mail/AuthOtpMail.php](app/Mail/AuthOtpMail.php)

ودوره هو تجهيز وإرسال رسائل مثل رمز التحقق OTP.

### app/Enums

هذا المجلد يحتوي على القيم الثابتة المنظمة، مثل أنواع المستخدمين وأنواع الإشعارات والمنصات.

فائدته:

- تقليل الأخطاء.
- توحيد القيم المستخدمة في المشروع.
- جعل الكود أوضح وأكثر احترافية.

### app/Concerns

هذا المجلد يحتوي على أجزاء مشتركة قابلة لإعادة الاستخدام، مثل قواعد التحقق Validation Rules.

### app/Providers

هذا المجلد خاص بربط الخدمات وتجهيز بعض الإعدادات العامة للتطبيق.

باختصار: مجلد [app](app) هو المكان الذي يعيش فيه منطق المشروع الحقيقي.

---

### 2) مجلد database

مجلد [database](database) مسؤول عن كل ما يتعلق بقاعدة البيانات.

أهم ما بداخله:

### database/migrations

هذا القسم هو الأهم في فهم بنية البيانات. يحتوي على الملفات التي تنشئ الجداول وتضيف الأعمدة والعلاقات.

من أهم ما تكشفه الهجرات الموجودة:

- جدول `users` هو الجدول الأساسي للمستخدمين.
- توجد جداول ملفات شخصية منفصلة مثل `student_profiles` و`customer_profiles` و`company_profiles`.
- توجد جداول للمحتوى مثل `ads` و`projects`.
- توجد جداول للتقديمات مثل `ad_applications` و`project_applications`.
- توجد جداول للإشعارات مثل `notifications` و`push_notifications` و`push_notification_recipients`.
- توجد جداول لربط الأجهزة مثل `device_tokens`.

هذا يعني أن قاعدة البيانات مبنية على فكرة:

- مستخدم أساسي.
- ثم نوع مستخدم.
- ثم ملف شخصي أو بيانات تفصيلية حسب النوع.
- ثم فرص أو مشاريع أو إشعارات مرتبطة بهذا المستخدم.

ملفات مهمة في هذا القسم:

- [database/migrations/2026_04_30_000003_add_user_types_and_profiles.php](database/migrations/2026_04_30_000003_add_user_types_and_profiles.php)
- [database/migrations/2026_04_30_000004_create_ads_projects_notifications_tables.php](database/migrations/2026_04_30_000004_create_ads_projects_notifications_tables.php)
- [database/migrations/2026_05_15_000100_create_push_notifications_tables.php](database/migrations/2026_05_15_000100_create_push_notifications_tables.php)

### database/seeders

هذا المجلد يحتوي على بيانات أولية أو تجريبية تُستخدم لتعبئة قاعدة البيانات بسرعة.

فائدته:

- اختبار النظام.
- عرض بيانات داخل لوحة التحكم.
- تسهيل العمل أثناء التطوير.

### database/factories

هذا المجلد يستخدم لتوليد بيانات وهمية بشكل منظم، وغالبًا يفيد جدًا أثناء الاختبارات والتطوير.

---

### 3) مجلد resources

مجلد [resources](resources) مسؤول عن الجزء المرئي من المشروع.

أهم ما بداخله:

### resources/views

هذا هو مجلد **القوالب Templates** في المشروع.

يحتوي على:

- [resources/views/layouts](resources/views/layouts): التخطيطات العامة للصفحات.
- [resources/views/livewire](resources/views/livewire): الواجهات المرتبطة بمكوّنات Livewire.
- [resources/views/components](resources/views/components): مكوّنات Blade القابلة لإعادة الاستخدام.
- [resources/views/partials](resources/views/partials): أجزاء صغيرة مشتركة مثل head.
- [resources/views/emails](resources/views/emails): قوالب البريد.

لماذا هذا مهم؟

- لأنه يفصل منطق العرض عن منطق الخلفية.
- يجعل الواجهات منظمة.
- يسهل إعادة استخدام العناصر المشتركة.

مثال واضح:

- [resources/views/layouts/dashboard.blade.php](resources/views/layouts/dashboard.blade.php) يمثل الإطار العام للوحة التحكم.
- [resources/views/livewire/dashboard/home-page.blade.php](resources/views/livewire/dashboard/home-page.blade.php) يمثل صفحة الإحصائيات داخل لوحة التحكم.

### resources/css

يحتوي على ملف CSS الرئيسي:

- [resources/css/app.css](resources/css/app.css)

ومن خلاله تم ضبط الثيم، الألوان، الخطوط، والأنماط الأساسية.

### resources/js

يحتوي على ملف JavaScript الرئيسي:

- [resources/js/app.js](resources/js/app.js)

ومن خلاله تم تشغيل الرسوم البيانية وإعادة تهيئتها بعد تنقلات Livewire.

---

### 4) مجلد routes

مجلد [routes](routes) يحدد كل المسارات التي يستجيب لها التطبيق.

أهم ملفين فيه:

### routes/api.php

هذا الملف مسؤول عن **واجهات API**.

من خلاله نرى أن المشروع يوفّر:

- مسارات للمصادقة مثل signup وlogin وverify-otp وrefresh.
- مسارات للإعلانات والمشاريع.
- مسارات للمستخدمين مثل الطلاب والشركات والعملاء.
- مسارات لإدارة Device Tokens.
- مسارات للإشعارات.

هذا الملف مهم لأنه يمثل الجسر بين التطبيق الخلفي وأي تطبيق خارجي مثل تطبيق الموبايل.

### routes/web.php

هذا الملف مسؤول عن **مسارات الويب** الخاصة بلوحة التحكم.

من خلاله نرى:

- إعادة التوجيه من الصفحة الرئيسية إلى `/dashboard`.
- صفحة تسجيل دخول الإدارة.
- صفحات لوحة التحكم مثل الشركات والعملاء والطلاب والإعلانات والمشاريع والإشعارات.
- حماية هذه المسارات عبر `auth` و`admin`.

هذا يعني أن المشروع فعليًا يحتوي على مسارين عمل:

- مسار API للموبايل أو العملاء الخارجيين.
- مسار Web للوحة التحكم الإدارية.

### routes/console.php

هذا الملف خاص بالأوامر البرمجية التي يمكن تنفيذها من سطر الأوامر إذا احتاج المشروع إلى ذلك.

---

### 5) مجلد config

رغم أن الطلب ركّز على app وdatabase وtemplates وroutes، إلا أن مجلد [config](config) مهم جدًا لفهم المشروع.

هذا المجلد يحتوي على إعدادات النظام مثل:

- قاعدة البيانات.
- البريد.
- الجلسات.
- الطوابير.
- الخدمات الخارجية.
- JWT.

ملفات مهمة:

- [config/database.php](config/database.php)
- [config/services.php](config/services.php)
- [config/queue.php](config/queue.php)
- [config/jwt.php](config/jwt.php)

من هذا المجلد نفهم كيف تم ربط المشروع بالخدمات الخارجية مثل Firebase.

---

### 6) مجلد public

مجلد [public](public) هو نقطة الدخول العامة للتطبيق على الويب.

يحتوي على:

- [public/index.php](public/index.php) كنقطة دخول رئيسية.
- الملفات المبنية من Vite داخل [public/build](public/build).

هذا المجلد هو الذي يتعامل معه المتصفح مباشرة.

---

### 7) مجلد storage

مجلد [storage](storage) يحفظ الملفات التشغيلية المؤقتة والمهمة مثل:

- السجلات Logs.
- الكاش Cache.
- الملفات المرفوعة أو المخزنة.
- ملفات الإطار الداخلي للتطبيق.

وهو مجلد مهم في التشغيل لكنه ليس عادة المكان الأساسي لكتابة منطق الأعمال.

---

## خامسًا: فهم قاعدة البيانات بشكل مبسط

يمكن تلخيص الهيكل العام لقاعدة البيانات في المشروع بالشكل التالي:

1. جدول `users` هو الأصل.
2. لكل مستخدم نوع محدد مثل شركة أو عميل أو طالب.
3. بعض الأنواع لها ملف شخصي مفصل في جدول مستقل.
4. الشركات يمكن أن تنشئ إعلانات وفرص.
5. توجد مشاريع يمكن التقديم عليها.
6. الطلاب يمكنهم التقديم على الإعلانات والمشاريع.
7. النظام يحتوي على إشعارات عادية وإشعارات Push.
8. كل مستخدم يمكن أن يمتلك أكثر من Device Token.

هذا التصميم مفيد لأنه:

- يقسم البيانات بشكل منطقي.
- يسهل التوسع لاحقًا.
- يجعل العلاقات أوضح في ERD.

---

## سادسًا: كيف يمكن شرح الـ ERD الخاص بالمشروع

عند رسم ERD باستخدام DB Visualizer أو أداة مشابهة، يمكن شرح العلاقات الأساسية هكذا:

- `users` يرتبط مع `student_profiles` أو `customer_profiles` أو `company_profiles` غالبًا بعلاقة واحد إلى واحد.
- `users` يرتبط مع `notifications` بعلاقة واحد إلى متعدد.
- الشركة أو المستخدم المرتبط بها يمكن أن يرتبط مع `ads`.
- `ads` ترتبط مع `ad_applications`.
- `projects` ترتبط مع `project_applications`.
- `users` ترتبط مع `device_tokens` بعلاقة واحد إلى متعدد.
- `push_notifications` ترتبط مع `push_notification_recipients`.

وعند عرض هذا الرسم، يصبح من السهل جدًا شرح منطق النظام أمام أي شخص يراجع المشروع.

---

## سابعًا: خلاصة نهائية

مشروع **Hiremee** هو مشروع متكامل يجمع بين:

- Backend قوي باستخدام PHP وLaravel.
- API منظم باستخدام Laravel وJWT.
- لوحة تحكم تفاعلية باستخدام Livewire وBlade.
- واجهة أنيقة باستخدام HTML وTailwind CSS وJavaScript.
- رسوم بيانية باستخدام Chart.js.
- إشعارات فورية باستخدام Firebase.
- تنظيم ممتاز لقاعدة البيانات عبر Migrations.
- اختبار عملي للـ API باستخدام Postman.
- فهم بصري لقاعدة البيانات باستخدام DB Visualizer وERD.

ولو أردنا تلخيص سبب نجاح هذه الأدوات معًا، فالإجابة هي أن كل أداة عالجت جزءًا محددًا من المشروع:

- Laravel نظّم الخلفية.
- Livewire سهّل الواجهة التفاعلية.
- Vite وTailwind وJS حسّنوا شكل الواجهة وسرعتها.
- Firebase قدّم الإشعارات.
- Postman اختبر الـ API.
- DB Visualizer وضّح قاعدة البيانات.
- VS Code جمع كل مراحل التطوير في بيئة واحدة.

وبهذا صار المشروع واضح البنية، قابلًا للتطوير، وسهلًا في الشرح والتنفيذ.

</div>

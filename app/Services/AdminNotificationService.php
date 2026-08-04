<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class AdminNotificationService
{
    /**
     * Helper to create a notification record.
     */
    private function send(User $recipient, string $userType, string $content, int $adminId = 1)
    {
        Notification::create([
            'user_id' => $recipient->id,
            'user_type' => $userType,
            'sender_id' => $adminId, // Assuming admin is sender
            'content' => $content,
        ]);
    }

    // ==========================================
    // Company Ads Notifications
    // ==========================================

    public function companyAdApproved(User $company, string $jobName)
    {
        $content = "تهانينا ! بعد مراجعة طلب الإعلان الخاص بالمسمى الوظيفي \"{$jobName}\" المقدم من قبلكم ، جرى التحقق من كل بيانات الإعلان وتم النشر بنجاح على التطبيق يمكن الآن لجميع الطلاب رؤيته.";
        $this->send($company, 'company', $content);
    }

    public function companyAdRejected(User $company, string $jobName)
    {
        $content = "للأسف ! لم تنجح عملية نشر إعلان التوظيف الخاص بالمسمى الوظيفي \"{$jobName}\" المقدم من قبلكم نظرا للأخطاء التي يتضمنها الإعلان يرجى التأكد من صحة المعلومات المدخلة ومن ثم إعادة المحاولة.";
        $this->send($company, 'company', $content);
    }

    public function companyReminder(User $company, string $studentName, string $jobName)
    {
        $content = "تهانينا ! بعد موافقتك على طلب إجراء مقابلة العمل المقدم من قبل \"{$studentName}\" والخاص بالمسمى الوظيفي \"{$jobName}\" الذي اعلنت عنه، ينبغي عليك أن تتواصل معه لتحديد موعد إجراء المقابلة سوف تجد رابط الايميل الخاص به في ملفه الشخصي.";
        $this->send($company, 'company', $content);
    }

    public function studentAdApplicationAccepted(User $student, string $companyName, string $jobName)
    {
        $content = "تهانينا ! لقد تم قبول طلبك من قبل شركة {$companyName} وذلك لإجراء مقابلة العمل الخاصة بالمسمى الوظيفي \"{$jobName}\" ابقى متحمسا سوف يتم التواصل معك قريبا على ايميلك لتحديد موعد المقابلة.";
        $this->send($student, 'student', $content);
    }

    // ==========================================
    // Client Ads / Projects Notifications
    // ==========================================

    public function projectApproved(User $client, string $projectName)
    {
        $content = "تهانينا ! بعد مراجعة طلب الإعلان الخاص بالخدمة المطلوبة \"{$projectName}\" المقدم من قبلك، جرى التحقق من كل بيانات الإعلان وتم النشر بنجاح على التطبيق يمكن الآن لجميع الطلاب رؤيته.";
        $this->send($client, 'customer', $content);
    }

    public function projectRejected(User $client, string $projectName)
    {
        $content = "للأسف ! لم تنجح عملية نشر الإعلان الخاص بالخدمة المطلوبة \"{$projectName}\" المقدم من قبلك نظرا للأخطاء التي يتضمنها الإعلان يرجى التأكد من صحة المعلومات المدخلة ومن ثم إعادة المحاولة.";
        $this->send($client, 'customer', $content);
    }

    // ==========================================
    // Agreement Workflow Notifications
    // ==========================================

    public function studentAgreementStarted(User $student, string $clientName, string $projectName, string $price)
    {
        $content = "تهانينا ! لقد وافق العميل {$clientName} علي طلبك للقيام بخدمة \"{$projectName}\" وفق سعرك المحدد \"{$price}\" يمكنك البدء في بناء الخدمة حال تلقيك تأكيد من الأدمن.";
        $this->send($student, 'student', $content);
    }

    public function studentConfirmStartWithLink(User $student, string $clientName, string $projectName, string $link, string $startDate, string $endDate)
    {
        $content = "تهانينا ! يمكنك البدء ببناء خدمة \"{$projectName}\" المطلوبة من العميل {$clientName}، يمكنك رفع الخدمة بعد أن تنتهي من إنجازها على الرابط التالي {$link}\nعلما انه يحق لك تسليم الخدمة في الفترة بين {$startDate} و {$endDate} في حال تأخر التسليم تعتبر العملية ملغية، احرص على تسليم كامل ما تم الاتفاق عليه مع العميل ضمن إعلان الخدمة حيث سيخضع ما قمت برفعه على الرابط لتقييم من قبل فريق مختص بمجال الخدمة وذلك قبل تسليمها للعميل.\nفي حال وجود أخطاء او نقص في محتويات الخدمة لن نستطيع تسليمك اية أجور وسيتعين عليك إصلاح اية اعطال في الخدمة.\nفي حال اجتازت خدمتك فحص الاختبار الخاص بفريق التقييم سوف نسلم خدمتك للعميل مباشرة ولكن يرجى اخذ العلم بأنه يحق للعميل ان يعترض على احد جوانب خدمتك خلال مدة \"تجريب الخدمة\" التي تمتد الى اسبوع من تاريخ تسلمه الخدمة.\nفي حال كان كل شيء على ما يرام ولم يعترض العميل خلال مدة التجريب سوف تستلم اجورك على الفور.\nفي حال اعترض العميل على جانب من جوانب الخدمة سوف يتم مراجعة اعتراضه لتحديد الإجراء المناسب.";
        $this->send($student, 'student', $content);
    }

    public function studentSubmissionApprovedByAdmin(User $student, string $link, string $clientName)
    {
        $content = "تهانينا ! لقد تم فحص محتويات الخدمة التي قمت برفعها على الرابط {$link} من قبل الفريق المختص لدينا وبالفعل كل شيء يبدو على ما يرام. حاليا قمنا بإرسال محتويات الخدمة الى العميل {$clientName} ليقوم بتجريبها واختبارها والتأكد من سلامة العمل بنفسه. سوف يتعين علينا الانتظار حتى انتهاء مدة التجريب التي تبلغ أسبوع ابتداءا من اليوم، قبل أن نتمكن من تحويل الأجور الى حسابك لنتأكد من رضا العميل عن العمل المنجز. في حال لم يعترض العميل على أحد جوانب الخدمة سوف تستلم اجورك فورا بعد انقضاء مدة التجريب. في حال اعترض العميل سوف يخضع اعتراضه للتقييم من قبل فريق مختص لتحديد الإجراء المناسب.";
        $this->send($student, 'student', $content);
    }

    public function studentSubmissionRejectedByAdmin(User $student, string $link, string $endDate)
    {
        $content = "للأسف ! لقد تم فحص محتويات الخدمة التي قمت برفعها على الرابط {$link} من قبل الفريق المختص لدينا وتبين وجود أخطاء ما أو نقص في محتوى الخدمة التي قمت بتسليمها. رجاء تأكد من صحة المحتويات التي قمت برفعها ثم أعد رفعها من جديد على نفس الرابط بعد ان تحل المشكلة او النقص. يرجى الانتباه الى أن آخر يوم لتسليم الخدمة هو {$endDate}. في حال عدم تسليم الخدمة بالشكل المطلوب سوف تفقد حقك في استلام اية أجور.";
        $this->send($student, 'student', $content);
    }

    public function studentFailedDelivery(User $student, string $projectName, string $clientName)
    {
        $content = "للأسف ! نظرا لتأخرك عن تسليم خدمة \"{$projectName}\" الخاصة بالعميل {$clientName} ووفقا لسياسة تطبيقنا فقد فقدت حقك في استلام اجرك المطلوب. شكرا لتفهمك.";
        $this->send($student, 'student', $content);
    }

    public function clientRefundedStudentFailed(User $client, string $studentName, string $projectName, string $amount)
    {
        $content = "للأسف ! لسبب ما لم ينجح الطالب \"{$studentName}\" في انجاز خدمة \"{$projectName}\" الخاصة بك وعليه قمنا بإعادة المبلغ الذي دفعته والبالغ \"{$amount}\" الى حسابك.";
        $this->send($client, 'customer', $content);
    }

    public function clientReceivedService(User $client, string $projectName, string $studentName, string $link)
    {
        $content = "تهانينا ! لقد تم استلام الخدمة المطلوبة \"{$projectName}\" من الطالب \"{$studentName}\" متضمنة كافة المحتويات المطلوب تسليمها بنجاح، سوف تجد الخدمة على الرابط التالي {$link}. في حال وجدت مشكلة او خطأ في أحد جوانب الخدمة المستلمة او شيء آخر مخالف للاتفاق المعلن عنه ضمن إعلان الخدمة، يمكنك تقديم طلب اعتراض لنا لاتخاذ الإجراءات اللازمة وضمان حقك بشكل كامل، ولكن يرجى اخذ العلم بأنه يحق لك الاعتراض فقط خلال مدة التجريب التي تبلغ أسبوع والتي تبدأ من اليوم وبعد مضي هذه المدة تفقد حقك في الاعتراض بشكل كامل، وسيتم تسليم الطالب \"{$studentName}\" أجره المتفق عليه، يمكنك تقديم طلب الاعتراض في قسم المساعدة والدعم.";
        $this->send($client, 'customer', $content);
    }

    public function studentTrialEndedSuccessfully(User $student, string $projectName, string $clientName)
    {
        $content = "تهانينا ! لقد انقضت مدة التجريب التي تخص خدمة \"{$projectName}\" التي قمت بإنجازها لصالح العميل {$clientName} وبالفعل كل شيء على ما يرام، لذلك تم تحويل اجورك الى حسابك يمكنك الآن سحبها والتصرف بها.";
        $this->send($student, 'student', $content);
    }

    // ==========================================
    // Objections Notifications
    // ==========================================

    public function clientObjectionReceived(User $client)
    {
        $content = "اطمئن لقد تم استلام طلب اعتراضك مع كافة التفاصيل المقدمة من قبلك حيث سيتم حاليا إعادة تدقيق محتويات الخدمة المستلمة من قبل الفريق المختص لاتخاذ الإجراء المناسب حيث في حال تبين وجود خطأ او نقص مسؤول عنه الطالب سوف نطالبه بإصلاح الأمر خلال مدة اقصاها 48 ساعة وسنعيد تسليم الخدمة لك. في حال لم يقم الطالب بإصلاح الخدمة واعادة تسليمها لنا خلال المدة المحددة سوف يفقد حقه الكامل في استلام اية اجور وسوف نعيد لك المبلغ المدفوع. أما في حال تبين عدم وجود نقص او خطأ من قبل الطالب لن نطالبه بأي شيء وسوف يستلم اجره مباشرة بعد انتهاء مدة التجريب. سوف نعلمك بالإجراء الذي سنتخذه حال انتهاء الفريق من تدقيق محتويات الخدمة المستلمة عادة العملية تستغرق أقل من 24 ساعة من وقت تقديم الاعتراض.";
        $this->send($client, 'customer', $content);
    }

    public function clientObjectionRejected(User $client, string $projectName, string $studentName)
    {
        $content = "بعد ان قام الفريق المختص لدينا بتدقيق شامل لمحتويات الخدمة \"{$projectName}\" التي قام بتقديمها الطالب \"{$studentName}\" ومطابقتها مع ما هو متفق على تسليمه في نص إعلان الخدمة، وجد الفريق بأنه لا يوجد اي نقص او أخطاء في الخدمة التي تم تسليمها، لك وعليه سوف يتم تسليم الطالب أجره المستحق وذلك فور انتهاء مدة التجريب.";
        $this->send($client, 'customer', $content);
    }

    public function clientObjectionAccepted(User $client, string $projectName, string $studentName)
    {
        $content = "بعد ان قام الفريق المختص لدينا بتدقيق شامل لمحتويات الخدمة \"{$projectName}\" التي قام بتقديمها الطالب \"{$studentName}\" ومطابقتها مع ما هو متفق على تسليمه في نص إعلان الخدمة وجد الفريق بأنه بالفعل يوجد بعض النقص او الأخطاء التي يتحمل مسؤوليتها الطالب \"{$studentName}\" لذلك طالبناه على الفور بإصلاح الأخطاء ومعالجة الخدمة وإعادة تسليمها لنا خلال مدة اقصاها 48 ساعة. في حال تم إصلاح الخدمة من قبل الطالب خلال المدة المحددة سوف نقوم بتسليمك الخدمة من جديد أما في حال لم ينجح الأمر سوف نعيد لك المبلغ الذي دفعته. سوف نعلمك بما ستؤول اليه الأمور خلال مدة أقصاها، اسبوع من الآن.";
        $this->send($client, 'customer', $content);
    }

    public function studentObjectionReceived(User $student, string $clientName, string $projectName, string $objectionDetails, string $deadlineDate, string $link)
    {
        $content = "للأسف ! لقد قدم العميل {$clientName} صاحب خدمة \"{$projectName}\" طلب اعتراض يخص النقاط التالية :\n{$objectionDetails}\nولقد قام الفريق المختص لدينا بالتأكد من صحة الاعتراض وعليه يتوجب عليك خلال مدة أقصاها 48 ساعة اي قبل يوم {$deadlineDate} معالجة المشاكل الموجود في الخدمة وإعادة رفعها على الرابط {$link}. في حال تأخرك عن رفع الخدمة بشكلها الصحيح سوف تفقد حقك في استلام اية اجور وسوف يتم الغاء العملية بشكل كامل بينك وبين العميل.";
        $this->send($student, 'student', $content);
    }

    public function studentFixRejected(User $student, string $link, string $deadlineDate)
    {
        $content = "للأسف ! لقد تم فحص محتويات الخدمة بنسختها المعدلة والتي قمت برفعها على الرابط {$link} من قبل الفريق المختص لدينا ولقد تبين لنا عدم قيامك بكامل التعديلات. يرجى الانتباه الى ان يوم {$deadlineDate} هو آخر يوم يمكنك فيه رفع الخدمة وفق التعديلات المطلوبة. في حال تأخرك عن القيام بالتعديلات ورفع الخدمة المعدلة يؤسفنا انك ستفقد حقك في استلام الأجور وسنقوم بإلغاء العملية بينك وبين العميل.";
        $this->send($student, 'student', $content);
    }

    public function studentFixApproved(User $student, string $link, string $clientName)
    {
        $content = "تهانينا ! لقد تم فحص محتويات الخدمة بنسختها المعدلة والتي قمت برفعها على الرابط {$link} من قبل الفريق المختص لدينا وبالفعل كل شيء يبدو على ما يرام وقد تم القيام بالتعديلات المطلوبة حاليا قمنا بإرسال محتويات الخدمة الى العميل {$clientName} من جديد ليقوم بتجريبها واختبارها والتأكد من سلامة العمل بنفسه سوف يتعين علينا الأنتظار حتى انتهاء مدة التجريب قبل أن نتمكن من تحويل الأجور الى حسابك لنتأكد من رضا العميل عن العمل المنجز. في حال لم يعترض العميل على أحد جوانب الخدمة سوف تستلم اجورك فورا حال انتهاء مدة التجريب وفي حال اعترض العميل مرة أخرى سوف يخضع اعتراضه للتقييم من قبل فريق مختص لتحديد الإجراء المناسب.";
        $this->send($student, 'student', $content);
    }

    public function clientFixReceived(User $client, string $studentName, string $projectName, string $link)
    {
        $content = "تهانينا ! لقد نجح الطالب \"{$studentName}\" في إصلاح الأخطاء التي كانت موجودة في خدمة \"{$projectName}\" الخاصة بك وقام بتسليمنا نسخة جديدة من الخدمة جرى تدقيقها من قبل الفريق المختص ووجد بأنها سليمة ولا تحتوي اية مشاكل ستجد النسخة الجديدة من الخدمة على الرابط التالي {$link}. في حال وجدت مشكلة او خطأ يمكنك تقديم طلب اعتراض لنا لاتخاذ الإجراءات اللازمة وضمان حقك بشكل كامل ولكن يرجى اخذ العلم بأنه يحق لك الاعتراض فقط خلال مدة التجريب.";
        $this->send($client, 'customer', $content);
    }
}

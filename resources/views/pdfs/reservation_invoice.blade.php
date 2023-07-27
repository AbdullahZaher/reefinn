<style>
    @media print {
        .reservation-invoice {
            zoom: 65%;
            page-break-inside: avoid;
        }
    }
</style>

<div class="mt-5 px-5 mx-auto reservation-invoice w-full" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    lang="{{ app()->getLocale() }}">
    <div>
        <img class="mx-auto"
            src="data:image/png;base64,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">
    </div>
    <div class="text-3xl font-bold text-center mt-10">
        {{ __('Guest Registration Card') }}
    </div>

    <div class="mt-10 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full">
                    <tbody class="">
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Full Name') }}:</span> {{ $reservation->guest_name }}
                            </td>
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Nickname') }}:</span>
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Phone Number') }}:</span> {{ $reservation->guest_phone }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Email') }}:</span>
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('ID Number') }}:</span> {{ $reservation->guest_id }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Nationality') }}:</span>
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Birthday') }}:</span>
                                {{ $attrs['guest_birthday'] }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Copy') }}:</span> {{ $reservation->copy }}
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Check-in Date') }}:</span>
                                {{ $attrs['checkin'] }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Check-out Date') }}:</span>
                                {{ $attrs['checkout'] }}
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Number Of Companions') }}:</span>
                                {{ $reservation->number_of_companions }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Apartment Number') }}:</span>
                                {{ $reservation->apartment->name }}
                            </td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Night\'s Price') }}:</span>
                                {{ $reservation->price_for_night . ' ' . __('SAR') }}
                            </td>

                            <td class="px-6 py-8 whitespace-nowrap font-medium text-black border border-black"
                                colspan="1">
                                <span class="font-bold">{{ __('Total') }}:</span>
                                {{ $reservation->total_price . ' ' . __('SAR') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (app()->getLocale() == 'ar')
        <div class="mt-10">
            تضع مؤسسة اجنحة ريام العقارية سرية معلومات مستخدميها ونزلائها على رأس قائمة الأولويات، وتبذل الإدارة كل
            جهودها لتقديم خدمة ذات جودة عالية لكل النزلاء. يجب على النزلاء الاطلاع المستمر على شروط ومبادئ الخصوصية
            وسرية المعلومات لدى الهيئة العامة للعقار والهيئة السعودية للسياحة لمعرفة أي تحديثات تتم عليها، علماً بأنهم
            غير مطالبين بالإعلان عن أية تحديثات تتم على تلك الشروط والمبادئ، واستئجارك يعني اطلاعك وموافقتك على تلك
            الشروط والمبادئ وما يتم عليها من تعديلات مستمرة.
            عند الإقامة، فإننا نجمع بياناتك الشخصية ونعالجها للأغراض التالية (1) تسجيل وصولك إلى الفندق وتسجيل مغادرتك
            له، (2) تخصيص بطاقة دخول لغرفتك أو السماح لك باستخدام جهازك الجوال مفتاحًا لدخول غرفتك، (3) الحصول على ضمان
            باستخدام بطاقتك الائتمانية أو وديعة للفندق لضمان دفع مقابل إقامتك، (4) إدارة بطاقة تسجيلك في الفندق
            (وأرشفتها)، (5) إنشاء ملفك التعريفي أو تحديثه في نظامنا لإدارة الفنادق، (6) تقييم مدى استيفائك لشروط ترقية
            الغرف وإدارة ذلك إذا كنت مستوفيًا للشروط، (7) إدارة سداد مقابل إقامتك، (8) إعداد فاتورة إقامتك أو طباعتها أو
            إرسالها، (9) سداد عمولة إلى وكيل السفر الذي حجزت عن طريقه (في حالة الحجز عن طريق وكيل سفر).
            في حالة حجزك غرفة في أحد فنادقنا وعدم حضورك في يوم الوصول الذي أبلغتنا به، دون الإلغاء، فإننا سنعالج بياناتك
            الشخصية للغرضين التاليين: (1) إلغاء إقامتك وأي حجز آخر قد تكون أجريته، و(2) إدارة أي مبالغ مستحقة غير
            مسدَّدة ومعالجتها وتسويتها.
            في حالة الاتصال بك يوم المغادرة الساعه الثانية ظهرًا، سيفتح باب الغرفة للاطلاع عليها وهذا حق من حقوق التآكد
            من انها شاغرة او مشغولة، علمًا ان المغادرة بعد الساعة السادسة مساءً سيحتسب بسعر ليلة كاملة. يمنع تدخين
            الشيشة والسجائر في الغرفة منعا باتًا وفي حال التدخين سيتم فرض غرامة قدرها مئتين ريال. يمنع اصطحاب او ادخال
            مواد طبخ الى الفندق كالغاز ونحوه. للمؤسسة الحق في اخلاء مقتنياتك من الغرفة حال تجاوز الوقت المحدد.
        </div>
    @else
        <div class="mt-10">
            Riam Real Estate Suites places the confidentiality of the information of its users and guests at the top of
            the list of priorities, and the management makes every effort to provide high quality service to all guests.
            Guests must constantly review the terms and principles of privacy and confidentiality of information with
            the General Real Estate Authority and the Saudi Tourism Authority to see any updates to them, bearing in
            mind that they are not required to announce any updates to these conditions and principles, and your hiring
            means that you have seen and agreed to those terms and principles and what is done on them. Continuous
            adjustments. When you stay, we collect and process your personal data for the following purposes (i)
            checking you in and checking out of the hotel, (ii) assigning an access card to your room or allowing you to
            use your mobile device as your room key, (iii) obtaining a security with your credit card or a hotel deposit
            To ensure payment for your stay, (iv) manage (and archive) your hotel check-in card, (v) create or update
            your profile in our hotel management system, (vi) assess and manage if you meet the requirements for a room
            upgrade, (vii) manage Payment for your accommodation, (viii) preparing, printing or sending an invoice for
            your
            accommodation, (ix) paying a commission to the travel agent you booked through (in the case of booking
            through a travel agent). In the event that you book a room in one of our hotels and fail to appear on the
            day of arrival that you notify us, without canceling, we will process your personal data for the following
            two purposes: (i) to cancel your stay and any other booking you may have made, and (ii) to administer,
            process and settle any outstanding amounts due. In the event that you are contacted on the day of departure
            at two o'clock in the afternoon, the door to the room will be opened to view it, and this is one of the
            rights to make sure that it is vacant or occupied, noting that leaving after six o'clock in the evening will
            be charged at the price of a full night. It is strictly forbidden to smoke shisha and cigarettes in the
            room. In the event of smoking, a fine of two hundred riyals will be imposed. It is forbidden to take or
            bring cooking materials to the hotel, such as gas and the like. The institution has the right to evacuate
            your belongings from the room if the specified time is exceeded.
        </div>
    @endif

    <div class="flex items-center justify-between mt-24">
        <div>{{ __('Please sign here') }}: _____________________________</div>
        <div>{{ __('Date') }}:
            {{ auth()->user()->calendar == 'hijri' ? \Alkoumi\LaravelHijriDate\Hijri::Date('j F Y h:i:s A', Timezone::convertToLocal(now(), 'Y-m-d h:i:s A')) : \Illuminate\Support\Carbon::parse(Timezone::convertToLocal(now(), 'Y-m-d h:i:s A'))->translatedFormat('d F Y h:i:s A') }}
        </div>
    </div>
</div>

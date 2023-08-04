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
    @if ($hotel['logo'])
    <div>
        <img class="mx-auto" src="{{ $hotel['logo'] }}"/>
    </div>
    @endif
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
                            <td class="font-medium text-black border-b border-r border-black p-0" colspan="2">
                                <div class="flex items-center">
                                    <div class="px-6 py-8 whitespace-nowrap flex-1 border-l border-black">
                                        <span class="font-bold">{{ __('Night\'s Price') }}:</span>
                                        {{ $reservation->price_for_night . ' ' . __('SAR') }}
                                    </div>
                                    <div class="px-6 py-8 whitespace-nowrap flex-1 border-l border-black">
                                        <span class="font-bold">{{ __('Total') }}:</span>
                                        {{ $reservation->total_price . ' ' . __('SAR') }}
                                    </div>
                                    <div class="px-6 py-8 whitespace-nowrap flex-1 border-l border-black">
                                        <span class="font-bold">{{ __('Discount') }}:</span>
                                        @if ($reservation->discount > 0)
                                        <span class="font-medium">{{ $reservation->discount . '%'}}</span>
                                        <span class="text-sm">({{ $reservation->discount_amount . " " . __("SAR") }})</span>
                                        @else
                                        <span class="font-medium">-</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="mt-10">
            {{ $attrs['reservation_lease_terms'] }}
        </div>


    <div class="flex items-center justify-between mt-24">
        <div>{{ __('Please sign here') }}: _____________________________</div>
        <div>{{ __('Date') }}:
            {{ auth()->user()->calendar == 'hijri' ? \Alkoumi\LaravelHijriDate\Hijri::Date('j F Y h:i:s A', Timezone::convertToLocal(now(), 'Y-m-d h:i:s A')) : \Illuminate\Support\Carbon::parse(Timezone::convertToLocal(now(), 'Y-m-d h:i:s A'))->translatedFormat('d F Y h:i:s A') }}
        </div>
    </div>
</div>

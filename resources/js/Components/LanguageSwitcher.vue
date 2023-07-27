<script setup>
import { ref } from "vue";
import { __ } from "@/Composables/translations";

const isMenuOpen = ref(false);
</script>

<template>
    <div class="relative">
        <button
            class="flex mx-4 text-gray-600 focus:outline-none"
            @click.prevent="isMenuOpen = !isMenuOpen"
        >
            <FontAwesomeIcon icon="fas fa-globe" class="text-lg" />
        </button>

        <div
            v-if="isMenuOpen"
            @click="isMenuOpen = false"
            class="fixed inset-0 h-full w-full z-10"
        ></div>

        <div
            v-if="isMenuOpen"
            class="absolute top-5 mt-2 bg-white rounded-lg shadow-xl overflow-hidden z-10 w-48 space-y-1"
        >
            <a
                :href="route('language', 'en')"
                class="flex items-center px-3 py-2 -mx-2"
                :class="[
                    $page.props.locale.lang == 'en'
                        ? 'bg-indigo-600 text-white'
                        : 'text-gray-600 hover:text-white hover:bg-indigo-500',
                ]"
            >
                <img
                    class="h-8 w-8 rounded-full object-cover mx-1"
                    src="data:image/png;base64,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"
                    alt="flag"
                />
                <p class="text-sm mx-2">{{ __("English") }}</p>
            </a>

            <a
                :href="route('language', 'ar')"
                class="flex items-center px-3 py-2 -mx-2"
                :class="[
                    $page.props.locale.lang == 'ar'
                        ? 'bg-indigo-600 text-white'
                        : 'text-gray-600 hover:text-white hover:bg-indigo-500',
                ]"
            >
                <img
                    class="h-8 w-8 rounded-full object-cover mx-1"
                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAAtCAMAAADFqPh+AAABOFBMVEVHcEwBbD8KYTwFWC8ATCQATiUBVSwFVy4AUCYBWS4NYTgAQR0CajwDbUABaTsOXjoAPhsBYzYAUCUAUSYBVywBckUAQx4AZjAAbDUAZC8AbjcAdT8AcDkAaTMAXCoAaDIAazQAXywAWikAcz0AeUIAWCgAVicAekQAVCUAUSQAYy4AYS0AfEYAXisAYi4AcjsAfUcAgEsAd0EAf0kARR4APBkATSMASSAAQRwAgk0AYTMAcEEAZDYAc0VEk2sAZzkAcToAbT/U5t0AXjAAWi0AajzB287L4dWAtZmWwqtcoHw5jGKaxK631cZtqop1sJJQm3WHuqBLlm8wil2dx7Hh7uey0sEdekkjhFWrz71hpoQRbz0NdkGny7jz+PYLe0cAhE+Nu6Iuf1SkybaPwKegyLQac0OSu6XvWC9/AAAAF3RSTlMA1xs2kbdTKm1CEddrjLIF8vTP9Xzw3wHXW9cAAAQYSURBVEjHjdaHVupKFAbggChg11NAEFE4iSCiHpRyRYNETK/0jtju+7/BncmEyYDouv8SWQv5Zu8pyFAUkZ11r9e7vkP9zwSdZ+/a1rbnV7lqZ8/j2d7aDPq/HWU9sM/A55Utz335vlwuV8vwV/UJpFIBP/YY3iXSH1itMTRDrW967kFOz6//wlxfX19dnZ/mouXqU+XOzp5n2/dzcy244vf7V4JrAd/qXo2m6SzDUL+AvPoHBNOrc5BTkD+56CWof3d7ewcej3ZqNfioZUKxGMD398/Pz4vWpjC5XC4aTT4chivQ2wnFY+Hw8THAKRtju4zCJEEuLx8eDg8jIMjGEf7CLqGHNkU2nkoDjCxaqbmyLl1mQwCXy47FZb+jhA2lbexat+MFusQi7FhUtss2uspkQtK5sq6dYdf+mfR7kto0onJfHypmdOhQtyy2B+kCVa06Fh0LuclxEt+cvE27bVEzOL4/7A6TblnXntkYWzjZN0lovWlGX2dFjrUUVW2J+shqDC2x3m10AJ3ZGSZszqy3epJpJc1usy8IkmBwUqc3kiJ8q62pPdJeFArUU5W00YloNTWNMyxr9M6yulVn25KqW2FzbIiiBCi2GYifXBuN5lil3tQ4rv3O8lLdbHDccNwwzN5bU3997/CkRdixaG9zjVel0Rt1lWTbVNut8VuYe5fr7QGrj7ljLhRy7UmhCPGsZbi1stFrtSw1aQgS25JaXE/nxamsvrzqEvchIHtmW4grT6RNCnVB7fFsb2x0BYHlNU3nB5psDT6kqSZppM1CXHGma5+oy8lDV24k5Qbb4pSOwHfMeDtuiqLZlKev7wJps0WISUscRXAqImyDZTuGUNdH4lhmB8IBnK5jjxy8zOID1TE6MbUfishyc0rao+INdVch7QJFJwrtz0BRQnbLjk1AfIftwqdnjoZCF3C2dllkEzcQI0v+i8I0hincXNyybekZXmz5S0rYGSbsF3S+ZdvSNyXq9nZm8UodxuYmS5YlLGNj16KykSnHN0VZGS5Qt2XbOni2VLjlyAsri688x6s624lfnF0MZmUJmyqVqMdboi7eHlRzoMjiiOfb/wr1lw9EXWvjxwXrThatU+Zs8CKMtPHHgkX4gfwmWLY9mZMXpa4OEMU2VcoDDC3RMkHdNT6CVXFZ26YRnmt5CXVnS1qIazWy5YVDQdBP1sGfrbuzkBJlCZvOQ4y/gOJLJutSpyy2BQcDGz/IZBPojwxD04mj7OeO520R4kT4+OCEZpzXwKs46RQDRsF01jJ8X+GmlM/vUjR9Agf8seoLuLeln77VjR9FJ4V0OpViGFy2UIQyvxFYp2gIA/4lNzVvEFy5NorFGyIlkDyUvhX4FmYfjPBNdvzw2rb/e7eEsrux6ltzboX/AX7CqVMFvrB5AAAAAElFTkSuQmCC"
                    alt="flag"
                />
                <p class="text-sm mx-2">{{ __("Arabic") }}</p>
            </a>
        </div>
    </div>
</template>

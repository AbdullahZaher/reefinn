import { library, config } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core/styles.css";

// Prevent fontawesome from adding its CSS since we did it manually above:
config.autoAddCss = false;

import {
    faXmark,
    faCircleInfo,
    faCirclePlus,
    faPenToSquare,
    faRightFromBracket,
    faClipboardCheck,
    faGlobe,
    faCircleCheck,
    faCircleXmark,
    faBan,
    faPersonWalkingArrowRight,
    faTrash,
    faCalendar,
    faMicrochip,
    faCaretDown,
    faPrint,
    faWrench,
    faUnlock,
} from "@fortawesome/free-solid-svg-icons";

export default function initFontawesome() {
    library.add(
        faXmark,
        faCircleInfo,
        faCirclePlus,
        faPenToSquare,
        faRightFromBracket,
        faClipboardCheck,
        faGlobe,
        faCircleCheck,
        faCircleXmark,
        faBan,
        faPersonWalkingArrowRight,
        faTrash,
        faCalendar,
        faMicrochip,
        faCaretDown,
        faPrint,
        faWrench,
        faUnlock
    );

    return FontAwesomeIcon;
}

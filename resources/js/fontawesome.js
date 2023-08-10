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
    faClockRotateLeft,
    faBuilding,
    faUsers,
    faMoneyBills,
    faMoneyBillTransfer,
    faList,
    faTableCellsLarge,
    faStar,
} from "@fortawesome/free-solid-svg-icons";

import { faStar as faStarR } from "@fortawesome/free-regular-svg-icons";

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
        faUnlock,
        faClockRotateLeft,
        faBuilding,
        faUsers,
        faMoneyBills,
        faMoneyBillTransfer,
        faList,
        faTableCellsLarge,
        faStar,
        faStarR
    );

    return FontAwesomeIcon;
}

import moment from "moment-hijri";

export function convertIfHijri(date, page) {
    return page.props.auth.user.calendar == "hijri"
        ? moment(date, "iYYYY-iMM-iDD").format("YYYY-MM-DD")
        : date;
}

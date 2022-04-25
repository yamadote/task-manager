
const LocalStorage = new function () {
    return {
        getShowCalendar: () => {
            return localStorage.getItem('showCalendar') !== "false"
        },
        setShowCalendar: (showCalendar) => {
            localStorage.setItem('showCalendar', showCalendar.toString());
        }
    }
}

export default LocalStorage;

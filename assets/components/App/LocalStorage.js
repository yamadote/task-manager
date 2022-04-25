
const LocalStorage = new function () {
    return {
        getShowCalendar: () => {
            return localStorage.getItem('showCalendar') === "true"
        },
        setShowCalendar: (showCalendar) => {
            localStorage.setItem('showCalendar', showCalendar.toString());
        }
    }
}

export default LocalStorage;

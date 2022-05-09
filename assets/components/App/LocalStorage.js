
const LocalStorage = new function () {
    return {
        getShowCalendar: () => {
            return localStorage.getItem('showCalendar') === "true"
        },
        setShowCalendar: (showCalendar) => {
            localStorage.setItem('showCalendar', showCalendar.toString());
        },
        getReminderNumber: () => {
            return localStorage.getItem('reminderNumber');
        },
        setReminderNumber: (reminderNumber) =>  {
            localStorage.setItem('reminderNumber', reminderNumber);
        }
    }
}

export default LocalStorage;

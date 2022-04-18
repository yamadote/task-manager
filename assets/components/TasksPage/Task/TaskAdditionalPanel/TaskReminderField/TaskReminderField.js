
import React from 'react';
import moment from "moment";
import './TaskReminderField.scss';

const TaskReminderField = ({task, events}) => {
    const updateReminder = (reminder) => {
        events.updateTaskReminder(task, reminder)
    }

    const onReminderChange = event => updateReminder(moment(event.target.value).unix())
    const value = task.reminder ? moment.unix(task.reminder).format('YYYY-MM-DDTHH:mm') : '';

    const setMorning = date => date.set('hour', 9).set('minute', 0).set('second', 0);

    const onOneHourClick = () => updateReminder(moment().add(1, 'hour').unix());
    const onTomorrowClick = () => updateReminder(setMorning(moment().add(1, 'day')).unix());
    const onNextMondayClick = () => {
        updateReminder(setMorning(moment().startOf('isoWeek').add(1, 'week')).unix());
    }
    const onNextWeekClick = () => updateReminder(setMorning(moment().add(1, 'week')).unix());
    const onNoneClick = () => updateReminder(null);

    return (
        <div className="field reminder">
            <span>Reminder:</span>
            <input
                className="form-control form-control-plaintext"
                type="datetime-local"
                value={value}
                onChange={onReminderChange}/>
            <button onClick={onOneHourClick}>One Hour</button>
            <button onClick={onTomorrowClick}>Tomorrow</button>
            <button onClick={onNextMondayClick}>Next Monday</button>
            <button onClick={onNextWeekClick}>Next Week</button>
            <button onClick={onNoneClick}>None</button>
        </div>
    )
}

export default TaskReminderField;


import React from 'react';
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from "@fullcalendar/interaction"
import './TasksCalendar.scss';
import moment from "moment";
import Config from "../../App/Config";

const TasksCalendar = ({tasks, statuses, events}) => {
    const reminders = tasks ? tasks.filter(task => !task.isHidden && task.reminder) : [];
    const calendarEvents = reminders.map(task => {
        const date = new Date(task.reminder * 1000);
        const isReminder = task.reminder && task.reminder < moment().unix();
        const status = statuses.find((status) => status.id === task.status);
        const color = isReminder ? Config.reminderTaskColor : status.color;
        return {
            id: task.id,
            title: task.title,
            date: date.toISOString(),
            backgroundColor: color
        };
    });
    const eventDrop = (info) => {
        const taskId = parseInt(info.event.id);
        const task = tasks.find(task => task.id === taskId);
        const reminder = info.event.start.getTime() / 1000;
        events.updateTaskReminder(task, reminder)
    };
    return (
        <div className="calendar">
            <FullCalendar
                plugins={[ dayGridPlugin, interactionPlugin ]}
                initialView="dayGridWeek"
                events={calendarEvents}
                eventDrop={eventDrop}
                editable={true}
                contentHeight={118}
                firstDay={1}
                eventClick={info => navigator.clipboard.writeText(info.event.title)}
            />
        </div>
    );
}

export default TasksCalendar;

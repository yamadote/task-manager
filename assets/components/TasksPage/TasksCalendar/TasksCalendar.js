
import React from 'react';
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from "@fullcalendar/interaction"
import './TasksCalendar.scss';

const TasksCalendar = ({tasks}) => {
    const reminders = tasks ? tasks.filter(task => !task.isHidden && task.reminder) : [];
    const events = reminders.map(task => {
        const date = new Date(task.reminder * 1000);
        return {
            title: task.title,
            date: date.toISOString()
        };
    });
    return (
        <div className="calendar">
            <FullCalendar
                plugins={[ dayGridPlugin, interactionPlugin ]}
                initialView="dayGridWeek"
                events={events}
                contentHeight={118}
                firstDay={1}
            />
        </div>
    );
}

export default TasksCalendar;


import React from 'react';
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from "@fullcalendar/interaction"
import './TasksCalendar.scss';

const TasksCalendar = ({tasks, events}) => {
    const reminders = tasks ? tasks.filter(task => !task.isHidden && task.reminder) : [];
    const calendarEvents = reminders.map(task => {
        const date = new Date(task.reminder * 1000);
        return {
            id: task.id,
            title: task.title,
            date: date.toISOString()
        };
    });
    const eventDrop = (info) => {
        const taskId = parseInt(info.event._def.publicId);
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
            />
        </div>
    );
}

export default TasksCalendar;

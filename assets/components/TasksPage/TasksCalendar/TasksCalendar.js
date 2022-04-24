
import React from 'react';
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from "@fullcalendar/interaction"
import './TasksCalendar.scss';

const TasksCalendar = ({root, tasks}) => {

    let reminders = tasks ? tasks.filter(task => {
        if (task.isHidden || !task.reminder) {
            return false;
        }
        if (!root) {
            return true;
        }
        // todo: filter by root
        return true;
    }).map(task => {
        const date = new Date(task.reminder * 1000);
        return {
            title: task.title,
            date: date.toISOString()
        };
    }) : null;

    return (
        <div className="calendar">
            <FullCalendar
                plugins={[ dayGridPlugin, interactionPlugin ]}
                initialView="dayGridWeek"
                events={reminders}
                contentHeight={118}
                firstDay={1}
            />
        </div>
    );
}

export default TasksCalendar;

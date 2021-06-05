
import React, {useEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import TaskList from './TaskList/TaskList'
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import Navbar from "./Navbar/Navbar";

const getRootParam = () => {
    const params = useParams();
    const isInteger = new RegExp('^[0-9]+$');
    return params.root && params.root.match(isInteger) ? parseInt(params.root) : null;
}

const TasksPage = ({fetchFrom, nested = true}) => {
    const root = getRootParam();
    const [tasks, setTasks] = useState(undefined);
    const [statuses, setStatuses] = useState(undefined);

    const events = new function () {
        return {
            reload: () => {
                setTasks(undefined);
                fetch(fetchFrom)
                    .then(response => response.json())
                    .then(response => {
                        setStatuses(response.statuses);
                        setTasks(response.tasks);
                    });
            },
            updateTask: (id, update) => {
                setTasks(tasks => {
                    return tasks.map(task => {
                        if (task.id === id) {
                            task = {...task, ...update};
                        }
                        return task;
                    });
                })
            },
            createNewTask: (parent = null) => {
                const url = Config.apiUrlPrefix + '/tasks/new';
                Helper.fetchJsonPost(url, {'parent': parent})
                    .then(task => {
                        setTasks(tasks => [task, ...tasks])
                        if (parent !== null) {
                            events.updateTask(parent, {isChildrenOpen: true})
                        }
                    });
            },
            removeTask: (id) => {
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/delete';
                fetch(url, {method: 'POST'})
                    .then(() => {
                        // todo: remove task children
                        setTasks(tasks => tasks.filter(i => i.id !== id))
                    })
            },
            updateTaskTitle: (id, title, setTitleChanging) => {
                setTitleChanging(true);
                events.updateTask(id, {title: title});
                Helper.addTimeout('task_title' + id, () => {
                    const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                    Helper.fetchJsonPost(url, {'title': title})
                        .then(() => setTitleChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskLink: (id, link, setLinkChanging) => {
                setLinkChanging(true);
                events.updateTask(id, {link: link});
                Helper.addTimeout('task_link' + id, () => {
                    const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                    Helper.fetchJsonPost(url, {'link': link})
                        .then(() => setLinkChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskReminder: (id, reminder) => {
                events.updateTask(id, {reminder: reminder});
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                Helper.fetchJsonPost(url, {'reminder': reminder}).then();
            },
            updateTaskStatus: (id, status) => {
                events.updateTask(id, {status: status});
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                Helper.fetchJsonPost(url, {'status': status}).then();
            },
            updateTaskChildrenViewSetting: (id, value) => {
                events.updateTask(id, {isChildrenOpen: value})
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit/settings';
                Helper.fetchJsonPost(url, {'isChildrenOpen': value}).then();
            },
            updateTaskAdditionalPanelViewSetting: (id, value) => {
                events.updateTask(id, {isAdditionalPanelOpen: value})
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit/settings';
                Helper.fetchJsonPost(url, {'isAdditionalPanelOpen': value}).then();
            }
        }
    }

    useEffect(events.reload, [fetchFrom]);

    const renderTaskList = () => {
        if (tasks === undefined) {
            return "loading ..."
        }
        if (tasks.length === 0) {
            return "no records found";
        }
        const getChildren = (parent) => {
            if (nested === false) {
                return tasks;
            }
            return tasks.filter(task => task.parent === parent);
        }
        const data = {tasks: tasks, statuses: statuses, nested: nested};
        return <TaskList data={data} children={getChildren(root)} events={events}/>;
    }

    return (
        <div>
            <Navbar events={events} root={root} tasks={tasks} />
            {renderTaskList()}
        </div>
    );
}

export default TasksPage;

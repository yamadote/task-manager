
import React, {useEffect, useState} from 'react';
import TaskList from './TaskList/TaskList'
import {Route} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";

const TaskPageHandlers = () => {
    const [tasks, setTasks] = useState(undefined);
    const [statuses, setStatuses] = useState(undefined);
    const events = new function () {
        return {
            updateTask: (id, update) => {
                setTasks(tasks => {
                    return tasks.map(task => {
                        if (task.id === id) {
                            task = update(task);
                        }
                        return task;
                    });
                })
            },
            createNewTask: (parent = null) => {
                const url = Config.apiUrlPrefix + '/tasks/new';
                Helper.fetchJsonPost(url, {'parent': parent?.id})
                    .then(task => {
                        setTasks(tasks => [task, ...tasks])
                        if (parent !== null) {
                            events.updateTaskChildrenViewSetting(parent.id, true);
                        }
                    });
            },
            removeTask: (task) => {
                const url = Config.apiUrlPrefix + '/tasks/' + task.id + '/delete';
                fetch(url, {method: 'POST'})
                    .then(() => {
                        // todo: remove task children
                        setTasks(tasks => tasks.filter(i => i.id !== task.id))
                    })
            },
            updateTaskTitle: (id, title, setTitleChanging) => {
                setTitleChanging(true);
                events.updateTask(id, (task) => {
                    task.title = title;
                    return task;
                });
                Helper.addTimeout('task_title' + id, () => {
                    const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                    Helper.fetchJsonPost(url, {'title': title})
                        .then(() => setTitleChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskLink: (id, link, setLinkChanging) => {
                setLinkChanging(true);
                events.updateTask(id, (task) => {
                    task.link = link;
                    return task;
                });
                Helper.addTimeout('task_link' + id, () => {
                    const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                    Helper.fetchJsonPost(url, {'link': link})
                        .then(() => setLinkChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskStatus: (id, status) => {
                events.updateTask(id, (task) => {
                    task.status = status;
                    return task;
                });
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                Helper.fetchJsonPost(url, {'status': status}).then();
            },
            updateTaskChildrenViewSetting: (id, value) => {
                events.updateTask(id, (task) => {
                    task.isChildrenOpen = value;
                    return task;
                })
            },
            updateTaskAdditionalPanelViewSetting: (id, value) => {
                events.updateTask(id, (task) => {
                    task.isAdditionalPanelOpen = value;
                    return task;
                })
            }
        }
    }
    const renderTaskPage = (path, fetchFrom, nested = true) => {
        const init = (url) => {
            setTasks(undefined);
            fetch(url)
                .then(response => response.json())
                .then(response => {
                    setStatuses(response.statuses);
                    setTasks(response.tasks);
                });
        };
        const data = {tasks: tasks, statuses: statuses, nested: nested}
        return (
            <Route path={path}>
                <TasksPage data={data} init={init} fetchFrom={fetchFrom} events={events}/>
            </Route>
        )
    }
    return {renderTaskPage, events};
}

const TasksPage = ({data, init, fetchFrom, events}) => {
    useEffect(() => init(fetchFrom), [fetchFrom]);
    const {tasks, nested} = data;
    if (tasks === undefined) {
        return "loading ...";
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
    return <TaskList data={data} children={getChildren(null)} events={events}/>;
}

export default TaskPageHandlers;

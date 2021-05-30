
import React, {useEffect, useState} from 'react';
import TaskList from './TaskList/TaskList'
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import Navbar from "./Navbar/Navbar";

const TasksPage = ({fetchFrom, nested = true}) => {
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
                            task = update(task);
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
                            events.updateTask(parent, (task) => {
                                task.isChildrenOpen = true;
                                return task;
                            })
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
            updateTaskReminder: (id, reminder) => {
                events.updateTask(id, (task) => {
                    task.reminder = reminder;
                    return task;
                });
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                Helper.fetchJsonPost(url, {'reminder': reminder}).then();
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
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit/settings';
                Helper.fetchJsonPost(url, {'isChildrenOpen': value}).then();
            },
            updateTaskAdditionalPanelViewSetting: (id, value) => {
                events.updateTask(id, (task) => {
                    task.isAdditionalPanelOpen = value;
                    return task;
                })
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
        return <TaskList data={data} children={getChildren(null)} events={events}/>;
    }

    return (
        <div>
            <Navbar events={events} />
            {renderTaskList()}
        </div>
    );
}

export default TasksPage;


import React, {useEffect, useState} from 'react';
import TaskList from './TaskList/TaskList'
import {Route} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";

const TaskPageHandlers = () => {
    const [tasks, setTasks] = useState(undefined);
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
                    .then(task => setTasks(tasks => [task, ...tasks]))
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
                });
            },
            updateTaskChildrenViewSetting: (id) => {
                events.updateTask(id, (task) => {
                    task.isChildrenOpen = !task.isChildrenOpen;
                    return task;
                })
            },
            updateTaskAdditionalPanelViewSetting: (id) => {
                events.updateTask(id, (task) => {
                    task.isAdditionalPanelOpen = !task.isAdditionalPanelOpen;
                    return task;
                })
            }
        }
    }
    const renderTaskPage = (path, fetchFrom, nested = true) => {
        return (
            <Route path={path}>
                <TasksPage
                    fetchFrom={fetchFrom}
                    nested={nested}
                    tasks={tasks}
                    init={(url) => {
                        setTasks(undefined);
                        fetch(url)
                            .then(response => response.json())
                            .then(tasks => setTasks(tasks));
                    }}
                    events={events}
                />
            </Route>
        )
    }
    return [renderTaskPage, events];
}

const TasksPage = (props) => {
    const tasks = props.tasks;
    useEffect(() => props.init(props.fetchFrom), [props.fetchFrom]);
    if (tasks === undefined) {
        return "loading ...";
    }
    if (tasks.length === 0) {
        return "no tasks found, please create new task";
    }
    const getChildren = (id) => {
        if (props.nested === false) {
            return tasks;
        }
        return tasks.filter(task => {
            return task.parent === id;
        });
    }
    return (<TaskList tasks={tasks} children={getChildren(null)} nested={props.nested} events={props.events}/>);
}

export default TaskPageHandlers;

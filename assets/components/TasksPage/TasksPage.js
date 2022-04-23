
import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import TaskListWrapper from "./TaskListWrapper/TaskListWrapper";
import Footer from "./Footer/Footer";
import Header from "../Header/Header";
import Sidebar from "../Sidebar/Sidebar";
import TaskPanelHeading from "./TaskPanelHeading/TaskPanelHeading";

const TasksPage = ({title, fetchFrom, nested = true}) => {

    const findRootTask = (params, tasks, previousRootTask) => {
        if (!params.root || !params.root.match(new RegExp('^[0-9]+$'))) {
            return null;
        }
        const id = parseInt(params.root);
        return {id: id, ...previousRootTask, ...tasks?.find(task => task.id === id)};
    }
    const isTaskVisible = (task, search, tasks) => {
        if (task.title.toLowerCase().includes(search.toLowerCase())) {
            return true;
        }
        return tasks.find(child => child.parent === task.id && isTaskVisible(child, search, tasks)) !== undefined;
    }

    const params = useParams();
    const [root, setRoot] = useState(findRootTask(params))
    const [tasks, setTasks] = useState(undefined);
    const [statuses, setStatuses] = useState(undefined);
    const [search, setSearch] = useState("");
    const [activeTask, setActiveTask] = useState(undefined);
    const [reminderNumber, setReminderNumber] = useState(undefined);

    const events = new function () {
        return {
            reload: () => {
                setTasks([]);
                Helper.fetchJson(fetchFrom)
                    .then(response => {
                        let tasks = response.tasks.map(task => {
                            task.isHidden = !isTaskVisible(task, search, response.tasks);
                            return task;
                        });
                        setStatuses(response.statuses);
                        setActiveTask(response.activeTask);
                        setTasks(tasks);
                        setRoot(findRootTask(params, tasks, root));
                        setReminderNumber(response.reminderNumber);
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
                const url = Helper.getNewTaskUrl();
                Helper.fetchJsonPost(url, {'parent': parent})
                    .then(task => {
                        setTasks(tasks => [task, ...tasks])
                        if (parent !== null) {
                            events.updateTask(parent, {isChildrenOpen: true})
                        }
                    });
            },
            startTask: (id) => {
                const url = Helper.getTaskStartUrl(id);
                Helper.fetchJsonPost(url)
                    .then(response => setActiveTask({task: id, trackedTime: 0, path: response.activeTask.path}));
            },
            finishTask: (id) => {
                const url = Helper.getTaskFinishUrl(id);
                Helper.fetchJsonPost(url)
                    .then(() => setActiveTask(undefined));
            },
            removeTask: (id) => {
                const task = tasks.find(task => task.id === id);
                if (!confirm("Are you sure, you want to remove '" + task.title + "'?")) {
                    return;
                }
                const url = Helper.getTaskDeleteUrl(id);
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
                    const url = Helper.getTaskEditUrl(id);
                    Helper.fetchJsonPost(url, {'title': title})
                        .then(() => setTitleChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskLink: (id, link, setLinkChanging) => {
                setLinkChanging(true);
                events.updateTask(id, {link: link});
                Helper.addTimeout('task_link' + id, () => {
                    const url = Helper.getTaskEditUrl(id);
                    Helper.fetchJsonPost(url, {'link': link})
                        .then(() => setLinkChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskReminder: (task, reminder) => {
                const time = Math.floor(Date.now() / 1000);
                const taskWasReminder = task.reminder && task.reminder < time;
                const taskWillBeReminder = reminder && reminder < time;

                if (taskWasReminder && !taskWillBeReminder) setReminderNumber(reminderNumber - 1);
                if (!taskWasReminder && taskWillBeReminder) setReminderNumber(reminderNumber + 1);

                events.updateTask(task.id, {reminder: reminder});
                const url = Helper.getTaskEditUrl(task.id);
                Helper.fetchJsonPost(url, {'reminder': reminder}).then();
            },
            updateTaskStatus: (id, status) => {
                events.updateTask(id, {status: status});
                const url = Helper.getTaskEditUrl(id);
                Helper.fetchJsonPost(url, {'status': status}).then();
            },
            updateTaskChildrenViewSetting: (id, value) => {
                events.updateTask(id, {isChildrenOpen: value})
                const url = Helper.getTaskEditSettingsUrl(id);
                Helper.fetchJsonPost(url, {'isChildrenOpen': value}).then();
            },
            updateTaskAdditionalPanelViewSetting: (id, value) => {
                events.updateTask(id, {isAdditionalPanelOpen: value})
                const url = Helper.getTaskEditSettingsUrl(id);
                Helper.fetchJsonPost(url, {'isAdditionalPanelOpen': value}).then();
            },
            updateTaskDescription: (id, description, setDescriptionChanging) => {
                setDescriptionChanging(true);
                events.updateTask(id, {description: description})
                Helper.addTimeout('task_description' + id, () => {
                    const url = Helper.getTaskEditUrl(id);
                    Helper.fetchJsonPost(url, {'description': description})
                        .then(() => setDescriptionChanging(false));
                }, Config.updateInputTimeout);
            },
            onSearchUpdate: () => {
                setTasks((tasks) => tasks.map(task => {
                    task.isHidden = !isTaskVisible(task, search, tasks);
                    return task;
                }));
            }
        }
    }

    useLayoutEffect(events.reload, [fetchFrom]);
    useLayoutEffect(events.onSearchUpdate, [search]);
    useLayoutEffect(() => setRoot(findRootTask(params, tasks)), [params.root]);

    return (
        <div>
            <Header/>
            <div className="container-fluid main-container">
                <div className="row row-offcanvas row-offcanvas-left">
                    <Sidebar root={root} onSearch={setSearch} reminderNumber={reminderNumber}/>
                    <div className="col-xs-12 col-sm-9 content">
                        <div className="panel panel-default">
                            <TaskPanelHeading title={title} root={root} events={events}/>
                            <div className="panel-body">
                                <div className="content-row">
                                    <div className="row">
                                        <TaskListWrapper data={{
                                            root: root,
                                            tasks: tasks,
                                            activeTask: activeTask,
                                            statuses: statuses,
                                            nested: nested
                                        }} events={events} />
                                        <Footer tasks={tasks} root={root} nested={nested}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default TasksPage;

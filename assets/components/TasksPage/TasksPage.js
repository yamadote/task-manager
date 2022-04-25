
import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import TaskListWrapper from "./TaskListWrapper/TaskListWrapper";
import TaskPanelHeading from "./TaskPanelHeading/TaskPanelHeading";
import Page from "../Page/Page";
import PanelBody from "../Page/PanelBody/PanelBody";
import TasksAmount from "./TasksAmount/TasksAmount";
import TasksCalendar from "./TasksCalendar/TasksCalendar";

const TasksPage = ({title, fetchFrom, nested = true}) => {

    const findRootTask = (params) => {
        if (!params.root || !params.root.match(new RegExp('^[0-9]+$'))) {
            return null;
        }
        return {id: parseInt(params.root)};
    }
    const composeRootTask = (root, previousRoot, tasks) => {
        if (!root) {
            return null;
        }
        return {...root, ...previousRoot, ...tasks?.find(task => task.id === root.id)};
    }
    const checkRootTask = (task, root, tasks) => {
        if (task.parent === null) {
            return false;
        }
        if (task.parent === root.id) {
            return true;
        }
        return checkRootTask(tasks.find(parent => parent.id === task.parent), root, tasks);
    }
    const isTaskVisible = (task, search, tasks, root) => {
        if (root && !checkRootTask(task, root, tasks)) {
            return false;
        }
        if (task.title.toLowerCase().includes(search.toLowerCase())) {
            return true;
        }
        if (task.link && Helper.isGithubLink(task.link) && Helper.getGithubIssueNumber(task.link).includes(search)) {
            return true;
        }
        return tasks.find(child => child.parent === task.id && isTaskVisible(child, search, tasks, root)) !== undefined;
    }

    const params = useParams();
    const [root, setRoot] = useState(findRootTask(params))
    const [tasks, setTasks] = useState(undefined);
    const [showCalendar, setShowCalendar] = useState(false);
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
                        const newRoot = findRootTask(params)
                        const tasks = response.tasks.map(task => {
                            task.isHidden = !isTaskVisible(task, search, response.tasks, newRoot);
                            return task;
                        });
                        setStatuses(response.statuses);
                        setActiveTask(response.activeTask);
                        setTasks(tasks);
                        setRoot(composeRootTask(newRoot, root, tasks));
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
                    task.isHidden = !isTaskVisible(task, search, tasks, root);
                    return task;
                }));
            },
            onRootUpdate: () => {
                const newRoot = findRootTask(params);
                setTasks((tasks) => tasks.map(task => {
                    task.isHidden = !isTaskVisible(task, search, tasks, newRoot);
                    return task;
                }));
                setRoot(composeRootTask(newRoot, root, tasks))
            },
            toggleCalendar: () => {
                setShowCalendar(showCalendar => !showCalendar);
            }
        }
    }

    useLayoutEffect(events.reload, [fetchFrom]);
    useLayoutEffect(events.onSearchUpdate, [search]);
    useLayoutEffect(events.onRootUpdate, [params.root]);

    return (
        <Page sidebar={{root: root, onSearch:setSearch, reminderNumber:reminderNumber}}>
            <TaskPanelHeading title={title} root={root} events={events}/>
            {showCalendar ? <TasksCalendar tasks={tasks} /> : null}
            <PanelBody>
                <TaskListWrapper data={{
                    root: root,
                    tasks: tasks,
                    activeTask: activeTask,
                    statuses: statuses,
                    nested: nested
                }} events={events} />
                <TasksAmount tasks={tasks} />
            </PanelBody>
        </Page>
    );
}

export default TasksPage;

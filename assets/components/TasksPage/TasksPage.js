
import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import Navbar from "./Navbar/Navbar";
import TaskListWrapper from "./TaskListWrapper/TaskListWrapper";
import Footer from "./Footer/Footer";

const TasksPage = ({fetchFrom, nested = true}) => {

    const findRootTask = (params, tasks, oldRootTask) => {
        if (!params.root || !params.root.match(new RegExp('^[0-9]+$'))) {
            return null;
        }
        const id = parseInt(params.root);
        return {
            id: id,
            ...oldRootTask,
            ...tasks?.find(task => task.id === id)
        };
    }

    const params = useParams();
    const [root, setRoot] = useState(findRootTask(params))
    const [tasks, setTasks] = useState(undefined);
    const [statuses, setStatuses] = useState(undefined);
    const [activeTask, setActiveTask] = useState(undefined);

    const events = new function () {
        return {
            reload: () => {
                setTasks([]);
                Helper.fetchJson(fetchFrom)
                    .then(response => {
                        setStatuses(response.statuses);
                        setActiveTask(response.activeTask);
                        setTasks(response.tasks);
                        setRoot(findRootTask(params, response.tasks, root));
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
            startTask: (id) => {
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/start';
                Helper.fetchJsonPost(url)
                    .then(response => setActiveTask({task: id, trackedTime: 0, path: response.activeTask.path}));
            },
            finishTask: (id) => {
                const url = Config.apiUrlPrefix + '/tasks/' + id + '/finish';
                Helper.fetchJsonPost(url)
                    .then(() => setActiveTask(undefined));
            },
            removeTask: (id) => {
                const task = tasks.find(task => task.id === id);
                if (!confirm("Are you sure, you want to remove '" + task.title + "'?")) {
                    return;
                }
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
            },
            updateTaskDescription: (id, description, setDescriptionChanging) => {
                setDescriptionChanging(true);
                events.updateTask(id, {description: description})
                Helper.addTimeout('task_description' + id, () => {
                    const url = Config.apiUrlPrefix + '/tasks/' + id + '/edit';
                    Helper.fetchJsonPost(url, {'description': description})
                        .then(() => setDescriptionChanging(false));
                }, Config.updateInputTimeout);
            },
            updateTaskTrackedTime: (id, trackedTime) => {
                events.updateTask(id, {trackedTime: trackedTime})
            },
            updateTaskChildrenTrackedTime: (id, childrenTrackedTime) => {
                events.updateTask(id, {childrenTrackedTime: childrenTrackedTime})
            }
        }
    }

    useLayoutEffect(events.reload, [fetchFrom]);
    useLayoutEffect(() => setRoot(findRootTask(params, tasks)), [params.root]);

    return (
        <div>
            {/*<Navbar events={events} root={root}/>*/}
            <TaskListWrapper data={{
                root: root,
                tasks: tasks,
                activeTask: activeTask,
                statuses: statuses,
                nested: nested
            }} events={events} />
            <Footer tasks={tasks} root={root} nested={nested}/>
        </div>
    );
}

export default TasksPage;

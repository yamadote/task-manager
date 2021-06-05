
import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import Navbar from "./Navbar/Navbar";
import TaskListWrapper from "./TaskListWrapper/TaskListWrapper";

const TasksPage = ({fetchFrom, nested = true}) => {

    const findRootTask = (params, tasks, oldRootTask) => {
        if (!params.root) {
            return null;
        }
        if (!params.root.match(new RegExp('^[0-9]+$'))) {
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

    const events = new function () {
        return {
            reload: () => {
                setTasks(undefined);
                Helper.fetchJson(fetchFrom)
                    .then(response => {
                        setStatuses(response.statuses);
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

    useLayoutEffect(events.reload, [fetchFrom]);
    useLayoutEffect(() => setRoot(findRootTask(params, tasks)), [params.root]);

    return (
        <div>
            <Navbar events={events} root={root}/>
            <TaskListWrapper data={{
                root: root,
                tasks: tasks,
                statuses: statuses,
                nested: nested
            }} events={events} />
        </div>
    );
}

export default TasksPage;

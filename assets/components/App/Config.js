
const Config = new function () {
    return {
        apiUrlPrefix: "/internal-api",
        updateInputTimeout: 1200,
        historyActionSpacerTime: 300,
        githubUrlPrefix: "https://github.com/",
        reminderTaskColor: 'rgb(255, 99, 71)',
        activeTaskColor: '#ffb6c1',
        repeatedActionTypes: ["editTaskTitle", "editTaskDescription"],
        repeatedActionMaxAmount: 4
    }
}

export default Config;

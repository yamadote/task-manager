
import React, {useState} from 'react';
import ReactMde from "react-mde";
import * as Showdown from "showdown";
import './TaskDescriptionEditor.scss';
import "react-mde/lib/styles/css/react-mde-all.css";

const TaskDescriptionEditor = ({task, events}) => {
    const converter = new Showdown.Converter({
        tables: true,
        simplifiedAutoLink: true,
        strikethrough: true,
        tasklists: true
    });
    const [isDescriptionChanging, setDescriptionChanging] = useState(false);
    const [selectedTab, setSelectedTab] = React.useState("preview");
    return (
        <div className={"task-description-editor " + (isDescriptionChanging ? "changing" : "")}>
            <ReactMde
                value={task.description ?? undefined}
                onChange={description => events.updateTaskDescription(task.id, description, setDescriptionChanging)}
                selectedTab={selectedTab}
                onTabChange={setSelectedTab}
                minEditorHeight={210}
                generateMarkdownPreview={markdown =>
                    Promise.resolve(converter.makeHtml(markdown))
                }
            />
        </div>
    );
}

export default TaskDescriptionEditor;

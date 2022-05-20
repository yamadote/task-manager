
import React from 'react';
import moment from "moment";
import parser, {Tag} from 'bbcode-to-react';

class TitleTag extends Tag {
    toReact() {
        const title = this.getContent(true);
        return <span>"{title}"</span>;
    }
}

class LinkTag extends Tag {
    toReact() {
        const link = this.getContent(true);
        return <a href={link}>{link}</a>;
    }
}

class ReminderTag extends Tag {
    toReact() {
        const timestamp = this.getContent(true);
        const time = moment.unix(timestamp).format('YYYY/MM/DD HH:mm dddd');
        return <b>{time}</b>;
    }
}

class StatusTag extends Tag {
    toReact() {
        const title = this.getContent(true);
        const slug = this.params.slug;
        return <span className={"status-" + slug}>"{title}"</span>;
    }
}

parser.registerTag('title', TitleTag);
parser.registerTag('link', LinkTag);
parser.registerTag('reminder', ReminderTag);
parser.registerTag('status', StatusTag);

const Action = ({action, isMergedTaskColumn}) => {
    const getStatusClassName = () => {
        if (action.type === 'createTask') {
            return 'info';
        }
        if (action.type === 'editTaskStatus') {
            return 'warning';
        }
        return '';
    }
    const statusClassName = getStatusClassName();
    const time = moment.unix(action.createdAt).format('HH:mm');
    const message = parser.toReact(action.message);
    const task = isMergedTaskColumn ? null : action.task.title;
    return (
        <tr>
            <td className={"column time-column " + statusClassName}>
                <div className="column-content">{time}</div>
            </td>
            <td className={"column message-column " + statusClassName}>
                <div className="column-content">{message}</div>
            </td>
            <td className={"column task-column " + (isMergedTaskColumn ? 'merged-column' : '')}>
                <div className="column-content">{task}</div>
            </td>
        </tr>
    );
}

export default Action;

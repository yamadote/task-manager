import React from 'react';
import ReactDOM from 'react-dom';
import './styles.css';

class App extends React.Component {
    constructor() {
        super();

        this.state = {
            entries: []
        };
    }

    componentDidMount() {
        // fetch('https://jsonplaceholder.typicode.com/posts/')
        //     .then(response => response.json())
        //     .then(entries => {
        //         this.setState({
        //             entries
        //         });
        //     });
    }

    render() {
        console.log("render");
        return (
            <div className="row">
                Test component
                {/*{this.state.entries.map(*/}
                {/*    ({ id, title, body }) => (*/}
                {/*        <Items*/}
                {/*            key={id}*/}
                {/*            title={title}*/}
                {/*            body={body}*/}
                {/*        >*/}
                {/*        </Items>*/}
                {/*    )*/}
                {/*)}*/}
            </div>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('root'));

'use strict';

class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            order_by: {
                name: 'ip',
                order: 'asc'
            },
            visits: []
        } ;

        this.setOrderBy = this.setOrderBy.bind(this);
    }

    componentDidMount() {
        this.loadData();
    }

    loadData() {
        let params = {
            sort_by : this.state.order_by.name,
            sort_order : this.state.order_by.order
        };
        $.get('/api/v1/visits', params, (response)  => {
            this.setState({
                visits: response.visits
            });
        });
    }

    setOrderBy(fieldName) {
        let sortOrder = null;

        if (this.state.order_by.name === fieldName) {
            sortOrder = this.state.order_by.order == 'asc' ? 'desc' : 'asc';
        } else {
            sortOrder = 'asc';
        }

        let state = Object.assign({}, this.state, {
            order_by : {
                name : fieldName,
                order: sortOrder
            }
        });

        this.setState(state, () => {
            this.loadData();
        });
    }

    render() {
        if (this.state.liked) {
            return 'You liked this.';
        }

        return (
            <div>
                <h1>Посетители</h1>
                <Table visits={this.state.visits} onSetOrder={this.setOrderBy} />
            </div>
        );
    }
}

class Table extends React.Component {

    constructor(props) {
        super(props);
        this.setOrderBy = this.setOrderBy.bind(this);
    }

    setOrderBy(fieldName) {
        this.props.onSetOrder(fieldName);
    }

    render() {

        let visits = this.props.visits;
        let visitListHtml = visits.map(visit => {
            return (
                <tr key={visit.ip}>
                    <td>{visit.ip}</td>
                    <td>{visit.browser}</td>
                    <td>{visit.os}</td>
                    <td>{visit.first_referer}</td>
                    <td>{visit.last_path}</td>
                    <td>{visit.unique_visits}</td>
                </tr>
            )
        });

        return (
            <div>
                <table className="table">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th><a href="#" onClick={() => {this.setOrderBy('browser')}}>Browser</a></th>
                            <th><a href="#" onClick={() => {this.setOrderBy('os')}}>OS</a></th>
                            <th>Source</th>
                            <th>Last page</th>
                            <th>Total pages</th>
                        </tr>
                    </thead>
                    <tbody>
                        {visitListHtml}
                    </tbody>
                </table>
            </div>
        )
    }
}

let domContainer = document.querySelector('#app');
let app = React.createElement(App, null);
ReactDOM.render(app, domContainer);
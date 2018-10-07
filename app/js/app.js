'use strict';

class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            order_by: {
                name: 'ip',
                order: 'asc'
            },
            filter: {
                ip : ''
            },
            visits: [],
            pagination: {
                current : 1,
                total   : 1
            }
        } ;

        this.setOrderBy = this.setOrderBy.bind(this);
        this.setFilter = this.setFilter.bind(this);
        this.setPageNumber = this.setPageNumber.bind(this);
    }

    componentDidMount() {
        this.loadData();
    }

    loadData() {
        let params = {
            sort_by : this.state.order_by.name,
            sort_order : this.state.order_by.order,
            filter: this.state.filter,
            page: this.state.pagination.current
        };
        $.get('/api/v1/visits', params, (response)  => {
            this.setState({
                visits: response.visits,
                pagination: {
                    current : this.state.pagination.current,
                    total   : response.page_count
                }
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

    setFilter(name, value) {
        let state = Object.assign({}, this.state);
        state.filter[name] = value;
        state.pagination.current = 1;

        this.setState(state, () => {
            this.loadData();
        });
    }

    setPageNumber(number) {
        let state = Object.assign({}, this.state);
        state.pagination.current = number;

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
                <Table
                    visits={this.state.visits}
                    filter={this.state.filter}
                    totalPages={this.state.pagination.total}
                    currentPage={this.state.pagination.current}
                    onSetOrder={this.setOrderBy}
                    onSetFilter={this.setFilter}
                    onSetPageNumber={this.setPageNumber}
                />
            </div>
        );
    }
}

class Table extends React.Component {

    constructor(props) {
        super(props);
        this.setOrderBy = this.setOrderBy.bind(this);
        this.setFilter  = this.setFilter.bind(this);
        this.setPageNumber  = this.setPageNumber.bind(this);
    }

    setOrderBy(fieldName) {
        this.props.onSetOrder(fieldName);
    }

    setFilter(event) {
        this.props.onSetFilter('ip', event.target.value);
    }

    setPageNumber(event) {
        this.props.onSetPageNumber(event.target.getAttribute('data-page'));
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


        let nav = [];
        for (let i = 1; i <= this.props.totalPages; i ++) {
            let classNames = ['page-item'];
            if (i == this.props.currentPage) {
                classNames.push('active');
            }
            nav.push(
                <li className={classNames.join(' ')} key={'p' + i}><a className="page-link" href="#" data-page={i} onClick={this.setPageNumber}>{i}</a></li>
            );
        }

        return (
            <div>
                <table className="table">
                    <thead>
                        <tr>
                            <th>
                                IP<br />
                                <input type="text" value={this.props.filter.ip} onChange={this.setFilter}/>
                            </th>
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
                <nav>
                    <ul className="pagination">
                        {nav}
                    </ul>
                </nav>
            </div>
        )
    }
}

let domContainer = document.querySelector('#app');
let app = React.createElement(App, null);
ReactDOM.render(app, domContainer);
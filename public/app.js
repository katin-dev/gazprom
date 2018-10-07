'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var App = function (_React$Component) {
    _inherits(App, _React$Component);

    function App(props) {
        _classCallCheck(this, App);

        var _this = _possibleConstructorReturn(this, (App.__proto__ || Object.getPrototypeOf(App)).call(this, props));

        _this.state = {
            order_by: {
                name: 'ip',
                order: 'asc'
            },
            filter: {
                ip: ''
            },
            visits: [],
            pagination: {
                current: 1,
                total: 1
            }
        };

        _this.setOrderBy = _this.setOrderBy.bind(_this);
        _this.setFilter = _this.setFilter.bind(_this);
        _this.setPageNumber = _this.setPageNumber.bind(_this);
        return _this;
    }

    _createClass(App, [{
        key: 'componentDidMount',
        value: function componentDidMount() {
            this.loadData();
        }
    }, {
        key: 'loadData',
        value: function loadData() {
            var _this2 = this;

            var params = {
                sort_by: this.state.order_by.name,
                sort_order: this.state.order_by.order,
                filter: this.state.filter,
                page: this.state.pagination.current
            };
            $.get('/api/v1/visits', params, function (response) {
                _this2.setState({
                    visits: response.visits,
                    pagination: {
                        current: _this2.state.pagination.current,
                        total: response.page_count
                    }
                });
            });
        }
    }, {
        key: 'setOrderBy',
        value: function setOrderBy(fieldName) {
            var _this3 = this;

            var sortOrder = null;

            if (this.state.order_by.name === fieldName) {
                sortOrder = this.state.order_by.order == 'asc' ? 'desc' : 'asc';
            } else {
                sortOrder = 'asc';
            }

            var state = Object.assign({}, this.state, {
                order_by: {
                    name: fieldName,
                    order: sortOrder
                }
            });

            this.setState(state, function () {
                _this3.loadData();
            });
        }
    }, {
        key: 'setFilter',
        value: function setFilter(name, value) {
            var _this4 = this;

            var state = Object.assign({}, this.state);
            state.filter[name] = value;
            state.pagination.current = 1;

            this.setState(state, function () {
                _this4.loadData();
            });
        }
    }, {
        key: 'setPageNumber',
        value: function setPageNumber(number) {
            var _this5 = this;

            var state = Object.assign({}, this.state);
            state.pagination.current = number;

            this.setState(state, function () {
                _this5.loadData();
            });
        }
    }, {
        key: 'render',
        value: function render() {
            if (this.state.liked) {
                return 'You liked this.';
            }

            return React.createElement(
                'div',
                null,
                React.createElement(
                    'h1',
                    null,
                    '\u041F\u043E\u0441\u0435\u0442\u0438\u0442\u0435\u043B\u0438'
                ),
                React.createElement(Table, {
                    visits: this.state.visits,
                    filter: this.state.filter,
                    totalPages: this.state.pagination.total,
                    currentPage: this.state.pagination.current,
                    onSetOrder: this.setOrderBy,
                    onSetFilter: this.setFilter,
                    onSetPageNumber: this.setPageNumber
                })
            );
        }
    }]);

    return App;
}(React.Component);

var Table = function (_React$Component2) {
    _inherits(Table, _React$Component2);

    function Table(props) {
        _classCallCheck(this, Table);

        var _this6 = _possibleConstructorReturn(this, (Table.__proto__ || Object.getPrototypeOf(Table)).call(this, props));

        _this6.setOrderBy = _this6.setOrderBy.bind(_this6);
        _this6.setFilter = _this6.setFilter.bind(_this6);
        _this6.setPageNumber = _this6.setPageNumber.bind(_this6);
        return _this6;
    }

    _createClass(Table, [{
        key: 'setOrderBy',
        value: function setOrderBy(fieldName) {
            this.props.onSetOrder(fieldName);
        }
    }, {
        key: 'setFilter',
        value: function setFilter(event) {
            this.props.onSetFilter('ip', event.target.value);
        }
    }, {
        key: 'setPageNumber',
        value: function setPageNumber(event) {
            this.props.onSetPageNumber(event.target.getAttribute('data-page'));
        }
    }, {
        key: 'render',
        value: function render() {
            var _this7 = this;

            var visits = this.props.visits;
            var visitListHtml = visits.map(function (visit) {
                return React.createElement(
                    'tr',
                    { key: visit.ip },
                    React.createElement(
                        'td',
                        null,
                        visit.ip
                    ),
                    React.createElement(
                        'td',
                        null,
                        visit.browser
                    ),
                    React.createElement(
                        'td',
                        null,
                        visit.os
                    ),
                    React.createElement(
                        'td',
                        null,
                        visit.first_referer
                    ),
                    React.createElement(
                        'td',
                        null,
                        visit.last_path
                    ),
                    React.createElement(
                        'td',
                        null,
                        visit.unique_visits
                    )
                );
            });

            var nav = [];
            for (var i = 1; i <= this.props.totalPages; i++) {
                var classNames = ['page-item'];
                if (i == this.props.currentPage) {
                    classNames.push('active');
                }
                nav.push(React.createElement(
                    'li',
                    { className: classNames.join(' '), key: 'p' + i },
                    React.createElement(
                        'a',
                        { className: 'page-link', href: '#', 'data-page': i, onClick: this.setPageNumber },
                        i
                    )
                ));
            }

            return React.createElement(
                'div',
                null,
                React.createElement(
                    'table',
                    { className: 'table' },
                    React.createElement(
                        'thead',
                        null,
                        React.createElement(
                            'tr',
                            null,
                            React.createElement(
                                'th',
                                null,
                                'IP',
                                React.createElement('br', null),
                                React.createElement('input', { type: 'text', value: this.props.filter.ip, onChange: this.setFilter })
                            ),
                            React.createElement(
                                'th',
                                null,
                                React.createElement(
                                    'a',
                                    { href: '#', onClick: function onClick() {
                                            _this7.setOrderBy('browser');
                                        } },
                                    'Browser'
                                )
                            ),
                            React.createElement(
                                'th',
                                null,
                                React.createElement(
                                    'a',
                                    { href: '#', onClick: function onClick() {
                                            _this7.setOrderBy('os');
                                        } },
                                    'OS'
                                )
                            ),
                            React.createElement(
                                'th',
                                null,
                                'Source'
                            ),
                            React.createElement(
                                'th',
                                null,
                                'Last page'
                            ),
                            React.createElement(
                                'th',
                                null,
                                'Total pages'
                            )
                        )
                    ),
                    React.createElement(
                        'tbody',
                        null,
                        visitListHtml
                    )
                ),
                React.createElement(
                    'nav',
                    null,
                    React.createElement(
                        'ul',
                        { className: 'pagination' },
                        nav
                    )
                )
            );
        }
    }]);

    return Table;
}(React.Component);

var domContainer = document.querySelector('#app');
var app = React.createElement(App, null);
ReactDOM.render(app, domContainer);
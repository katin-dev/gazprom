var App = function () {

    var self = this;
    this.container = null;
    this.tableTemplate = document.getElementById("entry-template").innerHTML;
    this.sortField = null;
    this.sortOrder = 'asc';

    this.init = function (container) {
        this.container = $(container);
        this.container.on('change', function () {
            self.fetchData();
        });
        this.container.trigger('change');
    };

    this.fetchData = function () {
        var params = {
            sort_by: this.sortField,
            sort_order: this.sortOrder
        };
        $.get('/api/v1/visits', params, function (response) {
            var template = Handlebars.compile(self.tableTemplate);
            var html = template({
                visits: response.visits,
                sort_field: self.sortField,
                sort_direction: self.sortOrder
            });

            self.container.html(html);
            self.bindEvents();
        });
    };

    // Вешаем обработчики на основные события
    this.bindEvents = function () {
        // Сортировка
        this.container.find('table th a').click(function () {
            self.setSortOrder($(this).data('name'));
            return false;
        });
    };

    this.setSortOrder = function(fieldName) {
        if (this.sortField == fieldName) {
            this.sortOrder = this.sortOrder == 'asc' ? 'desc' : 'asc';
        } else {
            this.sortField = fieldName;
            this.sortOrder = 'asc';
        }

        self.container.trigger('change');
    };
};
/*global define, alert*/
define(['underscore', 'backbone', 'routing', 'oronavigation/js/navigation', 'orolocale/js/formatter/datetime', 'autolinker'],
function (_, Backbone, routing, Navigation, dateTimeFormatter, autolinker) {
    'use strict';

    var $ = Backbone.$;

    /**
     * @export  oronote/js/note/view
     * @class   oronote.note.View
     * @extends Backbone.View
     */
    return Backbone.View.extend({
        options: {
            'template': null
        },

        attributes: {
            'class': 'list-item'
        },

        events: {
            'click .item-edit-button': '_edit',
            'click .item-remove-button': '_delete',
            'click .accordion-toggle': '_toggle'
        },

        initialize: function (options) {
            this.options = _.defaults(options || {}, this.options);

            this.template = _.template($(this.options.template).html());

            this.listenTo(this.model, 'change', this._onModelChanged);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function (collapsed) {
            var data = this.model.toJSON();
            data['collapsed'] = _.isUndefined(collapsed) ? false : collapsed;
            data['createdAt'] = dateTimeFormatter.formatDateTime(data['createdAt']);
            data['updatedAt'] = dateTimeFormatter.formatDateTime(data['updatedAt']);
            if (data['createdBy_id'] && data['createdBy_viewable']) {
                data['createdBy_url'] = routing.generate('oro_user_view', {'id': data['createdBy_id']});
            }
            if (data['updatedBy_id'] && data['updatedBy_viewable']) {
                data['updatedBy_url'] = routing.generate('oro_user_view', {'id': data['updatedBy_id']});
            }
            data['message'] = _.escape(data['message']);
            data['brief_message'] = data['message'].replace(new RegExp('\r?\n', 'g'), ' ');
            data['message'] = data['message'].replace(new RegExp('\r?\n', 'g'), '<br />');
            data['message'] = autolinker.link(data['message'], {className: 'no-hash'});
            if (data['brief_message'].length > 200) {
                data['brief_message'] = data['brief_message'].substr(0, 200);
            }
            data['brief_message'] = autolinker.link(data['brief_message'], {className: 'no-hash'});

            var html = this.template(data);

            this.$el.empty();
            this.$el.append(html);

            var navigation = Navigation.getInstance();
            if (navigation) {
                navigation.processClicks(this.$el.find('a'));
            }

            return this;
        },

        _edit: function () {
            this.trigger('edit', this, this.model);
        },

        _delete: function () {
            this.trigger('delete', this, this.model);
        },

        _toggle: function (e) {
            e.preventDefault();

            var $el = $(e.currentTarget);
            $el.toggleClass('collapsed');
            $el.closest('.accordion-group').find('.collapse').toggleClass('in');
        },

        _onModelChanged: function () {
            var collapsed = this.$el.find('.accordion-toggle').hasClass('collapsed');
            this.render(collapsed);
        }
    });
});

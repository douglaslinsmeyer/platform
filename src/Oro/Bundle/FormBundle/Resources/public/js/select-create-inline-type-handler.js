/* global define */
define(['oro/dialog-widget', 'orotranslation/js/translator', 'jquery.select2'],
function (DialogWidget, __) {
    'use strict';

    /**
     * @export  oroform/js/select-create-inline-type-handler
     * @class   oroform.selectCreateInlineTypeHandler
     */
    return function (container,
        selectorEl,
        label,
        gridUrl,
        existingEntityGridId,
        createEnabled,
        entityCreateUrl
    ) {
        var handleGridSelect = function (e) {
            e.preventDefault();

            var entitySelectDialog = new DialogWidget({
                title: __('Select {{ entity }}', {'entity': label}),
                url: gridUrl,
                stateEnabled: false,
                incrementalPosition: false,
                dialogOptions: {
                    modal: true,
                    allowMaximize: true,
                    width: 1280,
                    height: 650
                }
            });

            var processSelectedEntities = function (data) {
                selectorEl.select2('val', data.model.get(existingEntityGridId));
                entitySelectDialog.remove();
            };

            entitySelectDialog.on('grid-row-select', _.bind(processSelectedEntities, this));
            entitySelectDialog.render();
        };

        var handleCreate = function (e) {
            e.preventDefault();

            var entityCreateDialog = new DialogWidget({
                title: __('Create {{ entity }}', {'entity': label}),
                url: entityCreateUrl,
                stateEnabled: false,
                incrementalPosition: false,
                dialogOptions: {
                    modal: true,
                    allowMaximize: true,
                    width: 1280,
                    height: 650
                }
            });

            var processSelectedEntities = function (id) {
                selectorEl.select2('val', id);
                entityCreateDialog.remove();
            };

            entityCreateDialog.on('formSave', _.bind(processSelectedEntities, this));
            entityCreateDialog.render();
        };

        container.find('.entity-select-btn').on('click', handleGridSelect);
        if (createEnabled) {
            container.find('.entity-create-btn').on('click', handleCreate);
        }
    };
});

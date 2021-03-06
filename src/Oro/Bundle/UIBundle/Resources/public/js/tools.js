/*jslint nomen:true*/
/*global define, require*/
define(['underscore'], function (_) {
    'use strict';

    /**
     * @export oroui/js/tools
     * @name   oroui.tools
     */
    return {
        /**
         * Loads dynamic list of modules and execute callback function with passed modules
         *
         * @param {Object.<string, string>} modules where keys are formal module names and values are actual
         * @param {function(Object)} callback
         * @param {Object=} context
         */
        loadModules: function (modules, callback, context) {
            var requirements = _.values(modules);
            // load all dependencies and build grid
            require(requirements, function () {
                _.each(modules, _.bind(function (value, key) {
                    modules[key] = this[value];
                }, _.object(requirements, _.toArray(arguments))));
                callback.call(context || null, modules);
            });
        }
    };
});

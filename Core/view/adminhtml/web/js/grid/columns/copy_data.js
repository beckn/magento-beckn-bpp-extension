define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate) {
    'use strict';
    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_html']
        },
        getEventData: function (row) {
            return row[this.index + '_event_data']
        },
        preview: function (row) {
            var eventData = this.getEventData(row);
            const replaced = eventData.replaceAll("'", '');
            var $temp = $("<input value='"+replaced+"'>");
            $("body").append($temp);
            $temp.select();
            document.execCommand("copy");
            $temp.remove();
            alert("Json Data Copied");
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});

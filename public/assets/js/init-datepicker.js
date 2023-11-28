(function($) {

    'use strict';

    $(document).ready(function() {

        $('.js-datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'YYYY-MM-DD'

        });

        $('.input-group.date').datepicker({
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'YYYY-MM-DD'
        });

        $('.input-daterange').datepicker({
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'YYYY-MM-DD'
        });

    });

})(window.jQuery);

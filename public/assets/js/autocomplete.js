$.widget("ui.autocomplete", $.ui.autocomplete, {

    _renderMenu: function(ul, items) {
        var that = this;
        ul.attr("class", "nav nav-pills nav-stacked  bs-autocomplete-menu");
        $.each(items, function(index, item) {
            that._renderItemData(ul, item);
        });
    },

    _resizeMenu: function() {
        var ul = this.menu.element;
        ul.outerWidth(Math.min(
            // Firefox wraps long text (possibly a rounding bug)
            // so we add 1px to avoid the wrapping (#7513)
            ul.width("").outerWidth() + 1,
            this.element.outerWidth()
        ));
    }

});

(function() {
    "use strict";


    $('.bs-autocomplete').each(function() {
        var _this = $(this),
            _data = _this.data(),
            _hidden_field = $('#' + _data.hidden_field_id);

        _this.after('<div class="bs-autocomplete-feedback form-control-feedback"><div class="loader">Loading... </div></div>')
            .parent('.form-group').addClass('has-feedback');

        var feedback_icon = _this.next('.bs-autocomplete-feedback');
        feedback_icon.hide();

        _this.autocomplete({
            minLength: 2,
            autoFocus: true,
            maxResults: 30,
            source: function(request, response) {
                var cities = [
                    $.getJSON(
                        productfindurl+"?query=" + request.term,
                        function (data) {
                            response(data);
                        })
                ];
                /*
                var _regexp = new RegExp(request.term, 'i');
                var data = cities.filter(function(item) {
                    console.log(item);
                    return item.cityName.match(_regexp);
                });
                response(data);
                */
            },

            search: function() {
                feedback_icon.show();
                _hidden_field.val('');
            },

            response: function() {
                feedback_icon.hide();
            },

            focus: function(event, ui) {
                //_this.val(ui.item[_data.item_label]);
                event.preventDefault();
            },

            select: function(event, ui) {
                _this.val('');
                appendToTable(ui.item);
                event.preventDefault();
            }
        })

            .data('ui-autocomplete')._renderItem = function(ul, item) {
            console.log(item);
            const listUi = $('<li></li>')
                .data("item.autocomplete", item);

            const isMulti = item.stock && item.stock.multi_level && item.stock.units && item.stock.units.length > 0;

            if (isMulti) {
                let html = '<a>' + item.stock.name + '</a>&nbsp;&nbsp;&nbsp;&nbsp;';

                // Render all unit prices
                const priceColors = ['blue', 'purple', 'green', '#e83e8c', '#17a2b8'];
                item.stock.units.forEach(function(u, idx) {
                    const color = priceColors[idx % priceColors.length];
                    const priceFormatted = typeof formatMoney === 'function' ? formatMoney(u.selling_price) : Number(u.selling_price).toFixed(2);
                    html += '<span style="font-size: 15px;color:' + color + '">' + u.name + ' Price : &#x20A6; ' + priceFormatted + '&nbsp;&nbsp;&nbsp;</span>';
                });

                // Render all quantity levels if permission allows
                if (item['show_qty']) {
                    item.stock.units.forEach(function(u) {
                        const av = (u.available !== null && u.available !== undefined) ? u.available : 0;
                        html += '<span style="font-size: 15px;color:red">Ava. ' + u.name + ' : ' + av + '&nbsp;&nbsp;&nbsp;</span>';
                    });
                    if (item.stock.stock_label) {
                        html += '<span style="font-size: 15px;color:#8a6d3b;font-weight:bold">(' + item.stock.stock_label + ')&nbsp;&nbsp;&nbsp;</span>';
                    }
                }

                listUi.append(html);
            } else {
                const stockPrice = typeof formatMoney === 'function' ? formatMoney(item.stock.selling_price) : Number(item.stock.selling_price).toFixed(2);
                const yardPrice = typeof formatMoney === 'function' ? formatMoney(item.stock.yard_selling_price) : Number(item.stock.yard_selling_price).toFixed(2);

                if(item['show_qty']) {
                    listUi.append('<a>' + item['stock']['name'] + '</a>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 15px;color:blue">Price : &#x20A6; ' + stockPrice + '&nbsp;&nbsp;&nbsp;</span><span style="font-size: 15px;color:green">Yard Price : &#x20A6;' + yardPrice + '</span>&nbsp;&nbsp;&nbsp;<span  style="font-size: 15px;color:red">Ava. Qty :' + item.stock.available_quantity + '</span>&nbsp;&nbsp;&nbsp;<span  style="font-size: 15px;color:red">Ava. Yard Qty :' + item.stock.available_yard_quantity + '</span>');
                } else {
                    listUi.append('<a>' + item['stock']['name'] + '</a>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 15px;color:blue">Price : &#x20A6; ' + stockPrice + '&nbsp;&nbsp;&nbsp;</span><span style="font-size: 15px;color:green">Yard Price : &#x20A6;' + yardPrice + '</span>');
                }
            }
            listUi.appendTo(ul);

            return listUi;
        };
        // end autocomplete
    });
})();

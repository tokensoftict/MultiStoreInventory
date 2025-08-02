
function format(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
        '<tr>' +
        '<td>Full name:</td>' +
        '<td>' + d.name + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td>Extension number:</td>' +
        '<td>' + d.extn + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td>Extra info:</td>' +
        '<td>And any further details here (images etc)...</td>' +
        '</tr>' +
        '</table>';
}


// Data Table

$('.convert-data-table').dataTable(
    {
        buttons :[
            {
                extend: 'print',
                autoPrint: true,
                title: '',
                //For repeating heading.
                repeatingHead: {
                    logoPosition: 'right',
                    logoStyle: '',
                    title: '<h3></h3>'
                }
            },
            'copy', 'excel', 'pdf',
        ],
        aLengthMenu: [
            [500, 1000, 1000, 3000,4000, -1],
            [500, 1000, 1000, 3000,4000, "All"]
        ],
        dom:  "<'row be-datatable-header'<'col-sm-2'l><'col-sm-5 text-right'B><'col-sm-4 text-right'f>>" +
            "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    }
);




$('.colvis-data-table').dataTable(
    {
        /*
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ],
         */

        buttons :[
            {
                extend: 'print',
                autoPrint: true,
                title: '',
                //For repeating heading.
                repeatingHead: {
                    logo: 'https://www.google.co.in/logos/doodles/2018/world-cup-2018-day-22-5384495837478912-s.png',
                    logoPosition: 'right',
                    logoStyle: '',
                    title: '<h3></h3>'
                }
            },
            'copy', 'excel', 'pdf',
        ],
        aLengthMenu: [
            [500, 1000, 1000, 3000,4000, -1],
            [500, 1000, 1000, 3000,4000, "All"]
        ],
        dom:  "<'row be-datatable-header'<'col-sm-2'l><'col-sm-5 text-right'B><'col-sm-4 text-right'f>>" +
            "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    }
);


$('.responsive-data-table').dataTable(
    {
        /*
        buttons: [
            'copy', 'excel', 'pdf', 'print'
        ],
         */
        buttons :[
            {
                extend: 'print',
                autoPrint: true,
                title: '',
                //For repeating heading.
                repeatingHead: {
                    logo: 'https://www.google.co.in/logos/doodles/2018/world-cup-2018-day-22-5384495837478912-s.png',
                    logoPosition: 'right',
                    logoStyle: '',
                    title: '<h3></h3>'
                }
            },
            'copy', 'excel', 'pdf',
        ],
        aLengthMenu: [
            [500, 1000, 1000, 3000,4000, -1],
            [500, 1000, 1000, 3000,4000, "All"]
        ],
        dom:  "<'row be-datatable-header'<'col-sm-2'l><'col-sm-5 text-right'B><'col-sm-4 text-right'f>>" +
            "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    }
);


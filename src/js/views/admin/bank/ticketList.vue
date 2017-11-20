<template lang="html">
    <div class="content" id="ticketList">
        <div class="row text-right" id="upperBar"></div>
        <paca-admin-ticket
            :ticket="selectedTicket"
            v-on:saved="reset">
        </paca-admin-ticket>
        <vue-bootstrap-table
            :columns="columns"
            :values="tickets"
            :show-filter="true"
            :show-column-picker="false"
            :sortable="true"
            :paginated="true"
            :multi-column-sortable="true"
            :filter-case-sensitive="false"
        >
        </vue-bootstrap-table>
    </div>
</template>

<script>
var VueBootstrapTable  = require('vue-bootstrap-table');
var messageStore = require('./../../../messages.js');
export default {
    data() {
        return {
            columns: [
                {
                    name: "idTicket",
                    title: "ID"
                },
                {
                    name: "type",
                    title: "Transaction",
                    renderfunction: this.renderTypeColumn
                },
                {
                    name: "description",
                    title: "Description",
                    renderfunction: this.renderTransactionColumn
                },
                {
                    name: "currency",
                    title: "Amount"
                },
                {
                    name: "date",
                    title: "Date"
                },
                {
                    name: "options",
                    title: "Options",
                    visible: true,
                    renderfunction: this.renderOptionsColumn
                }
            ],
            tickets: [],
            selectedTicket: {}
        };
    },
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        idTicket: function() {
            return (this.selectedTicket == null ? null : this.selectedTicket.idTicket);
        },
        today: function() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();
            var hh = today.getHours();
            var Mm = today.getMinutes();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            return mm + '/' + dd + '/' + yyyy + ' ' + hh + ':' + Mm;
        }
    },
    methods: {
        reset: function() {
            this.selectedTicket = null;
        },
        loadTickets: function() {
            $.post('./php/controllers/bankController.php', {
                action: 'getTickets'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.tickets = json.tickets;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load ticket list',
                        type: 'error'
                    });
                }
            });
        },
        renderTransactionColumn: function (colname, entry) {
            let description = '';
            switch (entry.type) {
                case 'DEPOSIT': {
                    description += 'User ' + entry.userDestiny + ' recived ' + entry.currency;
                    break;
                }
                case 'WHITDRAW': {
                    description += 'User ' + entry.userDestiny + ' whitdrawn ' + entry.currency;
                    break;
                }
                case 'TRANSFER': {
                    description += 'User ' + entry.userOrigin + ' gave ' + entry.currency + ' to user ' + entry.userDestiny;
                    break;
                }
            }
            return description;
        },
        renderTypeColumn: function(colname, entry) {
            return '<span class="label label-' + (entry.type == 'WHITDRAW' ? 'danger' : 'success') + '">' + entry.type + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-idticket="' + entry.idTicket + '"]').length > 0) {
                    // set up the form button
                    $('ul.dropdown-menu a[data-idticket="' + entry.idTicket + '"][data-option="ticket"]').click((event) => {
                        var id = $(event.target).attr('data-idticket');
                        this.selectedTicket = null;
                        this.selectedTicket = $(this.tickets).filter(function(i,n) {
                            return n.idTicket == id;
                        })[0];
                        $("#ticketModal").modal();
                    });
                    clearTimeout(checker);
                }
            }, 100);
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options ' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-idticket="' + entry.idTicket + '" data-option="ticket">Download ticket</a></li>' +
                '</ul>' +
            '</div>';
        }
    },
    created: function() {
        this.loadTickets();
    }
}
</script>

<style lang="css">
#ticketList #upperBar {
    padding: 30px;
}
</style>

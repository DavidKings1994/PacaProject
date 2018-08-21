<template lang="html">
    <div class="content" id="logList">
        <div class="row text-right" id="upperBar"></div>
        <paca-admin-log
            :log="selectedLog"
            v-on:saved="reset">
        </paca-admin-log>
        <vue-bootstrap-table
            v-if="logs.length > 0"
            :columns="columns"
            :values="logs"
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
var messageStore = require('./../../messages.js');
export default {
    data() {
        return {
            columns: [
                {
                    name: "idLog",
                    title: "ID"
                },
                {
                    name: "description",
                    title: "Description",
                    renderfunction: this.renderDescriptionColumn
                },
                {
                    name: "quantity",
                    title: "Quantity"
                },
                {
                    name: "object",
                    title: "Object"
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
            logs: [],
            selectedLog: {}
        };
    },
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        idLog: function() {
            return (this.selectedLog == null ? null : this.selectedLog.idLog);
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
            this.selectedLog = null;
        },
        loadLogs: function() {
            $.post('./php/controllers/itemController.php', {
                action: 'getLogs'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.logs = json.logs;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load logs',
                        type: 'error'
                    });
                }
            });
        },
        renderDescriptionColumn: function (colname, entry) {
            let description = '';
            if (entry.userOrigin != null) {
                description += 'User ' + entry.userOrigin + ' transfered ' + entry.quantity + ' ' + entry.object + 's to ';
                if (entry.characterDestiny != null) {
                    description += ' character ' + entry.characterDestiny + ' of user ' + entry.characterOwner;
                } else if (entry.userDestiny != null) {
                    description += ' user ' + entry.userDestiny;
                }
            } else if (entry.userDestiny != null) {
                description += 'User ' + entry.userDestiny + ' recived ' + entry.quantity + ' ' + entry.object + 's';
            } else if (entry.characterDestiny != null) {
                description += 'Character ' + entry.characterDestiny + ' of user ' + entry.characterOwner + ' recived ' + entry.quantity + ' ' + entry.object + 's';
            }
            return description;
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-idlog="' + entry.idLog + '"]').length > 0) {
                    // set up the form button
                    $('ul.dropdown-menu a[data-idlog="' + entry.idLog + '"][data-option="log"]').click((event) => {
                        var id = $(event.target).attr('data-idlog');
                        this.selectedLog = null;
                        this.selectedLog = $(this.logs).filter(function(i,n) {
                            return n.idLog == id;
                        })[0];
                        $("#logModal").modal();
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
                    '<li><a data-idlog="' + entry.idLog + '" data-option="log">Download log</a></li>' +
                '</ul>' +
            '</div>';
        }
    },
    created: function() {
        this.loadLogs();
    }
}
</script>

<style lang="css">
#logList #upperBar {
    padding: 30px;
}
</style>

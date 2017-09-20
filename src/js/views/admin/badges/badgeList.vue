<template lang="html">
    <div class="content" id="badgeList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newBadge">
                <i class="fa fa-plus"></i> Register new badge
            </button>
        </div>
        <paca-admin-badge-form
            :badge="selectedBadge"
        >
        </paca-admin-badge-form>
        <paca-admin-badge-currency v-for="badge in badges"
            :badgeId="badge.idBadge"
            :badgeName="badge.badgeName"
            :badgeCurrency="badge.currency"
            :date="today"
        >
        </paca-admin-badge-currency>
        <vue-bootstrap-table
            :columns="columns"
            :values="badges"
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
var navigation = require('./../../../navigation.js');
var VueBootstrapTable  = require('vue-bootstrap-table');
export default {
    data() {
        return {
            columns: [
                {
                    name: "idBadge",
                    title: "ID"
                },
                {
                    name: "Image",
                    title: "Image",
                    renderfunction: this.renderImageColumn
                },
                {
                    name: "name",
                    title: "Name"
                },
                {
                    name: "description",
                    title: "Description"
                },
                {
                    name: "creationDate",
                    title: "Register date"
                    // renderfunction: this.renderOptionsColumn
                }
            ],
            badges: [],
            selectedBadge: null
        };
    },
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        today: function() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();

            if(dd<10) {
                dd = '0'+dd
            }

            if(mm<10) {
                mm = '0'+mm
            }

            return mm + '/' + dd + '/' + yyyy;
        }
    },
    methods: {
        loadBadges: function() {
            $.post('./php/controllers/badgecontroller.php', {
                action: 'getBadges'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.badges = json.badges;
                }
            });
        },
        newBadge: function() {
            this.selectedBadge = null;
            $('#badgeFormModal').modal();
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-idbadge="' + entry.idBadge + '"]').length > 0) {
                    $('ul.dropdown-menu a[data-idbadge="' + entry.idBadge + '"]').click((event) => {
                        var id = $(event.target).attr('data-idbadge');
                        this.selectedBadge = $(this.badges).filter(function(i,n) {
                            return n.idBadge == id;
                        })[0];
                        $("#badgeFormModal").modal();
                    })
                    clearTimeout(checker);
                }
            }, 100);
            var target = "#balanceModal" + entry.idBadge;
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a href="#" data-idbadge="' + entry.idBadge + '" data-option="profile">Edit badge</a></li>' +
                    '<li class="divider"></li>' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a href="#" data-toggle="modal" data-target="' + target + '">Currency</a></li>' +
                    '<li><a href="#">Profile</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a href="#">Currency</a></li>' +
                    '<li><a href="#">Badges</a></li>' +
                    '<li><a href="#">Badges</a></li>' +
                '</ul>' +
            '</div>';
        }
    },
    created: function() {
        this.loadBadges();
    }
}
</script>

<style lang="css">
#badgeList #upperBar {
    padding: 30px;
}
</style>

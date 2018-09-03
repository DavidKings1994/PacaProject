<template lang="html">
    <div class="content" id="badgeList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newBadge">
                <i class="fa fa-plus"></i> Register new badge
            </button>
        </div>
        <paca-admin-badge-form
            :badge="selectedBadge"
            v-on:saved="loadBadges">
        </paca-admin-badge-form>
        <paca-dialog
            :title="'Delete'"
            :message="'You really want to delete this badge?'"
            :onAccept="deleteBadge">
        </paca-dialog>
        <vue-bootstrap-table
            v-if="badges.length > 0"
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
var messageStore = require('./../../../messages.js');
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
                },
                {
                    title: "Options",
                    visible: true,
                    renderfunction: this.renderOptionsColumn
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
            this.badges = [];
            $.post('./php/controllers/badgeController.php', {
                action: 'getBadges'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.badges = json.badges;
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load badge list',
                        type: 'warning'
                    });
                }
            });
        },
        newBadge: function() {
            this.selectedBadge = null;
            $('#badgeFormModal').modal();
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.Image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-idbadge="' + entry.idBadge + '"]').length > 0) {
                    this.setUpSwall();
                    $('ul.dropdown-menu a[data-idbadge="' + entry.idBadge + '"][data-option="profile"]').click((event) => {
                        var id = $(event.target).attr('data-idbadge');
                        this.selectedBadge = $(this.badges).filter(function(i,n) {
                            return n.idBadge == id;
                        })[0];
                        $("#badgeFormModal").modal();
                    });
                    $('ul.dropdown-menu a[data-idbadge="' + entry.idBadge + '"][data-option="delete"]').click((event) => {
                        var id = $(event.target).attr('data-idbadge');
                        this.selectedBadge = $(this.badges).filter(function(i,n) {
                            return n.idBadge == id;
                        })[0];
                        $("#dialogModal").modal();
                    })
                    clearTimeout(checker);
                }
            }, 100);
            return '<div ' + (!$('#lateralNavbar').hasClass('compact') ? 'class="dropdown"' : '') + '>' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-idbadge="' + entry.idBadge + '" data-option="profile">Edit badge</a></li>' +
                    '<li><a data-idbadge="' + entry.idBadge + '" data-option="delete">Delete badge</a></li>' +
                '</ul>' +
            '</div>';
        },
        deleteBadge: function(callback) {
            $.post('./php/controllers/badgeController.php', {
                action: 'deleteBadge',
                badge: this.selectedBadge.idBadge
            }, (json) => {
                let response = JSON.parse(json);
                if (response.status == 'OK') {
                    this.loadBadges();
                    messageStore.commit('addMessage', {
                        text: 'Item ' + this.selectedBadge.name + ' deleted',
                        type: 'info'
                    });
                    callback();
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to delete item ' + this.selectedItem.name + '. ' + response.error,
                        type: 'error'
                    });
                    console.error(response.error);
                }
            });
        },
        setUpSwall: function() {
            let self = this;
            $('.table tr td:last-child div:not(.dropdown)').off('click');
            $('.table tr td:last-child div:not(.dropdown)').on('click', function() {
                let options = $(this).find('.dropdown-menu');
                let wrapper = document.createElement('div');
                wrapper.innerHTML = options.get(0).outerHTML;
                wrapper.className = 'swalDropDown';
                swal({
                    content: wrapper,
                    button: {
                        visible: false
                    }
                }).then(() => {
                    $('.swalDropDown').remove();
                });
                $('.swalDropDown').find('a').click((event) => {
                    swal.close();
                });
                $('.swalDropDown').find('a[data-option="profile"]').click((event) => {
                    var id = $(event.target).attr('data-idbadge');
                    self.selectedBadge = $(self.badges).filter(function(i,n) {
                        return n.idBadge == id;
                    })[0];
                    $("#badgeFormModal").modal();
                });
                $('.swalDropDown').find('a[data-option="delete"]').click((event) => {
                    var id = $(event.target).attr('data-idbadge');
                    self.selectedBadge = $(self.badges).filter(function(i,n) {
                        return n.idBadge == id;
                    })[0];
                    $("#dialogModal").modal();
                })
            });
        }
    },
    created: function() {
        this.loadBadges();
    },
    mounted: function() {
        $(window).resize(() =>{
            if ($(window).width() >= 780 && $(window).height() >= 480){
                $('.table tr td:last-child div').addClass('dropdown');
                $('.table tr td:last-child div').off('click');
            }
            if ($(window).width() <= 780 || $(window).height() <= 480){
                $('.table tr td:last-child div').removeClass('dropdown');
                this.setUpSwall();
            }
        });
    }
}
</script>

<style lang="css">
#badgeList #upperBar {
    padding: 30px;
}
</style>

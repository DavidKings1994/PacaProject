<template lang="html">
    <div class="content" id="characterList">
        <div class="row text-right" id="upperBar">
        </div>
        <paca-inventory-use
            :idCharacter="idCharacter"
            v-on:closed="resetInventory"
        >
        </paca-inventory-use>
        <paca-inventory
            :character="selectedCharacter"
            v-on:closed="resetInventory"
        >
        </paca-inventory>
        <paca-user-character-transaction
            :character="selectedCharacter"
            v-on:saved="loadCharacters"
        >
        </paca-user-character-transaction>
        <vue-bootstrap-table
            v-if="characters.length > 0"
            :columns="columns"
            :values="characters"
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
                    name: "idCharacter",
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
                    name: "type",
                    title: "Type"
                },
                {
                    name: "species",
                    title: "Species"
                },
                {
                    name: "traits",
                    title: "Traits",
                    renderfunction: this.renderTraitsColumn
                },
                // {
                //     name: "status",
                //     title: "Status",
                //     renderfunction: this.renderStatusColumn
                // },
                {
                    name: "options",
                    title: "Options",
                    visible: true,
                    renderfunction: this.renderOptionsColumn
                }
            ],
            characters: [],
            selectedCharacter: null,
            transaction: null
        };
    },
    props: ['id'],
    components: {
        VueBootstrapTable: VueBootstrapTable
    },
    computed: {
        idCharacter: function() {
            return (this.selectedCharacter == null ? null : this.selectedCharacter.idCharacter);
        },
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
        loadUserInfo: function() {
            return new Promise(resolve => {
                $.post('./php/controllers/userController.php', {
                    action: 'getProfile',
                    name: this.id
                }, (msg) => {
                    var json = JSON.parse(msg);
                    if (json.status == 'OK') {
                        this.profile = json.profile;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! User does\'t exist',
                            type: 'error'
                        });
                    }
                    resolve();
                });
            });
        },
        resetInventory: function() {
            this.selectedCharacter = null;
        },
        loadCharacters: function() {
            $.post('./php/controllers/userController.php', {
                action: 'getCharacters',
                id: this.profile.idUser
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.$set(this, 'characters', json.characters);
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to load user\'s characters',
                        type: 'error'
                    });
                }
            });
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        // renderStatusColumn: function (colname, entry) {
        //     return '<span class="label label-' + (entry.status == 'HOME' ? 'success' : 'danger') + '">' + entry.status + '</span>';
        // },
        renderRegisterColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.registered == 1 ? 'success' : 'danger') + '">' + (entry.registered == 1 ? 'YES' : 'NO') + '</span>';
        },
        renderTraitsColumn: function (colname, entry) {
            let render = '';
            if (entry.traits != null && entry.traits != '') {
                render = '<p>';
                let lines = entry.traits.split(/\n/);
                lines.forEach((line) => {
                    render += line + '<br>';
                });
                render += '</p>';
            }
            return render;
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                 // if the opttions menu is already rendered
                if ($('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"]').length > 0) {
                    this.setUpSwall();
                    // set up the inventory button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="inventory"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#inventoryModal").modal();
                    });
                    // set up the inventory use button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="useItem"]').click((event) => {
                        var id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#inventoryUseModal").modal();
                    });
                    // set up the transfer character button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="transferCharacter"]').click((event) => {
                        var id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#characterTransactionModal").modal();
                    });
                    clearTimeout(checker);
                }
            }, 100);
            return '<div ' + (!$('#lateralNavbar').hasClass('compact') ? 'class="dropdown"' : '') + '>' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="inventory">Inventory</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="useItem">Use item</a></li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="transferCharacter">Transfer character</a></li>' +
                '</ul>' +
            '</div>';
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
                // set up the inventory button
                $('.swalDropDown').find('a[data-option="inventory"]').click((event) => {
                    let id = $(event.target).attr('data-idcharacter');
                    self.selectedCharacter = null;
                    self.selectedCharacter = $(self.characters).filter(function(i,n) {
                        return n.idCharacter == id;
                    })[0];
                    $("#inventoryModal").modal();
                });
                // set up the inventory use button
                $('.swalDropDown').find('a[data-option="useItem"]').click((event) => {
                    var id = $(event.target).attr('data-idcharacter');
                    self.selectedCharacter = null;
                    self.selectedCharacter = $(self.characters).filter(function(i,n) {
                        return n.idCharacter == id;
                    })[0];
                    $("#inventoryUseModal").modal();
                });
                // set up the transfer character button
                $('.swalDropDown').find('a[data-option="transferCharacter"]').click((event) => {
                    var id = $(event.target).attr('data-idcharacter');
                    this.selectedCharacter = null;
                    this.selectedCharacter = $(this.characters).filter(function(i,n) {
                        return n.idCharacter == id;
                    })[0];
                    $("#characterTransactionModal").modal();
                });
            });
        }
    },
    created: function() {
        this.loadUserInfo().then(() => {
            this.loadCharacters();
        });
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

        window.eventBus.$on('updateCharacters', data => {
            this.loadCharacters();
        });
    }
}
</script>

<style lang="css">
#characterList #upperBar {
    padding: 30px;
}
</style>

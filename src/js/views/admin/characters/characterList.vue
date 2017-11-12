<template lang="html">
    <div class="content" id="characterList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newCharacter">
                <i class="fa fa-plus"></i> Register new character
            </button>
        </div>
        <paca-admin-character-form
            :character="selectedCharacter"
            v-on:saved="loadCharacters"
        >
        </paca-admin-character-form>
        <paca-inventory-transaction
            :idCharacter="idCharacter"
            :transaction="transaction"
            v-on:closed="resetInventory"
        >
        </paca-inventory-transaction>
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
        <vue-bootstrap-table
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
                    name: "status",
                    title: "Status",
                    renderfunction: this.renderStatusColumn
                },
                {
                    name: "ownerName",
                    title: "Owner"
                },
                {
                    name: "registered",
                    title: "Registered",
                    renderfunction: this.renderRegisterColumn
                },
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
        resetInventory: function() {
            this.selectedCharacter = null;
        },
        loadCharacters: function() {
            $.post('./php/controllers/characterController.php', {
                action: 'getCharacters'
            }, (msg) => {
                var json = JSON.parse(msg);
                if (json.status == 'OK') {
                    this.characters = json.characters;
                }
            });
        },
        newCharacter: function() {
            this.selectedCharacter = null;
            $('#characterFormModal').modal();
        },
        renderImageColumn: function (colname, entry) {
            return '<img src="' + entry.image + '" class="" alt="' + entry.name + '" width="50" height="50" />';
        },
        renderStatusColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.status == 'HOME' ? 'success' : 'danger') + '">' + (entry.status == 'HOME' ? 'Home' : 'Not home') + '</span>';
        },
        renderRegisterColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.registered == 1 ? 'success' : 'danger') + '">' + (entry.registered == 1 ? 'YES' : 'NO') + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                 // if the opttions menu is already rendered
                if ($('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"]').length > 0) {
                    // set up the form button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="profile"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#characterFormModal").modal();
                    });
                    // set up the inventory button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="inventory"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#inventoryModal").modal();
                    });
                    // set up the give-badge button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="giveBadge"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#inventoryTransactionModal").modal();
                        this.transaction = 'giveBadge';
                    });
                    // set up the give-item button
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"][data-option="giveItem"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        this.transaction = 'giveItem';
                        $("#inventoryTransactionModal").modal();
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
                    clearTimeout(checker);
                }
            }, 100);
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="profile">Edit character</a></li>' +
                    '<li class="divider"></li>' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="inventory">Inventory</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="useItem">Use item</a></li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="giveItem">Give item</a></li>' +
                    '<li><a data-idcharacter="' + entry.idCharacter + '" data-option="giveBadge">Give badge</a></li>' +
                '</ul>' +
            '</div>';
        }
    },
    created: function() {
        this.loadCharacters();
    }
}
</script>

<style lang="css">
#characterList #upperBar {
    padding: 30px;
}
</style>

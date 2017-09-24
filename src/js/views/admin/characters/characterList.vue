<template lang="html">
    <div class="content" id="characterList">
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newCharacter">
                <i class="fa fa-plus"></i> Register new character
            </button>
        </div>
        <paca-admin-character-form
            :character="selectedCharacter"
        >
        </paca-admin-character-form>
        <paca-admin-character-currency v-for="character in characters"
            :characterId="character.idCharacter"
            :characterName="character.characterName"
            :characterCurrency="character.currency"
            :date="today"
        >
        </paca-admin-character-currency>
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
                }
            ],
            characters: [],
            selectedCharacter: null
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
            return '<span class="label label-' + (entry.status == 'HOME' ? 'success' : 'danger') + '">' + entry.status + '</span>';
        },
        renderRegisterColumn: function (colname, entry) {
            return '<span class="label label-' + (entry.registered == 1 ? 'success' : 'danger') + '">' + (entry.registered == 1 ? 'YES' : 'NO') + '</span>';
        },
        renderOptionsColumn: function(colname, entry) {
            var checker = setTimeout(() => {
                if ($('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"]').length > 0) {
                    $('ul.dropdown-menu a[data-idcharacter="' + entry.idCharacter + '"]').click((event) => {
                        var id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#characterFormModal").modal();
                    })
                    clearTimeout(checker);
                }
            }, 100);
            var target = "#balanceModal" + entry.idCharacter;
            return '<div class="dropdown">' +
                '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' +
                    'Options' +
                    '<span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li><a href="#" data-idcharacter="' + entry.idCharacter + '" data-option="profile">Edit character</a></li>' +
                    '<li class="divider"></li>' +
                    '<li class="dropdown-header">Download images</li>' +
                    '<li><a href="#" data-toggle="modal" data-target="' + target + '">Currency</a></li>' +
                    '<li><a href="#">Profile</a></li>' +
                    '<li class="dropdown-header">Transactions</li>' +
                    '<li><a href="#">Currency</a></li>' +
                    '<li><a href="#">Characters</a></li>' +
                    '<li><a href="#">Characters</a></li>' +
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

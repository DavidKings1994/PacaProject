<template id="">
    <div class="container" id="characterList">
        <paca-character-card :selectedId="selectedCharacter" :characters="filteredCharacters" v-on:close="selectedCharacter = null">
        </paca-character-card>
        <div class="row text-right" id="upperBar">
            <button type="button" class="btn btn-success" v-on:click="newCharacter">
                <i class="fa fa-plus"></i> Register new character
            </button>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div style="padding-top: 10px; padding-bottom: 10px;">
                    <div class="input-group">
                        <input type="text" placeholder="Filter" class="form-control" v-model="filter">
                        <div class="input-group-addon">
                            <i class="glyphicon glyphicon-search"></i>
                        </div>
                    </div>
                </div>
            </div>
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
        <div class="row well">
            <transition-group name="slide-fade" tag="div">
                <div class="cardContainer" v-for="(character, index) in page"
                :id="character.idCharacter"
                v-on:click="selectedCharacter = index"
                v-bind:key="character.idCharacter">
                    <div class="characterCardSmall">
                        <span>{{ character.idCharacter + ': ' + character.name }}</span>
                        <img src="/assets/character_card_background_small.png" alt="background" class="character_card_background_small">
                        <div class="characterImage">
                            <img :src="character.image" alt="character image">
                        </div>
                        <img src="/assets/character_card_foreground_small.png" alt="foreground" class="character_card_foreground_small">
                    </div>
                </div>
            </transition-group>
        </div>
        <div class="col-sm-12">
            <div role="toolbar" aria-label="pagination bar" class="btn-toolbar">
                <div role="group" aria-label="first page" class="btn-group">
                    <button type="button" class="btn btn-default" @click="currentPage = 0">«</button>
                </div>
                <div class="btn-group" v-if="currentPage - 2 > 0">...</div>
                <div role="group" aria-label="pages" class="btn-group">
                    <button v-for="(p, index) in pageRange" type="button" class="btn btn-default"
                    :class="{active: p == currentPage}" @click="currentPage = p">{{ p + 1 }}</button>
                </div>
                <div class="btn-group" v-if="currentPage + 2 < pages">...</div>
                <div role="group" aria-label="last page" class="btn-group">
                    <button type="button" class="btn btn-default" @click="currentPage = pages">»</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    var navigation = require('./../../navigation.js');
    var messageStore = require('./../../messages.js');
    export default {
        data: function() {
            return {
                characters: [],
                filteredCharacters: [],
                filter: '',
                page: [],
                currentPage: 0,
                itemsPerPage: 30,
                selectedCharacter: null,
                transaction: null
            }
        },
        watch: {
            filter: function() {
                this.filteredCharacters = $.map(this.characters, (c, i) => {
                    if (this.filter.trim() != '') {
                        if ((c.name != null ? c.name.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.description != null ? c.description.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.idCharacter != null ? c.idCharacter.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.ownerName != null ? c.ownerName.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)) {
                            return c;
                        }
                    } else {
                        return c;
                    }
                });
                this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
            },
            currentPage: function() {
                this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
            }
        },
        computed: {
            idCharacter: function() {
                return (this.selectedCharacter == null ? null : this.selectedCharacter.idCharacter);
            },
            pages: function() {
                return Math.floor(this.filteredCharacters.length / this.itemsPerPage);
            },
            pageRange: function() {
                let p = [];
                let min = 0;
                let max = 0;
                for (var i = 1; i < 6; i++) {
                    if(max - min < 5) {
                        min = (this.currentPage - i < 0 ? 0 : this.currentPage - i);
                        max = (this.currentPage + i + 1 > this.pages ? this.pages : this.currentPage + i + 1);
                    }
                }
                for (var i = min; i < max; i++) {
                    p.push(i)
                }
                return p;
            }
        },
        methods: {
            resetInventory: function() {
                this.selectedCharacter = null;
            },
            loadCharacters: function() {
                this.characters = [];
                $.post('./php/controllers/characterController.php', {
                    action: 'getCharacters'
                }, (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status == 'OK') {
                        this.characters = json.characters;
                        this.filteredCharacters = this.characters;
                        this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load character list.',
                            type: 'error'
                        });
                    }
                });
            },
            newCharacter: function() {
                this.selectedCharacter = null;
                $('#characterFormModal').modal();
            },
            deleteCharacter: function(id) {
                $.post('./php/controllers/characterController.php', {
                    action: 'deleteCharacter',
                    id: id
                }, (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status == 'OK') {
                        this.loadCharacters();
                        messageStore.commit('addMessage', {
                            text: 'Bye! character deleted.',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to delete character.',
                            type: json.error
                        });
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
                        let id = $(event.target).attr('data-idcharacter');
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#characterFormModal").modal();
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
                    // set up the give-badge button
                    $('.swalDropDown').find('a[data-option="giveBadge"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        $("#inventoryTransactionModal").modal();
                        self.transaction = 'giveBadge';
                    });
                    // set up the give-item button
                    $('.swalDropDown').find('a[data-option="giveItem"]').click((event) => {
                        let id = $(event.target).attr('data-idcharacter');
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        self.transaction = 'giveItem';
                        $("#inventoryTransactionModal").modal();
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
                    // set up the delete button
                    $('.swalDropDown').find('a[data-option="deleteCharacter"]').click((event) => {
                        var id = $(event.target).attr('data-idcharacter');
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == id;
                        })[0];
                        swal({
                            title: "Delete character",
                            text: "Once deleted, you will not be able to recover this character",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                this.deleteCharacter(this.selectedCharacter.idCharacter);
                            }
                        });
                    });
                });
            }
        },
        created: function() {
            this.loadCharacters();
        }
    }
</script>

<style media="screen">
    #characterList #upperBar {
        padding: 30px;
    }

    .cardContainer {
        float:left;
        position:relative;
    }

    .characterCardSmall {
        height: 100px;
        border-top: solid black 20px;
        width: min-content;
        padding: 0;
        margin-bottom: 35px;
        margin-right: 2vw;
    }

    .characterCardSmall span {
        position: absolute;
        color: white;
        top: 0;
    }

    .characterCardSmall .character_card_background_small {
        display: block;
        width: 140px;
    }

    .characterCardSmall .characterImage {
        height: 80px;
        width: 140px;
        position: absolute;
        overflow: hidden;
        top: 20px;
        text-align: center;
    }

    .characterCardSmall .characterImage img {
        height: 80%;
        margin-top: 10px;
    }

    .characterCardSmall .character_card_foreground_small {
        position: absolute;
        top: 20px;
        width: 140px;
    }

    .slide-fade-move {
        transition: transform 1s ease;
    }
    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .3s ease;
    }
    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }

</style>

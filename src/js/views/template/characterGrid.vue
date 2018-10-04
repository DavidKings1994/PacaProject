<template id="">
    <div class="container-fluid" id="characterList">
        <paca-character-card
            :selectedId="selectedIndex"
            :characters="filteredCharacters"
            v-on:close="selectedIndex = null"
            v-on:indexChange="changeselectedIndex">
        </paca-character-card>
        <button id="editCharacterButton" type="button" class="btn btn-primary" v-on:click="setUpSwall()" v-if="selectedIndex != null">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="row text-right" id="upperBar" v-if="rol == 'admin'">
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
        <paca-user-character-transaction
            :character="selectedCharacter"
            v-on:saved="loadCharacters"
        >
        </paca-user-character-transaction>
        <paca-admin-character-form
            :character="selectedCharacter"
            v-on:saved="loadCharacters"
            v-on:closed="resetInventory"
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
                v-on:click="selectIndex(currentPage * itemsPerPage + index)"
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
                bufferIndex: null,
                selectedIndex: null,
                transaction: null,
                profile: null
            }
        },
        props: ['id'],
        watch: {
            filter: function() {
                this.filteredCharacters = $.map(this.characters, (c, i) => {
                    if (this.filter.trim() != '') {
                        if ((c.name != null ? c.name.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.description != null ? c.description.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.idCharacter != null ? c.idCharacter.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.traits != null ? c.traits.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)
                        || (c.ownerName != null ? c.ownerName.toLowerCase().includes(this.filter.toLowerCase().trim()) : false)) {
                            return c;
                        }
                    } else {
                        return c;
                    }
                });
                this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
                this.currentPage = 0;
            },
            currentPage: function() {
                this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
            }
        },
        computed: {
            rol: function() {
                return navigation.state.session.rol;
            },
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
                        max = (this.currentPage + i + 1 > this.pages ? this.pages + 1 : this.currentPage + i + 1);
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
            changeselectedIndex: function(newindex) {
                this.bufferIndex = newindex;
            },
            selectIndex: function(index) {
                this.selectedIndex = this.bufferIndex = index;
            },
            loadUserInfo: function() {
                return new Promise(resolve => {
                    $.post('./php/controllers/userController.php', {
                        action: 'getProfile',
                        name: this.id
                    }, (msg) => {
                        var json = JSON.parse(msg);
                        if (json.status == 'OK') {
                            this.$set(this, 'profile', json.profile);
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
            loadCharacters: function() {
                this.characters = [];
                if (this.rol == 'admin') {
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
                } else {
                    this.loadUserInfo().then(() => {
                        $.post('./php/controllers/userController.php', {
                            action: 'getCharacters',
                            id: this.profile.idUser
                        }, (msg) => {
                            var json = JSON.parse(msg);
                            if (json.status == 'OK') {
                                this.characters = json.characters;
                                this.filteredCharacters = this.characters;
                                this.page = this.filteredCharacters.slice(this.currentPage * this.itemsPerPage, (this.currentPage * this.itemsPerPage) + this.itemsPerPage);
                            } else {
                                messageStore.commit('addMessage', {
                                    text: 'Unable to load user\'s characters',
                                    type: 'error'
                                });
                            }
                        });
                    });
                }
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
                let _idCharacter = this.characters[this.bufferIndex].idCharacter;
                let self = this;
                let wrapper = document.createElement('div');
                if (this.rol == 'admin') {
                    wrapper.innerHTML = '<ul class="dropdown-menu">' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="profile">Edit character</a></li>' +
                            '<li class="divider"></li>' +
                            '<li class="dropdown-header">Download images</li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="inventory">Inventory</a></li>' +
                            '<li class="dropdown-header">Transactions</li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="useItem">Use item</a></li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="giveItem">Give item</a></li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="giveBadge">Give badge</a></li>' +
                            '<li class="divider"></li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="deleteCharacter">Delete character</a></li>' +
                        '</ul>';
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
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#characterFormModal").modal();
                    });
                    // set up the inventory button
                    $('.swalDropDown').find('a[data-option="inventory"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#inventoryModal").modal();
                    });
                    // set up the give-badge button
                    $('.swalDropDown').find('a[data-option="giveBadge"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#inventoryTransactionModal").modal();
                        self.transaction = 'giveBadge';
                    });
                    // set up the give-item button
                    $('.swalDropDown').find('a[data-option="giveItem"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        self.transaction = 'giveItem';
                        $("#inventoryTransactionModal").modal();
                    });
                    // set up the inventory use button
                    $('.swalDropDown').find('a[data-option="useItem"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#inventoryUseModal").modal();
                    });
                    // set up the delete button
                    $('.swalDropDown').find('a[data-option="deleteCharacter"]').click((event) => {
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
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
                } else {
                    wrapper.innerHTML = '<ul class="dropdown-menu">' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="profile">Edit character</a></li>' +
                            '<li class="divider"></li>' +
                            '<li class="dropdown-header">Download images</li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="inventory">Inventory</a></li>' +
                            '<li class="dropdown-header">Transactions</li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="useItem">Use item</a></li>' +
                            '<li><a data-idcharacter="' + _idCharacter + '" data-option="transferCharacter">Transfer character</a></li>' +
                        '</ul>';
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
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#characterFormModal").modal();
                    });
                    // set up the inventory button
                    $('.swalDropDown').find('a[data-option="inventory"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#inventoryModal").modal();
                    });
                    // set up the inventory use button
                    $('.swalDropDown').find('a[data-option="useItem"]').click((event) => {
                        self.selectedCharacter = null;
                        self.selectedCharacter = $(self.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#inventoryUseModal").modal();
                    });
                    // set up the transfer character button
                    $('.swalDropDown').find('a[data-option="transferCharacter"]').click((event) => {
                        this.selectedCharacter = null;
                        this.selectedCharacter = $(this.characters).filter(function(i,n) {
                            return n.idCharacter == _idCharacter;
                        })[0];
                        $("#characterTransactionModal").modal();
                    });
                }
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

    #editCharacterButton {
        position: fixed;
        bottom: 4vh;
        right: 50vw;
        transform: translate(50%);
        z-index: 1002;
    }

</style>

<template lang="html">
    <div id="inventoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory</h4>
                </div>
                <div class="modal-body">
                    <div :class="'logFrame ' + (this.character != null ? 'characterInv' : 'userInv')">
                        <div id="title">
                            {{ inventoryName }}
                        </div>
                        <div id="itemsFrame" v-if="inventory.length > 0">
                            <div
                                v-for="item in inventory"
                                :style="'background: url(' + item.image + ');'">
                                <span v-if="item.total != null">x{{ item.total }}</span>
                            </div>
                        </div>
                        <div v-else id="emptyMessage">
                            Empty
                        </div>
                        <div id="badgesFrame" v-if="this.character != null">
                            <div v-for="badge in badges" :style="'background: url(' + badge.image + ');'"></div>
                        </div>
                        <div class="logFooter">
                            <span>{{ today }}</span>
                            <span v-if="this.character != null">Owner: {{ this.character.ownerName }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" v-on:click="close">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Save image</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var domtoimage = require('dom-to-image');
var filesaver = require('file-saver');
var messageStore = require('./../../messages.js');
export default {
    data: function() {
        return {
            inventory: [],
            badges: []
        }
    },
    props: ['user', 'character'],
    watch: {
        user: function() {
            this.loadInventory();
        },
        character: function() {
            this.loadInventory();
            this.loadBadges();
        }
    },
    computed: {
        inventoryName: function() {
            return (this.user == null ? (this.character == null ? '' : this.character.name) : this.user.userName);
        },
        today: function() {
            let d = new Date();
            let dformat = [
                d.getFullYear(),
                d.getMonth()+1,
                d.getDate()
            ].join('-')+' '+ [
                d.getHours(),
                d.getMinutes(),
                d.getSeconds()
            ].join(':');
            return dformat;
        }
    },
    methods: {
        close: function() {
            this.$emit('closed');
        },
        save: function() {
            domtoimage.toBlob($('#inventoryModal .logFrame')[0], {
                height: 600,
                width: 800
            })
            .then((blob) => {
                filesaver.saveAs(blob, this.inventoryName + '\'s inventory.png');
                $('#inventoryModal .btn-danger').click();
                this.close();
            })
            .catch(function (error) {
                messageStore.commit('addMessage', {
                    text: 'Sorry, an error occurred while creating the image. Error: ' + error,
                    type: 'error'
                });
                console.error('oops, something went wrong!', error);
            });
        },
        loadBadges: function() {
            if (this.character != null) {
                $.post('./php/controllers/characterController.php', {
                    action: 'getBadges',
                    id: this.character.idCharacter
                }, (json) => {
                    let response = JSON.parse(json);
                    if (response.status == 'OK') {
                        this.badges = response.badges;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load character\'s badges. ' + response.error,
                            type: 'error'
                        });
                        console.error(response.error);
                    }
                });
            }
        },
        loadInventory: function() {
            if (this.user != null) {
                $.post('./php/controllers/userController.php', {
                    action: 'getInventoryCount',
                    id: this.user.idUser
                }, (json) => {
                    var inventory = JSON.parse(json);
                    if (inventory.status == 'OK') {
                        this.inventory = inventory.items;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load users\'s inventory. ' + response.error,
                            type: 'error'
                        });
                        console.error(inventory.error);
                    }
                });
            } else if (this.character != null) {
                $.post('./php/controllers/characterController.php', {
                    action: 'getInventory',
                    id: this.character.idCharacter
                }, (json) => {
                    var inventory = JSON.parse(json);
                    if (inventory.status == 'OK') {
                        this.inventory = inventory.items;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Unable to load character\'s inventory. ' + response.error,
                            type: 'error'
                        });
                        console.error(inventory.error);
                    }
                });
            } else {
                this.inventory = [];
            }
        }
    }
}
</script>

<style lang="css">
</style>

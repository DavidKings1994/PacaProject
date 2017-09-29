<template lang="html">
    <div id="inventoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ inventoryName }}'s inventory</h4>
                </div>
                <div class="modal-body">
                    <div class="inventoryFrame">
                        <img v-for="item in inventory" :src="item.image" :alt="item.name" height="50" width="50">
                        <h4 v-if="inventory.length == 0">Inventory is empty</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">Save image</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
var domtoimage = require('dom-to-image');
export default {
    data: function() {
        return {
            inventory: []
        }
    },
    props: ['user', 'character'],
    watch: {
        user: function() {
            this.loadInventory();
        },
        character: function() {
            this.loadInventory();
        }
    },
    computed: {
        inventoryName: function() {
            return (this.user == null ? (this.character == null ? '' : this.character.name) : this.user.name);
        }
    },
    methods: {
        save: function() {
            domtoimage.toPng($('#inventoryModal .inventoryFrame')[0])
            .then((dataUrl) => {
                var link = document.createElement('a');
                link.download = this.inventoryName + '\'s inventory ';
                link.href = dataUrl;
                link.click();
                $('#inventoryModal .btn-danger').click();
            })
            .catch(function (error) {
                console.error('oops, something went wrong!', error);
            });
        },
        loadInventory: function() {
            if (this.user != null) {
                $.post('./php/controllers/userController.php', {
                    action: 'getInventory',
                    id: this.user.idUser
                }, (json) => {
                    var inventory = JSON.parse(json);
                    if (inventory.status == 'OK') {
                        this.inventory = inventory.items;
                    } else {
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

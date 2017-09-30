<template lang="html">
    <div id="itemFormModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Item information</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" name="action" :value="action">
                        <input type="hidden" name="item" :value="this.itemId">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter item name" :value="this.itemName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="desc">Description:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="desc" id="desc" placeholder="Enter item description" :value="this.itemDesc">
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="image">Image:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="image" id="image" placeholder="Enter item image link" :value="this.itemImage">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">{{ buttonText }}</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    props: ['item'],
    computed: {
        itemId: function() { return this.item == null ? '' : this.item.idItem; },
        itemName: function() { return this.item == null ? '' : this.item.name; },
        itemDesc: function() { return this.item == null ? '' : this.item.description; },
        itemImage: function() { return this.item == null ? '' : this.item.Image; },
        buttonText: function() { return this.item == null ? 'Register' : 'Save'; },
        action: function() { return this.item == null ? 'registerItem' : 'updateItem'; }
    },
    methods: {
        save: function() {
            $.post('./php/controllers/itemController.php',
            $("#itemFormModal form").serialize(),
            (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.$emit('saved');
                    $('#itemFormModal .btn-danger').click();
                } else {
                    console.error(result.error);
                }
            });
        }
    }
}
</script>

<style lang="css">
</style>

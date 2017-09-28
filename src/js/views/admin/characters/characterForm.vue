<template lang="html">
    <div id="characterFormModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Item information</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" name="action" :value="action">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="id">Registry number:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="id" id="id" placeholder="Enter character registry number" :value="this.characterName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter character name" :value="this.characterName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="desc">Description:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="desc" id="desc" placeholder="Enter character description" :value="this.characterDesc">
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="image">Image:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="image" id="image" placeholder="Enter character image link" :value="this.characterImage">
                            </div>
                        </div>
                        <div class="form-group" v-if="this.character != null">
                            <label class="control-label col-sm-2" for="status">Location:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="HOME">Home</option>
                                    <option value="NOT_HOME">Not in home</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="owner">Owner:</label>
                            <div class="col-sm-10">
                                <v-select :value.sync="selected" :options="options" class="form-control" name="owner" id="owner">
                                </v-select>
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
var vSelect  = require('vue-select');
export default {
    data: function() {
        return {
            selected: null,
            options: []
        };
    }
    components: {
        vSelect: vSelect
    },
    props: ['character'],
    computed: {
        characterName: function() { return this.character == null ? '' : this.character.name; },
        characterDesc: function() { return this.character == null ? '' : this.character.description; },
        characterImage: function() { return this.character == null ? '' : this.character.Image; },
        buttonText: function() { return this.character == null ? 'Register' : 'Save'; },
        action: function() { return this.character == null ? 'registerItem' : 'updateItem'; }
    },
    methods: {
        save: function() {
            $.post('./php/controllers/characterController.php',
            $("#characterFormModal form").serialize(),
            (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.$emit('saved');
                    $('#characterFormModal .btn-danger').click();
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

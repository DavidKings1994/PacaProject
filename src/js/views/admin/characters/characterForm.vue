<template lang="html">
    <div id="characterFormModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Character information</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" name="action" :value="action">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="id">Registry number:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="id" id="id" placeholder="Enter character registry number" :value="this.characterId">
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
                                    <option value="NOT_HOME">Not home</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="owner">Owner:</label>
                            <div class="col-sm-10">
                                <v-select
                                    v-model="selected"
                                    :debounce="250"
                                    :options="users"
                                    :on-search="getUsers"
                                    :clearSearchOnSelect="true"
                                    placeholder="Search owner name">
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
import vSelect from 'vue-select';
export default {
    data: function() {
        return {
            selected: null,
            users: []
        };
    },
    components: {
        'v-select': vSelect
    },
    props: ['character'],
    watch: {
        character: function() {
            if (this.character != null) {
                $("#characterFormModal select[name='status']").val(this.character.status);
                this.selected = this.character.ownerName;
            } else {
                this.selected = null;
            }
        }
    },
    computed: {
        characterId: function() { return this.character == null ? '' : this.character.idCharacter; },
        characterName: function() { return this.character == null ? '' : this.character.name; },
        characterDesc: function() { return this.character == null ? '' : this.character.description; },
        characterImage: function() { return this.character == null ? '' : this.character.image; },
        buttonText: function() { return this.character == null ? 'Register' : 'Save'; },
        action: function() { return this.character == null ? 'registerCharacter' : 'updateCharacter'; }
    },
    methods: {
        getUsers: function(search, loading) {
            loading(true);
            $.post('./php/controllers/userController.php', {
                action: 'search',
                name: search
            }, (json) => {
                var result = JSON.parse(json);
                this.users = result.users;
                loading(false);
            });
        },
        save: function() {
            console.log(this.selected);
            $.post('./php/controllers/characterController.php',
            $("#characterFormModal form").serialize() + '&owner=' + encodeURIComponent(this.selected != null ? this.selected.label : ''),
            (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.$emit('saved');
                    $('#characterFormModal .btn-danger').click();
                    this.selected = null;
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

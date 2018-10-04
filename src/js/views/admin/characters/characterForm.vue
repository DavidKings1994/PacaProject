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
                        <div class="form-group" v-if="rol == 'admin'">
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
                        <div class="form-group" v-if="rol == 'admin'">
                            <label class="control-label col-sm-2" for="id">Registry number:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="id" id="id" placeholder="Enter character registry number" v-model="editingCharacter.id">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter character name" v-model="editingCharacter.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="desc">Description:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="desc" id="desc" placeholder="Enter character description" v-model="editingCharacter.description">
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group" v-if="rol == 'admin'">
                            <label class="control-label col-sm-2" for="image">Image:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="image" id="image" placeholder="Enter character image link" v-model="editingCharacter.image">
                            </div>
                        </div>
                        <div class="form-group" v-if="rol == 'admin'">
                            <label class="control-label col-sm-2" for="type">Type:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="type" name="type">
                                    <option value="city">City</option>
                                    <option value="wild">Wild</option>
                                    <option value="neutral">Neutral</option>
                                    <option value="void">Void</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-if="rol == 'admin'">
                            <label class="control-label col-sm-2" for="species">Species:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="species" name="species">
                                    <option value="louxe">Louxe</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" v-if="rol == 'admin'">
                            <label class="control-label col-sm-2" for="traits">Traits:</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" name="traits" id="traits"
                                placeholder="Enter character's traits" v-model="editingCharacter.traits" maxlength="800">
                                </textarea>
                            </div>
                        </div>
                        <!-- <div class="form-group" v-if="this.character != null">
                            <label class="control-label col-sm-2" for="class">Class:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="class" name="class">
                                    <option value="HOME">Home</option>
                                    <option value="NOT_HOME">Not home</option>
                                </select>
                            </div>
                        </div> -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"  v-on:click="close">Close</button>
                    <button type="button" class="btn btn-success" v-on:click="save">{{ buttonText }}</button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select';
var messageStore = require('./../../../messages.js');
var navigation = require('./../../../navigation.js');
export default {
    data: function() {
        return {
            selected: null,
            users: [],
            editingCharacter: {
                id: '',
                name: '',
                description: '',
                image: '',
                traits: ''
            }
        };
    },
    components: {
        'v-select': vSelect
    },
    props: ['character'],
    watch: {
        character: function() {
            if (this.character != null) {
                if (this.rol == 'admin') {
                    $("#characterFormModal select[name='class']").val(this.character.clas);
                    $("#characterFormModal select[name='type']").val(this.character.type);
                    $("#characterFormModal select[name='species']").val(this.character.species);
                    this.selected = {
                        value: this.character.idCharacter,
                        label: this.character.ownerName
                    };
                }
                this.editingCharacter.id = this.character.idCharacter;
                this.editingCharacter.name = this.character.name;
                this.editingCharacter.description = this.character.description;
                this.editingCharacter.image = this.character.image;
                this.editingCharacter.traits = this.character.traits;
            } else {
                this.selected = null;
            }
        }
    },
    computed: {
        logged: function() { return navigation.state.session != null; },
        rol: function() { return this.logged ? navigation.state.session.rol : null; },
        userName: function() { return this.logged ? navigation.state.session.name : null; },
        buttonText: function() { return this.character == null ? 'Register' : 'Save'; },
        action: function() { return this.character == null ? 'registerCharacter' : 'updateCharacter'; }
    },
    methods: {
        close: function() {
            this.selected = null;
            this.editingCharacter = {
                id: '',
                name: '',
                description: '',
                image: '',
                traits: ''
            };
            $('#characterFormModal form input').val('');
            $('#characterFormModal .btn-danger').click();
            this.$emit('closed');
        },
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
            let data;
            if (this.rol == 'admin') {
                data = $("#characterFormModal form").serialize() + '&owner=' +
                encodeURIComponent(this.selected != null ? this.selected.label : '');
            } else {
                data = {
                    action: 'updateCharacter',
                    id: this.editingCharacter.id,
                    image: this.editingCharacter.image,
                    name: $('#characterFormModal input#name').val(),
                    desc: $('#characterFormModal textarea#desc').val(),
                    type: this.editingCharacter.type,
                    species: this.editingCharacter.species,
                    traits: this.editingCharacter.traits,
                    owner: this.userName,
                    class: this.editingCharacter.class
                };
            }
            $.post('./php/controllers/characterController.php', data,
            (json) => {
                var result = JSON.parse(json);
                if (result.status == 'OK') {
                    this.$emit('saved');
                    this.close();
                    messageStore.commit('addMessage', {
                        text: 'Character ' + (this.character == null ? 'registered' : 'updated'),
                        type: 'success'
                    });
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Unable to save character\'s information. ' + result.error,
                        type: 'error'
                    });
                }
            });
        }
    }
}
</script>

<style lang="css">
</style>
